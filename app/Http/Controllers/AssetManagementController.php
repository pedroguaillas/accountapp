<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use App\Models\Company;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AssetManagementController extends Controller
{
    public function index()
    {
        $company = Company::first();
    
        // Paginación para activos fijos
        $fixedAssetss = DB::table('fixed_assets')
            ->selectRaw("id, code, to_char(date_acquisition, 'DD-MM-YYYY') as date_acquisition, detail, value")
            ->where('company_id', $company->id)
            ->paginate(10);  // Paginación de 10 activos fijos
    
        // Paginación para depreciaciones
        $depreciations = FixedAsset::paginate(10);  // Paginación para depreciaciones
    
        return Inertia::render('FixedAsset/Index2', [
            'fixedAssetss' => $fixedAssetss,
            'depreciations' => $depreciations,
        ]);
    }
    
}