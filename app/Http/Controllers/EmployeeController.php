<?php

namespace App\Http\Controllers;


use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        // Obtener el término de búsqueda desde la solicitud
        $search = $request->input('search', ''); // Usar un valor por defecto vacío si no hay búsqueda
        $company = Company::first();

        // Consultar las sucursales y aplicar filtro si hay término de búsqueda
        $employees = Employee::selectRaw("
                id, 
                company_id, 
                cuit, 
                name, 
                sector_code, 
                position, 
                days, 
                CAST(salary AS FLOAT) AS salary, 
                CAST(porcent_aportation  AS FLOAT) AS porcent_aportation, 
                is_a_parnert, 
                is_a_qualified_craftsman, 
                affiliated_with_spouse, 
                to_char(date_start, 'YYYY-MM-DD') as date_start

            ")
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->where('company_id', $company->id) // Filtrar por la compañía
            ->whereNull('deleted_at') // Excluir registros eliminados
            ->paginate(10)
            ->withQueryString(); // Paginar directamente en la consulta
        // Retornar las sucursales a la vista
        return inertia('Employee/Index', [
            'employees' => $employees,
            'filters' => [
                'search' => $request->search, // Retornar el filtro de búsqueda
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Employee/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:300',
            'cuit' => 'required|min:10|max:13',
        ]);

        $inputs = [...$request->all(), 'salary' => $request->salary ?? 0, 'days' => $request->days ?? 0, 'porcent_aportation' => $request->porcent_aportation ?? 0];

        $company = Company::first();

        $company->employees()->create($inputs);
        return to_route('employee.index');
    }

    public function edit(int $employeeId)
    {
        $employee = Employee::selectRaw("
            id, 
            company_id, 
            cuit, 
            name, 
            sector_code, 
            position, 
            days, 
            CAST(salary AS FLOAT) AS salary, 
            CAST(porcent_aportation  AS FLOAT) AS porcent_aportation, 
            is_a_parnert, 
            is_a_qualified_craftsman, 
            affiliated_with_spouse, 
            to_char(date_start, 'YYYY-MM-DD') as date_start,
            xiii,
            xiv,
            reserve_funds,
            email
        ")
            ->where('id', $employeeId)
            ->first();

        return Inertia::render('Employee/Edit', [
            'employee' => $employee,
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'nullable|min:3|max:300',
            'cuit' => 'required|min:10|max:13',
        ]);

        $inputs = [...$request->all(), 'salary' => $request->salary ?? 0, 'days' => $request->days ?? 0, 'porcent_aportation' => $request->porcent_aportation ?? 0];

        $employee->update($inputs);
        return to_route('employee.index');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete(); // Esto usará SoftDeletes

        return response()->json([
            'success' => true,
            'message' => 'Empleado eliminado correctamente.',
        ]);
    }
}
