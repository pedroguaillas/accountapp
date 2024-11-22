<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\FixedAsset;
use App\Models\PayMethod;
use App\Models\ActiveTypes;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FixedAssetController extends Controller
{
    public function index()
    {
        $company = Company::first();
        $company = Company::first();
      //  dd(FixedAsset::where('company_id', $company->id)->toSql(), $company->id);

        $fixedAssetss = FixedAsset::where('company_id', $company->id)->get();


       // dd($fixedAssetss->toArray());
        return Inertia::render('FixedAsset/Index', [
            'fixedAssetss' => $fixedAssetss,
        ]);

    }


    public function create()
    {
        $payMethods = PayMethod::all();
        $activeTypes=ActiveTypes::all();

        return Inertia::render('FixedAsset/Create', [
            'payMethods' => $payMethods,
            'activeTypes'=> $activeTypes,
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

        //return response()->json(['message' => 'Activo fijo creado con éxito.'], 201);

        return to_route('fixedassets.index');

    }



}
