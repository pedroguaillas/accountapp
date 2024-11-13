<?php

namespace App\Http\Controllers;

use App\Models\Company;
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
        $company = Company::first();
        CostCenter::create([...$request->all(), 'company_id' => $company->id]);
    }

    public function update(Request $request,CostCenter $costCenter)
    {
        $company = Company::first();
        
        $costCenter->update($request->all());
    }

    public function delete(Request $request,CostCenter $costCenter)
    {
        //no tenga relacion el centro de costo con otras
        //if ()
        //
        //else
        //return(response)
        $costCenter->delete();
    }
}
