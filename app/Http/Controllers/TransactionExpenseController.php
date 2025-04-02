<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\CashSession;
use App\Models\Journal;
use App\Models\BankAccount;
use App\Models\Box;
use App\Models\Company;
use App\Http\Requests\TransactionExpenseRequest;
use App\Models\TransactionExpense;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class TransactionExpenseController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::first();
        $search = $request->input('search', ''); // Si no hay búsqueda, usa un valor vacío

        // Construimos la consulta base
        $transactions = TransactionExpense::query()
            ->select('transaction_expenses.*','expenses.name')
            ->join('expenses','expenses.id','=','expense_id')
            ->where('transaction_expenses.data_additional->company_id', $company->id)
            ->when($search, function ($query, $search) {
                $query->whereRaw("LOWER(description) LIKE ?", ["%" . strtolower($search) . "%"]);
            })->paginate(10)
            ->withQueryString(); // Puedes seleccionar lo que necesites
            
        // Renderizamos la vista con los datos necesarios
        return Inertia::render('TransactionExpense/Index', [
            'filters' => [
                'search' => $search,
            ],
            'expenses' => $transactions,
        ]);
    }

    public function create()
    {
        $company = Company::first();

        // Obtenemos las cuentas bancarias de la compañía
        $boxes = CashSession::select('cash_sessions.id', 'cash_sessions.box_id', 'boxes.name')
            ->join('boxes', 'boxes.id', '=', 'cash_sessions.box_id') // Aquí corriges la relación
            ->where('cash_sessions.state_box', 'open')
            ->get();
        $bankAccounts = BankAccount::join('banks', 'banks.id', '=', 'bank_accounts.bank_id')
            ->where('bank_accounts.data_additional->company_id', $company->id)
            ->select('bank_accounts.id', 'bank_accounts.account_number', 'banks.name') // Puedes seleccionar lo que necesites
            ->get();

        // Obtenemos los tipos de gastos de la compañía
        $expenses = Expense::where('company_id', $company->id)
            ->get();
        // Renderizamos la vista con los datos necesarios
        return Inertia::render('TransactionExpense/Create', [
            'boxes' => $boxes,
            'bankAccounts' => $bankAccounts,
            'expenses' => $expenses,
        ]);
    }

    public function store(TransactionExpenseRequest $transactionExpenseRequest)
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

        $boxValidate = Box::whereNull('account_id')
            ->where('boxes.company_id', $company->id)
            ->get();

        if ($boxValidate->count() > 0) {
            return back()->withErrors(['error' => 'Por favor, vincula las cuentas para poder utilizar todas las funcionalidades. Haz clic en "Aceptar" para continuar.', 'redirect' => 'setting.account.box.index']);
        }

        $expenseValidate = Expense::whereNull('account_id')
            ->get();

        if ($expenseValidate->count() > 0) {
            return back()->withErrors(['error' => 'Por favor, vincula las cuentas para poder utilizar todas las funcionalidades. Haz clic en "Aceptar" para continuar.', 'redirect' => 'setting.account.expenses.index']);
        }

        // usuario autentificado,
        $user = auth()->user();
        //fecha actual
        $date = Carbon::now();

        $data = [
            "company_id" => $company->id,
        ];

        //creacion de gastos
        $transaction = TransactionExpense::create([...$transactionExpenseRequest->all(), 'data_additional' => $data]);
        $journalEntries = [];
        $expense = Expense::find($transaction->expense_id);

        if ($transactionExpenseRequest->payment_type === 'banco') {
            $bankAccount = BankAccount::where('bank_accounts.data_additional->company_id', $company->id)
                ->where('id', $transactionExpenseRequest->payment_method_id)
                ->first();

            $bankAccount->decrement('current_balance', abs($transaction->amount));
            $journalEntries[] = [
                'account_id' => $expense->account_id,
                'debit' => $transaction->amount,
                'have' => 0,
            ];
            $journalEntries[] = [
                'account_id' => $bankAccount->account_id,
                'debit' => 0,
                'have' => $transaction->amount,
            ];
        }else
        {
            $cash = CashSession::where('id', $transactionExpenseRequest->payment_method_id)
                ->first();
            $cash->increment('egress', $transactionExpenseRequest->amount);
            $cash->update([
                'balance' => $cash->initial_value + $cash->ingress - $cash->egress
            ]);

            $box = Box::where('company_id', $company->id)
                ->where('id', $cash->box_id)
                ->first();
            $journalEntries[] = [
                'account_id' => $expense->account_id,
                'debit' => $transactionExpenseRequest->amount,
                'have' => 0,
            ];
            $journalEntries[] = [
                'account_id' => $box->account_id,
                'debit' => 0,
                'have' => $transactionExpenseRequest->amount,
            ];
        }

        // Crear el diario
        $inputs = [
            'description' => "Movimiento Gasto " . $transaction->description,
            'date' => $date,
            'user_id' => $user->id,
            'document_id' => $transaction->id,
            'table' => 'transactions',
        ];

        $journal = $company->journals()->create($inputs);
        $journal->journalentries()->createMany($journalEntries);

        return to_route("transaction.expenses.index");
    }

}