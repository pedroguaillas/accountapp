<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Person;
use App\Models\MovementType;
use App\Models\BankAccount;
use App\Models\Company;
use App\Http\Requests\TransactionStoreRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::first();
        $search = $request->input('search', ''); // Si no hay búsqueda, usa un valor vacío

        // Construimos la consulta base
        $transactions = Transaction::query()
            ->select('transactions.*', 'banks.name', 'bank_accounts.account_number')
            ->join('bank_accounts', 'bank_accounts.id', '=', 'transactions.bank_account_id')
            ->join('banks', 'banks.id', '=', 'bank_accounts.bank_id')
            ->where('bank_accounts.data_additional->company_id', $company->id)
            ->when($search, function ($query, $search) {
                $query->whereRaw("LOWER(name) LIKE ?", ["%" . strtolower($search) . "%"]);
            })->paginate(10)
            ->withQueryString(); // Puedes seleccionar lo que necesites

        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Transaction/Index', [
            'filters' => [
                'search' => $search,
            ],
            'transactions' => $transactions,
        ]);
    }

    public function create()
    {
        $company = Company::first();

        // Obtenemos las cuentas bancarias de la compañía
        $bankAccounts = BankAccount::join('banks', 'banks.id', '=', 'bank_accounts.bank_id')
            ->where('bank_accounts.data_additional->company_id', $company->id)
            ->select('bank_accounts.id', 'bank_accounts.account_number', 'banks.name') // Puedes seleccionar lo que necesites
            ->get();

        // Obtenemos los tipos de movimiento de la compañía
        $movementtypes = MovementType::where('company_id', $company->id)
            ->where(function ($query) {
                $query->where('venue', 'ambos')
                    ->orWhere('venue', 'bancos');
            })
            ->get();

        $peopleCount = Person::count();
        $people = [];
        $people = Person::where('company_id', $company->id)->get();
       
        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Transaction/Create', [
            'bankaccounts' => $bankAccounts,
            'movementtypes' => $movementtypes,
            //NOTA MAYORES A 500 VER COMO PAGINAR
            'people' => $people,
            'countperson' => $peopleCount,
        ]);
    }

    public function store(TransactionStoreRequest $transactionStoreRequest)
    {
        // Llamada a la compañía
        $company = Company::first();
        $data = [
            "company_id" => $company->id,
        ];

        //creacion de los estableicmientos 
        Transaction::create([...$transactionStoreRequest->all(), 'data_additional' => $data]);
        $type = MovementType::where('id', $transactionStoreRequest->movement_type_id)->value('type');

        // Actualizar el saldo de la cuenta bancaria restando el monto de la transacción
        if ($type === 'Ingreso') {
            BankAccount::where('id', $transactionStoreRequest->bank_account_id)
                ->increment('current_balance', $transactionStoreRequest->amount);
        } else {
            BankAccount::where('id', $transactionStoreRequest->bank_account_id)
                ->decrement('current_balance', abs($transactionStoreRequest->amount));
        }
    }

    public function edit(int $transactionId)
    {
        $company = Company::first();
        $transaction = Transaction::join('bank_accounts', 'bank_accounts.id', '=', 'transactions.bank_account_id')
            ->join('banks', 'banks.id', '=', 'bank_accounts.bank_id')
            ->join('movement_types', 'movement_types.id', '=', 'transactions.movement_type_id')
            ->join('people', 'people.id', '=', 'transactions.beneficiary_id')
            ->where('transactions.id', $transactionId)
            ->select(
                'transactions.id',
                'transactions.movement_type_id',
                'transactions.bank_account_id',
                DB::raw("to_char(transactions.transaction_date, 'YYYY-MM-DD') as transaction_date"),
                'transactions.amount',
                'transactions.description',
                'transactions.payment_method',
                'transactions.beneficiary_id',
                DB::raw("to_char(transactions.cheque_date, 'YYYY-MM-DD') as cheque_date"),
                'transactions.transfer_account',
                'transactions.voucher_number',
                'transactions.state_transaction',
                'banks.name',
                'bank_accounts.account_number',
                'movement_types.type'
            )
            ->first();

        // Obtenemos las cuentas bancarias de la compañía
        $bankAccounts = BankAccount::join('banks', 'banks.id', '=', 'bank_accounts.bank_id')
            ->where('bank_accounts.data_additional->company_id', $company->id)
            ->select('bank_accounts.id', 'bank_accounts.account_number', 'banks.name') // Puedes seleccionar lo que necesites
            ->get();

        // Obtenemos los tipos de movimiento de la compañía
        $movementtypes = MovementType::where('company_id', $company->id)
        ->where(function ($query) {
            $query->where('venue', 'ambos')
                ->orWhere('venue', 'bancos');
        })
        ->get();

        $people = Person::where('company_id', $company->id)->get();
        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Transaction/Edit', [
            'transaction' => $transaction,
            'bankaccounts' => $bankAccounts,
            'movementtypes' => $movementtypes,
            //NOTA MAYORES A 500 VER COMO PAGINAR
            'people' => $people,
        ]);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $initialAmount = $transaction->amount;
        $diferent = abs((float) $initialAmount - (float) $request->amount); // Convierte ambos a float
        $type = MovementType::where('id', $transaction->movement_type_id)->value('type');
        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'transaction_date' => 'required|date',
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($diferent, $request, $type, $initialAmount) {
                    // Obtener la cuenta bancaria usando el ID del request
        
                    $bankAccount = BankAccount::find($request->bank_account_id);
                    // Validar si el monto supera el saldo disponible
                    if ($type === "Egreso" && $initialAmount < $value && $bankAccount && $diferent > $bankAccount->current_balance) {
                        $fail("El monto no puede ser mayor que el saldo disponible: " . ($bankAccount->current_balance + $initialAmount));
                    }
                    if ($type === "Ingreso" && $initialAmount > $value && $bankAccount && $diferent > $bankAccount->current_balance) {
                        $fail("El monto no puede ser menor al saldo de: " . ($initialAmount - $bankAccount->current_balance));
                    }
                },
            ],
        ]);

        //actulizar los establecimientos
        $transaction->update($request->all());

        // Actualizar el saldo de la cuenta bancaria restando el monto de la transacción
        $operation = ($type === 'Ingreso')
            ? ($initialAmount < $request->amount ? 'increment' : 'decrement')
            : ($initialAmount > $request->amount ? 'increment' : 'decrement');

        BankAccount::where('id', $transaction->bank_account_id)->$operation('current_balance', $diferent);
    }

    public function destroy(Transaction $transaction)
    {
        $initialAmount = $transaction->amount;
        $type = MovementType::where('id', $transaction->movement_type_id)->value('type');

        // Obtener la cuenta bancaria
        $bankAccount = BankAccount::find($transaction->bank_account_id);

        if (!$bankAccount) {
            return response()->json(['error' => 'Cuenta bancaria no encontrada'], 404);
        }

        if ($type === "Ingreso") {
            // Verificar que el saldo disponible sea mayor o igual al monto
            if ($bankAccount->current_balance < $initialAmount) {
                return response()->json([
                    'error' => 'No se puede eliminar porque el saldo disponible es menor que la transacción'
                ], 400);
            }

            // Restar el monto del saldo actual
            $bankAccount->decrement('current_balance', $initialAmount);
        } elseif ($type === "Egreso") {
            // Reintegrar el monto al saldo disponible
            $bankAccount->increment('current_balance', $initialAmount);
        }

        // Eliminar la transacción
        $transaction->delete();

        return response()->json(['success' => 'Transacción eliminada correctamente']);
    }
}