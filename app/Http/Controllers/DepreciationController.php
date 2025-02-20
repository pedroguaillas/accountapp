<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DepreciationController extends Controller
{
    public function index(FixedAsset $fixedAsset)
    {
        // $process = new ProcessDepreciation;
        // $process->handle();

        $depreciations = DB::table('depreciations')
            ->selectRaw("id,to_char(date, 'DD-MM-YYYY') as date,amount,value,accumulated")
            ->where('fixed_asset_id', $fixedAsset->id)
            ->get();

        return Inertia::render('Depreciation/Index', [
            'fixedAsset' => $fixedAsset,
            'depreciations' => $depreciations,
        ]);
    }
}