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

        $roleIngresses = RoleIngress::selectRaw(
            "role_ingresses.*," .
            "aa.code || ' - ' || aa.name AS aa_info," .
            "ap.code || ' - ' || ap.name AS ap_info," .
            "ass.code || ' - ' || ass.name AS ass_info"
        )
            ->leftJoin('accounts AS aa', 'account_active_id', '=', 'aa.id')
            ->leftJoin('accounts AS ap', 'account_pasive_id', '=', 'ap.id')
            ->leftJoin('accounts AS ass', 'account_spent_id', '=', 'ass.id')
            ->where('role_ingresses.company_id', $company->id)
            ->orderBy('id')
            ->get();

        $roleEgresses = RoleEgress::selectRaw(
            "role_egresses.*," .
            "aa.code || ' - ' || aa.name AS aa_info," .
            "ap.code || ' - ' || ap.name AS ap_info," .
            "ass.code || ' - ' || ass.name AS ass_info"
        )
            ->leftJoin('accounts AS aa', 'account_active_id', '=', 'aa.id')
            ->leftJoin('accounts AS ap', 'account_pasive_id', '=', 'ap.id')
            ->leftJoin('accounts AS ass', 'account_spent_id', '=', 'ass.id')
            ->where('role_egresses.company_id', $company->id)
            ->orderBy('id')
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
