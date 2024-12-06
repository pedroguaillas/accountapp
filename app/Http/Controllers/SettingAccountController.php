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

        $activeTypes = ActiveType::all();
        $accounts = Account::where('is_detail', true)
            ->where('company_id', $company->id)->get();

        return Inertia::render('Setting/Index', [
            'activeTypes' => $activeTypes,
            'accounts' => $accounts
        ]);
    }
}
