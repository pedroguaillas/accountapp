<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use Inertia\Inertia;

class AmortizationController extends Controller
{
    public function index()
    {

        //$depreciations = Depreciation::with('fixed_asset')->get(); // Obtén las depreciaciones
        return Inertia::render('IntangibleAsset/Amortizacion', [
            'amortizations' => FixedAsset::all(),
        ]);
    }
}
