<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\IntangibleAsset;
use App\Models\PayMethod;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class IntangibleAssetController extends Controller
{
   
    public function create()
    {
        $payMethods = PayMethod::all();
  

        return Inertia::render('IntangibleAsset/Create', [
            'payMethods' => $payMethods,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pay_method_id' => 'required|exists:pay_methods,id',
            'is_legal' => 'boolean',
            'vaucher' => 'nullable|string|max:17',
            'date_acquisition' => 'required|date',
            'detail' => 'nullable|string|max:300',
            'code' => 'required|string|max:20',
            'period' => 'nullable|integer',
            'value' => 'required|numeric|min:0',
            'date_end' => 'nullable|date',
        ]);

        // Crear el activo inatngible en la base de datos
        $company = Company::first(); // Asegúrate de tener la empresa disponible
        $company->intangibleassets()->create($request->all());

        return to_route('intangibleamortization.index');
    }
    public function edit(int $intangibleAsset_id)
    {
        $intangibleAsset = DB::table("intangible_assets")
            ->selectRaw("
            id, 
            pay_method_id, 
            is_legal, 
            vaucher, 
            to_char(date_acquisition, 'YYYY-MM-DD') as date_acquisition, 
            detail, 
            code, 
            type, 
            period, 
            CAST(value AS FLOAT) as value, 
            to_char(date_end, 'YYYY-MM-DD') as date_end
        ")
            ->where('id', $intangibleAsset_id)
            ->first();

        //dd($intangibleAsset);

        $payMethods = PayMethod::all();

        return Inertia::render('IntangibleAsset/Edit', [
            'intangibleAsset' => $intangibleAsset,
            'payMethods' => $payMethods,
        ]);

    }

    public function update(Request $request, IntangibleAsset $intangibleasset)
    {
        // Validación de los campos del formulario
        $request->validate([
            'pay_method_id' => 'required|exists:pay_methods,id',
            //'is_legal' => 'boolean',
            'vaucher' => 'nullable|string|max:17',
            'date_acquisition' => 'required|date',
            'detail' => 'required|string|max:300',
            'code' => 'required|string|max:20',
            'period' => 'required|integer',
            'value' => 'required|numeric|min:0',
            'date_end' => 'required|date',
        ]);
    
        // Actualización del activo fijo con los datos validados
        $intangibleasset->update($request->all());
    
        // Redirigir a la vista de listado de activos fijos
        return redirect()->route('intangibleamortization.index')->with('success', 'Activo intangible actualizado exitosamente.');
}
    

public function destroy(int $intangibleassetId)
{
    $intangibleasset = IntangibleAsset::findOrFail($intangibleassetId);
    $intangibleasset->delete(); // Esto usará SoftDeletes

    return response()->json([
        'success' => true,
        'message' => 'Activo fijo eliminado correctamente.',
    ]);
}

}
