<?php

namespace App\Http\Controllers;

use App\Models\Advance;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdvanceController extends Controller
{
    public function index(Request $request)
    {
        // Obtener la primera compañía
        $company = Company::first();

        // Validar que la compañía exista
        if (!$company) {
            return back()->withErrors(['error' => 'No se encontró una compañía registrada.']);
        }

        // Construimos la consulta base
        $advances = Advance::select('advances.*', 'e.cuit', 'e.name')
            ->join('employees as e', 'e.id', '=', 'advances.employee_id') // Relación con empleados
            ->where('advances.company_id', $company->id);

        // Aplicamos el filtro si existe
        $search = $request->input('search', '');
        $advances->whereRaw("LOWER(e.name) LIKE ?", ["%" . strtolower($search) . "%"]); // Buscar por nombre del empleado

        // Obtenemos los empleados como una colección
        $employees = Employee::where('company_id', $company->id)->get();

        // Paginamos los resultados y preservamos los filtros en la URL
        $paginatedAdvances = $advances->paginate(10)->withQueryString();

        //dd($paginatedAdvances);
        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Advance/Index', [
            'filters' => [
                'search' => $search,
            ],
            'advances' => $paginatedAdvances,
            'employees' => $employees,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|min:1',
        ]);
        $company = Company::first();

        $company->advances()->create($request->all());

    }

    public function update(Request $request, Advance $advance)
    {
        $request->validate([
            'amount' => 'required|min:1',
        ]);
        $advance->update($request->all());
    }

    public function destroy(Advance $advance)
    {
        $advance->delete(); // Esto usará SoftDeletes

        return response()->json([
            'success' => true,
            'message' => 'Adelanto eliminado correctamente.',
        ]);
    }
}
