<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CostCenter;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class CostCenterController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::first();

        $costCenters = CostCenter::select("*")
            ->where('company_id', $company->id);
       
        if ($request->search) {
            $costCenters->where('code', 'LIKE', '%' . $request->search . '%');
        }

        $costCenters = $costCenters->paginate(10);
        //dd($costCenters);

        return Inertia::render(
            'CostCenter/Index',
            [
                "filters" => $request->search,
                "costCenters" => $costCenters,
            ]
        );

    }

    public function store(Request $request)
    {
        $company = Company::first();
        CostCenter::create([...$request->all(), 'company_id' => $company->id]);
    }

    public function update(Request $request, CostCenter $costCenter)
    {
        $company = Company::first();

        $costCenter->update($request->all());
    }



    public function destroy(int $costCenterId)
    {
        $costCenter= CostCenter::findOrFail($costCenterId);
        $costCenter->delete(); // Esto usará SoftDeletes

        return response()->json([
            'success' => true,
            'message' => 'Centro de costo eliminado correctamente.',
        ]);
    }

}
