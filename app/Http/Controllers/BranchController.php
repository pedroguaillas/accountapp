<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;


class BranchController extends Controller
{
    public function index(Request $request)
    {
        // Obtener el término de búsqueda desde la solicitud
        $search = $request->input('search', ''); // Usar un valor por defecto vacío si no hay búsqueda
    
        // Consultar las sucursales y aplicar filtro si hay término de búsqueda
        $branches = Branch::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        $branches = $branches->paginate(10);
        // Retornar las sucursales a la vista
        return inertia('Branch/Index', [
            'branches' => $branches,
        ]);
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|min:1|max:999',
            'name' => 'nullable|min:3|max:300',
            'city' => 'required',
            'address' => 'required',
            //'is_matriz' => '',
            'enviroment_type' => 'required',

        ]);

        $company= Company::first();

        $company->branches()->create($request->all());

    }

    public function update(Request $request,Branch $branch)
    {
        $request->validate([
            'number' => 'required|min:1|max:999',
            'name' => 'nullable|min:3|max:300',
            'city' => 'required',
            'address' => 'required',
            //'is_matriz' => '',
            'enviroment_type' => 'required',
        ]);
        if($request->is_matriz)
        {
            $company= Company::first();

            $company->branches()->update(['is_matriz'=>false]);
        }
        
        $branch->update($request->all());
    }

    public function destroy(Branch $branch)
    {
        $branch->delete(); // Esto usará SoftDeletes

        return response()->json([
            'success' => true,
            'message' => 'Establecimiento eliminado correctamente.',
        ]);
    }
}
