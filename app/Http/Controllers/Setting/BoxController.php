<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Box;
use App\Models\MovementType;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BoxController extends Controller
{
    public function index()
    {
        $company = Company::first();

        $movements = MovementType::selectRaw(
            "movement_types.*, CONCAT(ap.code, ' - ', ap.name) AS ap_info"
        )
            ->leftJoin('accounts AS ap', 'movement_types.account_id', '=', 'ap.id')
            ->whereIn('movement_types.type', ["Ingreso", "Egreso"])
            ->where(function ($query) {
                $query->where('venue', 'ambos')
                    ->orWhere('venue', 'caja');
            })
            ->orderBy('movement_types.id')
            ->get();

        // Filtrar ingresos y egresos después de la consulta
        $ingress = $movements->where('type', 'Ingreso');
        $egress = $movements->where('type', 'Egreso');

        $boxes = Box::selectRaw(
            "boxes.name ,boxes.id," .
            "CONCAT(ap.code, ' - ', ap.name) AS ap_info "
        )
            ->leftJoin('accounts AS ap', 'boxes.account_id', '=', 'ap.id')
            ->where('boxes.company_id', $company->id)
            ->get();

        $accounts = Account::select('id', 'code', 'name')
            ->where('is_detail', true)
            ->where('company_id', $company->id)
            ->orderBy("code")
            ->get();

        return Inertia::render('Business/AccountLink/Boxes/Index', [
            'accounts' => $accounts,
            'boxes' => $boxes,
            'ingres' => $ingress,
            'egres' => $egress,
        ]);
    }

    public function updateBoxAccount(Request $request, $id)
    {
        $model = $request->table === 'Box' ? Box::find($id) : MovementType::find($id);

        $model->update(['account_id' => $request->account_id]);
    }
}