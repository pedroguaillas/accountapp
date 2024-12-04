<?php

namespace App\Http\Controllers;

use App\Http\Requests\FixedAssetStoreRequest;
use App\Http\Requests\FixedAssetUpdateRequest;
use App\Models\Company;
use App\Models\FixedAsset;
use App\Models\PayMethod;
use App\Models\ActiveType;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class FixedAssetController extends Controller
{

    public function create()
    {
        $payMethods = PayMethod::all();
        $activeTypes = ActiveType::all();

        return Inertia::render('FixedAsset/Create', [
            'payMethods' => $payMethods,
            'activeTypes' => $activeTypes,
        ]);
    }

    public function store(FixedAssetStoreRequest $fixedAssetStoreRequest)
    {

        // Crear el activo fijo en la base de datos
        $company = Company::first(); // Asegúrate de tener la empresa disponible
        $company->fixedassets()->create($fixedAssetStoreRequest->all());

        return to_route('assetsdepreciation.index');
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

    public function update(FixedAssetUpdateRequest $fixedAssetUpdateRequest, FixedAsset $fixedAsset)
    {
        // Actualización del activo fijo con los datos validados
        $fixedAsset->update($fixedAssetUpdateRequest->all());
        // Redirigir a la vista de listado de activos fijos
        return to_route('assetsdepreciation.index')->with('success', 'Activo fijo actualizado exitosamente.');
    }

    public function destroy(int $fixedAssetId)
    {
        $fixedAsset = FixedAsset::findOrFail($fixedAssetId);
        $fixedAsset->delete(); // Esto usará SoftDeletes

        return response()->json([
            'success' => true,
            'message' => 'Activo fijo eliminado correctamente.',
        ]);
    }


}
