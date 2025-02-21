<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\RoleIngress;
use App\Models\RoleEgress;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingRolController extends Controller
{
    public function index()
    {
        $company = Company::first();

        //traer los role ingres para la vinculacion de cuentas 
        $roleIngresses = RoleIngress::selectRaw(
            "role_ingresses.*, " .
            "CONCAT(ap.code, ' - ', ap.name) AS ap_info, " .
            "CONCAT(ac.code, ' - ', ac.name) AS ac_info"
        )
        ->leftJoin('accounts AS ap', 'role_ingresses.account_departure_id', '=', 'ap.id')
        ->leftJoin('accounts AS ac', 'role_ingresses.account_counterpart_id', '=', 'ac.id')
        ->where('role_ingresses.company_id', $company->id)
        ->orderBy('role_ingresses.id')
        ->get();

        $roleEgresses = RoleEgress::selectRaw(
            "role_egresses.*, " .
            "CONCAT(ap.code, ' - ', ap.name) AS ap_info, " .
            "CONCAT(ac.code, ' - ', ac.name) AS ac_info"
        )
        ->leftJoin('accounts AS ap', 'role_egresses.account_departure_id', '=', 'ap.id')
        ->leftJoin('accounts AS ac', 'role_egresses.account_counterpart_id', '=', 'ac.id')
        ->where('role_egresses.company_id', $company->id)
        ->orderBy('role_egresses.id')
        ->get();
        
        $accounts = Account::select('id', 'code', 'name')
            ->where('is_detail', true)
            ->where('company_id', $company->id)
            ->where(function ($query) {
                $query->where('code', 'like', '1%')
                    ->orWhere('code', 'like', '2%')
                    ->orWhere('code', 'like', '5%');
            })
            ->orderBy("code")
            ->get();
        return Inertia::render('Business/AccountLink/Rol/Index', [
            'roleIngresses' => $roleIngresses,
            'roleEgresses' => $roleEgresses,
            'accounts' => $accounts,
        ]);
    }

    public function updateRoleAccount(Request $request, $id)
    {
        $model = null;
        if ($request->type === 'ingress') {
            $model = RoleIngress::find($id);
        } else {
            $model = RoleEgress::find($id);
        }
        $model->update([$request->name => $request->account_id]);
    }
}