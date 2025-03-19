<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdvanceRequest;
use App\Models\Advance;
use App\Models\Company;
use App\Models\Employee;
use App\Models\MovementType;
use App\Models\CashSession;
use App\Models\BankAccount;
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

        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Advance/Index', [
            'filters' => [
                'search' => $search,
            ],
            'advances' => $advances,
        ]);
    }

    public function create()
    {
        $company = Company::first();

        $employeeCount = Employee::where('company_id', $company->id)
            ->count();
        $employees = [];
        $optimum = false;
        if ($employeeCount <= 5) {
            $optimum = true;
            $employees = Employee::where('company_id', $company->id)
                ->get();
        }
        $movementTypes = MovementType::where('company_id', $company->id)
            ->whereIn('code', ['AEU', 'AES'])
            ->get();
        $cash = CashSession::select('cash_sessions.id', 'cash_sessions.box_id', 'boxes.name')
            ->join('boxes', 'boxes.id', '=', 'cash_sessions.box_id') // Aquí corriges la relación
            ->where('cash_sessions.state_box', 'open')
            ->get();
        $bankAccounts = BankAccount::join('banks', 'banks.id', '=', 'bank_accounts.bank_id')
            ->where('bank_accounts.data_additional->company_id', $company->id)
            ->select('bank_accounts.id', 'bank_accounts.account_number', 'banks.name') // Puedes seleccionar lo que necesites
            ->get();

        return Inertia::render('Advance/Create', [
            'employees' => $employees,
            'movementTypes' => $movementTypes,
            'cash' => $cash,
            'bankAccounts' => $bankAccounts,
            'optimum' => $optimum,
        ]);
    }

    public function store(AdvanceRequest $advanceRequest)
    {

        $company = Company::first();

        $company->advances()->create($advanceRequest->all());

        return redirect()->route('advances.index');
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