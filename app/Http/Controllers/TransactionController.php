<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Person;
use App\Models\MovementType;
use App\Models\BankAccount;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::first();

        // Construimos la consulta base
        $transactions = Transaction::join('bank_accounts', 'bank_accounts.id', '=', 'transactions.bank_account_id')
            ->join('banks', 'banks.id', '=', 'bank_accounts.bank_id')
            ->where('bank_accounts.company_id', $company->id)
            ->select('transactions.*', 'banks.name', 'bank_accounts.account_number'); // Puedes seleccionar lo que necesites

        // Aplicamos el filtro si existe
        $search = $request->input('search', ''); // Si no hay búsqueda, usa un valor vacío
        $transactions->whereRaw("LOWER(banks.name) LIKE ?", ["%" . strtolower($search) . "%"]);


        // Paginamos los resultados y preservamos los filtros en la URL
        $transactions = $transactions->paginate(10)->withQueryString();

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
            ->where('bank_accounts.company_id', $company->id)
            ->select('bank_accounts.id', 'bank_accounts.account_number', 'banks.name') // Puedes seleccionar lo que necesites
            ->get();

        // Obtenemos los tipos de movimiento de la compañía
        $movementtypes = MovementType::where('company_id', $company->id)->get();

        $people = Person::where('company_id', $company->id)->get();
        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Transaction/Create', [
            'bankaccounts' => $bankAccounts,
            'movementtypes' => $movementtypes,
            'people' => $people,
        ]);
    }

    public function store(Request $request)
    {
        // Validación de datos
        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'transaction_date' => 'required|date',
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) use ($request) {
                    // Buscar la cuenta bancaria en la base de datos
                    $bankAccount = DB::table('bank_accounts')
                        ->where('id', $request->bank_account_id)
                        ->first();

                    // Validar si el monto supera el saldo disponible
                    if ($bankAccount && $value > $bankAccount->current_balance) {
                        $fail("El monto no puede ser mayor que el saldo disponible: {$bankAccount->current_balance}");
                    }
                },
            ],
        ]);

        // Llamada a la compañía
        $company = Company::first();

        // Crear la transacción
        $company->transactions()->create($request->all());

        // Actualizar el saldo de la cuenta bancaria restando el monto de la transacción
        BankAccount::where('id', $request->bank_account_id)
            ->decrement('current_balance', $request->amount);

    }

    public function edit(int $transactionId)
    {
        $company = Company::first();
        $transaction = Transaction::join('bank_accounts', 'bank_accounts.id', '=', 'transactions.bank_account_id')
            ->join('banks', 'banks.id', '=', 'bank_accounts.bank_id')
            ->join('movement_types', 'movement_types.id', '=', 'transactions.movement_type_id')
            ->join('people', 'people.id', '=', 'transactions.beneficiary_id')
            ->where('bank_accounts.company_id', $company->id)
            ->where('transactions.id',$transactionId)
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
            ->where('bank_accounts.company_id', $company->id)
            ->select('bank_accounts.id', 'bank_accounts.account_number', 'banks.name') // Puedes seleccionar lo que necesites
            ->get();

        // Obtenemos los tipos de movimiento de la compañía
        $movementtypes = MovementType::where('company_id', $company->id)->get();

        $people = Person::where('company_id', $company->id)->get();
        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Transaction/Edit', [
            'transaction' => $transaction,
            'bankaccounts' => $bankAccounts,
            'movementtypes' => $movementtypes,
            'people' => $people,
        ]);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
        ]);

        //actulizar los establecimientos
        $transaction->update($request->all());
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete(); // Esto usará SoftDeletes

        return response()->json([
            'success' => true,
            'message' => 'Movimiento bancario eliminado correctamente',
        ]);
    }
}
