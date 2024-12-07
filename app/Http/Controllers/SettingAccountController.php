<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ActiveType;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingAccountController extends Controller
{
    public function index()
    {
        $company = Company::first();

        $activeTypes = ActiveType::selectRaw("active_types.*, accounts.code || ' - ' || accounts.name AS account_info")
            ->leftJoin('accounts', 'active_types.account_id', '=', 'accounts.id')
            ->orderBy('id')
            ->get();

        $accounts = Account::select('id', 'code', 'name')
            ->where('is_detail', true)
            ->where('company_id', $company->id)->get();

        return Inertia::render('Setting/Index', [
            'activeTypes' => $activeTypes,
            'accounts' => $accounts
        ]);
    }

    public function update(Request $request, ActiveType $activeType)
    {
        $activeType->update([
            'account_id' => $request->account_id
        ]);
    }
}
