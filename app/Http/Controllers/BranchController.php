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
        $search = $request->input('search', '');

        // Construimos la consulta base
        $branches = Branch::query()
            ->where('company_id', $company->id)
            ->when($search, function ($query, $search) {
                $query->whereRaw("LOWER(branches.name) LIKE ?", ["%" . strtolower($search) . "%"]);
            })->paginate(10)
            ->withQueryString();

        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Branch/Index', [
            'filters' => [
                'search' => $search, // Retornar el filtro de búsqueda
            ],
            'branches' => $branches,
        ]);
    }

    public function store(Request $request)
    {
        //validacion de datos
        $request->validate([
            'number' => 'required|min:1|max:999',
            'name' => 'nullable|min:3|max:300',
            'city' => 'required',
            'address' => 'required',
            //'is_matriz' => '',
            'enviroment_type' => 'required',

        ]);
        //llamada a la compania
        $company = Company::first();

        //creacion de los estableicmientos 
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

        //actualizar si esta ya esta seleccionado la matriz las otras se colocan sucursales
        if ($request->is_matriz) {
            $company = Company::first();

            $company->branches()->update(['is_matriz' => false]);
        }

        //actulizar los establecimientos
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

    public function updateState($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->state = !$branch->state; // Toggle the state
        $branch->save();

        return response()->json(['success' => true]);
    }

}
