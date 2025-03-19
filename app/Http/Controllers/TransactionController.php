<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Person;
use App\Models\MovementType;
use App\Models\Journal;
use App\Models\BankAccount;
use App\Models\Company;
use App\Http\Requests\TransactionRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

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
            ->whereNotIn('code',['AEU','AES'])
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

    public function store(TransactionRequest $transactionStoreRequest)
    {
        // Llamada a la compañía
        $company = Company::first();

        $journal = Journal::where('description', 'ASIENTO DE SITUACION INICIAL')
            ->where('company_id', $company->id)
            ->first();

        if ($journal === null) {
            return back()->withErrors(['error' => 'Por favor, crea el asiento inicial. Haz clic en "Aceptar" para continuar.', 'redirect' => 'journal.create']);
        }
        $banckAccountValidate = BankAccount::whereNull('account_id')
            ->where('bank_accounts.data_additional->company_id', $company->id)
            ->get();

        if ($banckAccountValidate->count() > 0) {
            return back()->withErrors(['error' => 'Por favor, vincula las cuentas para poder utilizar todas las funcionalidades. Haz clic en "Aceptar" para continuar.', 'redirect' => 'setting.account.bank.index']);
        }

        $movementTypeValidate = MovementType::whereNull('account_id')
            ->where(function ($query) {
                $query->where('venue', 'ambos')
                    ->orWhere('venue', 'bancos');
            })
            ->get();

        if ($movementTypeValidate->count() > 0) {
            return back()->withErrors(['error' => 'Por favor, vincula las cuentas para poder utilizar todas las funcionalidades. Haz clic en "Aceptar" para continuar.', 'redirect' => 'setting.account.bank.index']);
        }

        // usuario autentificado,
        $user = auth()->user();
        //fecha actual
        $date = Carbon::now();

        $data = [
            "company_id" => $company->id,
        ];

        //creacion de los estableicmientos 
        $transaction = Transaction::create([...$transactionStoreRequest->all(), 'data_additional' => $data]);
        $journalEntries = [];
        $banckAccount = BankAccount::find($transaction->bank_account_id);
        $movementType = MovementType::find($transaction->movement_type_id);

        // Actualizar el saldo de la cuenta bancaria restando el monto de la transacción
        if ($movementType->type === 'Ingreso') {
            $banckAccount->increment('current_balance', $transaction->amount);
            $journalEntries[] = [
                'account_id' => $banckAccount->account_id,
                'debit' => $transaction->amount,
                'have' => 0,
            ];
            $journalEntries[] = [
                'account_id' => $movementType->account_id,
                'debit' => 0,
                'have' => $transaction->amount,
            ];

        } else {
            $banckAccount->decrement('current_balance', abs($transaction->amount));
            $journalEntries[] = [
                'account_id' => $movementType->account_id,
                'debit' => $transaction->amount,
                'have' => 0,
            ];
            $journalEntries[] = [
                'account_id' => $banckAccount->account_id,
                'debit' => 0,
                'have' => $transaction->amount,
            ];
        }
        // Crear el diario
        $inputs = [
            'description' => "Movimiento Bancario " . $transaction->description,
            'date' => $date,
            'user_id' => $user->id,
            'document_id' => $transaction->id,
            'table' => 'transactions',
        ];

        $journal = $company->journals()->create($inputs);
        $journal->journalentries()->createMany($journalEntries);

        return to_route("transactions.index");
    }

}