<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;


class BranchController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::first();

        // Construimos la consulta base
        $branches = Branch::query()
            ->where('company_id', $company->id);

        // Aplicamos el filtro si existe
        if ($request->has('search') && !empty($request->search)) {
            $branches->where('name', 'LIKE', '%' . $request->search . '%');
        }

        // Paginamos los resultados y preservamos los filtros en la URL
        $branches = $branches->paginate(10)->withQueryString();

        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Branch/Index', [
            'filters' => $request->search,
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

        $company = Company::first();

        $company->branches()->create($request->all());

    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'number' => 'required|min:1|max:999',
            'name' => 'nullable|min:3|max:300',
            'city' => 'required',
            'address' => 'required',
            //'is_matriz' => '',
            'enviroment_type' => 'required',
        ]);
        if ($request->is_matriz) {
            $company = Company::first();

            $company->branches()->update(['is_matriz' => false]);
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
