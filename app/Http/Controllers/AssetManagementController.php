<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use App\Models\Company;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetManagementController extends Controller
{
    public function index(Request $request)
    {
        // Obtener la compañía actual
        $company = Company::first();

        // Capturar los filtros de búsqueda enviados desde el frontend
        // dd( $request->search);

        // Consulta para activos fijos con filtros opcionales
        $fixedAssetssQuery = DB::table('fixed_assets')
            ->selectRaw("id, code, to_char(date_acquisition, 'DD-MM-YYYY') as date_acquisition, detail, value")
            ->where('company_id', $company->id);

        // Aplicar filtro por código si existe
        if ($request->search) {
            $fixedAssetssQuery->where('code', 'LIKE', '%' . $request->search . '%');
        }


        // Aplicar paginación a activos fijos
        $fixedAssetss = $fixedAssetssQuery->paginate(10);



        $depreciations = $fixedAssetssQuery->paginate(10);

        // Retornar la vista con los datos y filtros actuales
        return Inertia::render('FixedAsset/Index2', [
            'fixedAssetss' => $fixedAssetss,
            'depreciations' => $depreciations,
            'filters' => [
                'search' => $request->search, // Retornar el filtro de código
            ],
        ]);
    }

}