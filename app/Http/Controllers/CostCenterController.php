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
        $search = $request->input('search', '');

        $costCenters = CostCenter::where('company_id', $company->id)
            ->when(!empty($search), function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'LIKE', "%$search%")
                        ->orWhere('name', 'LIKE', "%$search%");
                });
            })
            ->paginate(10)
            ->withQueryString();

        // Renderizamos la vista con los datos necesarios
        return Inertia::render('CostCenter/Index', [
            'filters' => [
                'search' => $search,
            ],
            'costCenters' => $costCenters,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'name' => 'required',
        ]);

        $company = Company::first();

        //crear el costcenter
        $company->costcenters()->create($request->all());
    }

    public function update(Request $request, CostCenter $costCenter)
    {
        $request->validate([
            'code' => 'required',
            'name' => 'required',
        ]);
        //actualizar constcenter
        $costCenter->update($request->all());
    }

    public function destroy(int $costCenterId)
    {
        //eleiminar el costcenter
        $costCenter = CostCenter::findOrFail($costCenterId);
        $costCenter->delete(); // Esto usará SoftDeletes

        return response()->json([
            'success' => true,
            'message' => 'Centro de costo eliminado correctamente.',
        ]);
    }

    public function updateState($id)
    {
        $costcenter = CostCenter::findOrFail($id);
        $costcenter->state = !$costcenter->state; // Toggle the state
        $costcenter->save();

        return response()->json(['success' => true]);
    }
}