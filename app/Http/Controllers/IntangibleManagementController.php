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
    $company = Company::first();

    // Filtro de búsqueda
    $search = $request->search;

    // Consulta principal
    $intangibleAssetssQuery = DB::table('intangible_assets')
        ->selectRaw("id, code, to_char(date_acquisition, 'DD-MM-YYYY') as date_acquisition, detail, value")
        ->where('company_id', $company->id)
        ->whereNull('deleted_at');

    if ($search) {
        $intangibleAssetssQuery->where('code', 'LIKE', '%' . $search . '%');
    }

    // Paginación
    $intangibleAssetss = $intangibleAssetssQuery->paginate(10);
    $amortizations = $intangibleAssetssQuery->paginate(10);

    return Inertia::render('IntangibleAsset/Manager/Index', [
        'intangibleAssetss' => $intangibleAssetss,
        'amortizations' => $amortizations,
        'filters' => ['search' => $search],
    ]);
}


}