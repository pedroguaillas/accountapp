<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Imports\AccountsImport;
use App\Models\Company;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function index()
    {
        $company = Company::first();

        $accounts = Account::select('accounts.code', 'accounts.name', 'a2.code AS c2')
            ->leftJoin('accounts AS a2', 'accounts.parent_id', 'a2.id')
            ->where('accounts.company_id', $company->id)
            ->orderBy('accounts.id')->get();

        return Inertia::render('Account/Index', [
            'accounts' => $accounts
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);

        // Importar el archivo Excel a la tabla 'accounts'
        Excel::import(new AccountsImport, $request->file('file'));
    }
}
