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
        $accounts = Account::select('code', 'name')
            ->where('company_id', $company->id)
            ->orderBy('id')->get();
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
