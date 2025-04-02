<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\BankAccount;
use App\Models\Expense;
use App\Models\MovementType;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function index()
    {
        $company = Company::first();

        //traer los role ingres para la vinculacion de cuentas 
        $expenses = Expense::selectRaw(
            "expenses.*, CONCAT(ap.code, ' - ', ap.name) AS ap_info"
        )
            ->leftJoin('accounts AS ap', 'expenses.account_id', '=', 'ap.id')
            ->orderBy('expenses.id')
            ->get();

        $accounts = Account::select('id', 'code', 'name')
            ->where('is_detail', true)
            ->where('company_id', $company->id)
            ->orderBy("code")
            ->get();

        return Inertia::render('Business/AccountLink/Expense/Index', [
            'expenses' => $expenses,
            'accounts' => $accounts,
        ]);
    }

    public function updateExpenseAccount(Request $request, $id)
    {

        $model = Expense::find($id);

        $model->update(['account_id' => $request->account_id]);
    }
}