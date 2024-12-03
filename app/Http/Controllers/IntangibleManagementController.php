<?php

namespace App\Http\Controllers;

use App\Models\IntangibleAsset;
use App\Models\Company;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntangibleManagementController extends Controller
{
    public function index(Request $request)
    {
        // Obtener la compañía actual
        $company = Company::first();

        // Capturar los filtros de búsqueda enviados desde el frontend
        // dd( $request->search);

        // Consulta para activos fijos con filtros opcionales
        $intangibleAssetssQuery = DB::table('intangible_assets')
            ->selectRaw("id, code, to_char(date_acquisition, 'DD-MM-YYYY') as date_acquisition, detail, value")
            ->where('company_id', $company->id)
            ->whereNull('deleted_at'); // Excluir registros eliminados

        // Aplicar filtro por código si existe
        if ($request->search) {
            $intangibleAssetssQuery->where('code', 'LIKE', '%' . $request->search . '%');
        }


        // Aplicar paginación a activos fijos
        $intangibleAssetss = $intangibleAssetssQuery->paginate(10);

        $amortizations = $intangibleAssetssQuery->paginate(10);

        // Retornar la vista con los datos y filtros actuales
        return Inertia::render('IntangibleAsset/Index2', [
            'intangibleAssetss' => $intangibleAssetss,
            'amortizations' => $amortizations,
            'filters' => [
                'search' => $request->search, // Retornar el filtro de código
            ],
        ]);
    }

}