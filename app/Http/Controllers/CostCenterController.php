<?php

namespace App\Http\Controllers;

use App\Models\CostCenter;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CostCenterController extends Controller
{
    public function index()
    {
        $costCenters = CostCenter::all();
        return Inertia::render('CostCenter/Index', ["costCenters" => $costCenters]);
    }

    public function store(Request $request)
    {
        CostCenter::create($request->all());
    }
}
