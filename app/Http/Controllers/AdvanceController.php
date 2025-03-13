<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdvanceRequest;
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

        $search = $request->input('search', '');
        // Construimos la consulta base
        $advances = Advance::select('advances.*', 'e.cuit', 'e.name')
            ->join('employees as e', 'e.id', '=', 'advances.employee_id')
            ->where('advances.company_id', $company->id)
            ->when($search, function ($query, $search) {
                return $query->whereRaw("LOWER(e.name) LIKE ?", ["%" . strtolower($search) . "%"]);
            })
            ->paginate(10)->withQueryString();

        // Obtenemos los empleados como una colección
        $employees = Employee::where('company_id', $company->id)->get();
        //dd($paginatedAdvances);
        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Advance/Index', [
            'filters' => [
                'search' => $search,
            ],
            'advances' => $advances,
            'employees' => $employees,
        ]);
    }

    public function store(AdvanceRequest $advanceRequest)
    {
        $company = Company::first();

        $company->advances()->create($advanceRequest->all());
    }

    public function update(AdvanceRequest $advanceRequest, Advance $advance)
    {
        $advance->update($advanceRequest->all());
    }

    public function destroy(Advance $advance)
    {
        $advance->delete(); // Esto usará SoftDeletes
    }
}