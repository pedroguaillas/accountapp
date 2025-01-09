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

        $activeTypes = ActiveType::selectRaw(
            "active_types.*," .
            "a.code || ' - ' || a.name AS a_info," .
            "ad.code || ' - ' || ad.name AS ad_info," .
            "ads.code || ' - ' || ads.name AS ads_info"
        )
            ->leftJoin('accounts AS a', 'account_id', '=', 'a.id')
            ->leftJoin('accounts AS ad', 'account_dep_id', '=', 'ad.id')
            ->leftJoin('accounts AS ads', 'account_dep_spent_id', '=', 'ads.id')
            ->where('active_types.company_id', $company->id)
            ->orderBy('id')
            ->get();

        $accounts = Account::select('id', 'code', 'name')
            ->where('is_detail', true)
            ->where('company_id', $company->id)
            ->where(function ($query) {
                $query->where('code', 'like', '1%')
                    ->orWhere('code', 'like', '5%');
            })
            ->orderBy("code")
            ->get();

        return Inertia::render('Business/AccountLink/Setting/Index', [
            'activeTypes' => $activeTypes,
            'accounts' => $accounts,
        ]);
    }

    public function updateActiveTypeAccount(Request $request, ActiveType $activeType)
    {
        $activeType->update([$request->name => $request->account_id]);
    }
}
