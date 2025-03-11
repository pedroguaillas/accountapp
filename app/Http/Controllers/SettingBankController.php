<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\TypeBank;
use App\Models\BankAccount;
use App\Models\MovementType;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingBankController extends Controller
{
    public function index()
    {
        $company = Company::first();

        //traer los role ingres para la vinculacion de cuentas 
        $ingress = MovementType::selectRaw(
            "movement_types.*, " .
            "CONCAT(ap.code, ' - ', ap.name) AS ap_info "
        )
            ->leftJoin('accounts AS ap', 'movement_types.account_id', '=', 'ap.id')
            ->where('movement_types.type', "Ingreso")
            ->orderBy('movement_types.id')
            ->get();


        $egress = MovementType::selectRaw(
            "movement_types.*, " .
            "CONCAT(ap.code, ' - ', ap.name) AS ap_info "
        )
            ->leftJoin('accounts AS ap', 'movement_types.account_id', '=', 'ap.id')
            ->where('movement_types.type', "Egreso")
            ->orderBy('movement_types.id')
            ->get();

        $bankaccounts = BankAccount::selectRaw(
            "bank_accounts.*, b.name as bankname," .
            "CONCAT(ap.code, ' - ', ap.name) AS ap_info "
        )
            ->leftJoin('accounts AS ap', 'bank_accounts.account_id', '=', 'ap.id')
            ->join('banks as b','bank_accounts.bank_id','=','b.id' )
            ->where('bank_accounts.data_additional->company_id', $company->id)
            ->get();

        $accounts = Account::select('id', 'code', 'name')
            ->where('is_detail', true)
            ->where('company_id', $company->id)
            ->orderBy("code")
            ->get();
        return Inertia::render('Business/AccountLink/Bank/Index', [
            'bankingres' => $ingress,
            'bankegres' => $egress,
            'accountbanck' => $bankaccounts,
            'accounts' => $accounts,
        ]);
    }

    public function updateBankAccount(Request $request, $id)
    {
        if ($request->table==='BankAccount'){
            $model = BankAccount::find($id);
            $model->update(['account_id' => $request->account_id]);
        }
        else
        {
            $model = MovementType::find($id);
            $model->update(['account_id' => $request->account_id]);
        }
    }
}