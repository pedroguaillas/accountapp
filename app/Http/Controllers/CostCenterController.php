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

        // Construimos la consulta base
        $costCenters = CostCenter::select("*")
            ->where('company_id', $company->id);

        // Aplicamos el filtro si existe
        if ($request->has('search') && !empty($request->search)) {
            $costCenters->where('code', 'LIKE', '%' . $request->search . '%')
                ->orWhere('name', 'LIKE', '%' . $request->search . '%');
        }

        // Paginamos los resultados
        $costCenters = $costCenters->paginate(10)->withQueryString(); // Importante usar withQueryString()


        // Renderizamos la vista con los datos necesarios
        return Inertia::render('CostCenter/Index', [
            'filters' => $request->search,
            'costCenters' => $costCenters,
        ]);
    }


    public function store(Request $request)
    {
        $company = Company::first();
        //crear el costcenter

        $company->costcenters()->create($request->all());
    }

    public function update(Request $request, CostCenter $costCenter)
    {
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
        $costcenter ->state = !$costcenter ->state; // Toggle the state
        $costcenter ->save();

        return response()->json(['success' => true]);
    }

}
