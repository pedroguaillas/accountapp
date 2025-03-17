<?php

namespace App\Http\Controllers;

use App\Models\Hour;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class HourController extends Controller
{
    public function index(Request $request)
    {
        // Obtener la primera compañía
        $company = Company::first();
        if (!$company) {
            return back()->withErrors(['error' => 'No se encontró una compañía registrada.']);
        }
        $search = $request->input('search', '');

        $hours = Hour::select('hours.*', 'e.cuit', 'e.name')
            ->join('employees as e', 'e.id', '=', 'hours.employee_id') // Relación con empleados
            ->where('hours.company_id', $company->id)
            ->when(!empty($search), function ($query) use ($search) {
                $query->where('e.name', 'LIKE', "%$search%"); // Buscar por nombre del empleado
            });

        // Obtenemos los empleados como una colección
        $employees = Employee::where('company_id', $company->id)->get();

        // Paginamos los resultados y preservamos los filtros en la URL
        $paginatedhours = $hours->paginate(10)->withQueryString();

        //dd($paginatedAdvances);
        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Hour/Index', [
            'filters' => [
                'search' => $search,
            ],
            'hours' => $paginatedhours,
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $date = Carbon::now();
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|min:1',
            //TODO poner el inimo segun la configuracion de la generacion del rol
            'date' => "required|before_or_equal:$date",
            'type' => 'required',
        ]);
        $company = Company::first();

        $company->hours()->create($request->all());
    }

    public function update(Request $request, Hour $hour)
    {
        $date = Carbon::now();
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|min:1',
            'date' => "required|before_or_equal:$date",
            'type' => 'required',
        ]);
        $hour->update($request->all());
    }

    public function destroy(Hour $hour)
    {
        $hour->delete(); // Esto usará SoftDeletes

        return response()->json([
            'success' => true,
            'message' => 'Adelanto eliminado correctamente.',
        ]);
    }
}