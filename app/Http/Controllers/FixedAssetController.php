<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\FixedAsset;
use App\Models\PayMethod;
use App\Models\ActiveType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class FixedAssetController extends Controller
{
    public function index()
    {
        $company = Company::first();
        $fixedAssetss = DB::table("fixed_assets")
            ->selectRaw("id, code,to_char(date_acquisition, 'DD-MM-YYYY') as date_acquisition,detail,value")
            ->where('company_id', $company->id)
            ->get();

        return Inertia::render('FixedAsset/Index', [
            'fixedAssetss' => $fixedAssetss,
        ]);
    }

    public function create()
    {
        $payMethods = PayMethod::all();
        $activeTypes = ActiveType::all();

        return Inertia::render('FixedAsset/Create', [
            'payMethods' => $payMethods,
            'activeTypes' => $activeTypes,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pay_method_id' => 'required|exists:pay_methods,id',
            'is_depretation_a' => 'boolean',
            'is_legal' => 'boolean',
            'vaucher' => 'nullable|string|max:17',
            'date_acquisition' => 'required|date',
            'detail' => 'nullable|string|max:300',
            'code' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'period' => 'nullable|integer|min:0',
            'value' => 'required|numeric|min:0',
            'residual_value' => 'nullable|numeric|min:0',
            'date_end' => 'nullable|date',
        ]);

        // Crear el activo fijo en la base de datos
        $company = Company::first(); // Asegúrate de tener la empresa disponible
        $company->fixedassets()->create($request->all());

        return to_route('fixedassets.index');
    }
    public function edit(int $fixedAsset_id)
    {
        $fixedAsset = DB::table("fixed_assets")
            ->selectRaw("
            id, 
            pay_method_id, 
            is_depretation_a, 
            is_legal, 
            vaucher, 
            to_char(date_acquisition, 'YYYY-MM-DD') as date_acquisition, 
            detail, 
            code, 
            type_id, 
            address, 
            period, 
            CAST(value AS FLOAT) as value, 
            CAST(residual_value AS FLOAT) as residual_value, 
            to_char(date_end, 'YYYY-MM-DD') as date_end
        ")
            ->where('id', $fixedAsset_id)
            ->first();

        //dd($fixedAsset);

        $payMethods = PayMethod::all();
        $activeTypes = ActiveType::all();


        return Inertia::render('FixedAsset/Edit', [
            'fixedAsset' => $fixedAsset,
            'payMethods' => $payMethods,
            'activeTypes' => $activeTypes,
        ]);

    }

    public function update(Request $request, FixedAsset $fixedAsset)
    {
        // Validación de los campos del formulario
        $request->validate([
            'pay_method_id' => 'required|exists:pay_methods,id',
           // 'is_depretation_a' => 'boolean',
            //'is_legal' => 'boolean',
            'vaucher' => 'nullable|string|max:17',
            'date_acquisition' => 'required|date',
            'detail' => 'required|string|max:300',
            'code' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'period' => 'required|integer|min:0',
            'value' => 'required|numeric|min:0',
            'residual_value' => 'nullable|numeric|min:0',
            'date_end' => 'required|date',
        ]);
    
        // Actualización del activo fijo con los datos validados
        $fixedAsset->update($request->all());
    
        // Redirigir a la vista de listado de activos fijos
        return to_route('fixedassets.index')->with('success', 'Activo fijo actualizado exitosamente.');
    }
    

}
