<?php

namespace App\Http\Controllers;

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

        // Consulta para activos fijos con filtros opcionales
        $fixedAssetssQuery = DB::table('fixed_assets')
            ->selectRaw("id, code, to_char(date_acquisition, 'DD-MM-YYYY') as date_acquisition, detail, value")
            ->where('company_id', $company->id)
            ->whereNull('deleted_at'); // Excluir registros eliminados

        // Aplicar filtro por código si existe
        if ($request->search) {
            $fixedAssetssQuery->where('code', 'LIKE', '%' . $request->search . '%');
        }

        // Aplicar paginación a activos fijos
        $fixedAssetss = $fixedAssetssQuery->paginate(10);

        $depreciations = $fixedAssetssQuery->paginate(10);

        // Retornar la vista con los datos y filtros actuales
        return Inertia::render('FixedAsset/Manager/Index', [
            'fixedAssetss' => $fixedAssetss,
            'depreciations' => $depreciations,
            'filters' => [
                'search' => $request->search, // Retornar el filtro de código
            ],
        ]);
    }

}