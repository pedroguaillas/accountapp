<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdvanceRequest;
use App\Models\Advance;
use App\Models\Company;
use App\Models\Box;
use App\Models\Employee;
use App\Models\MovementType;
use App\Models\CashSession;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

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
            'cash' => $cash,
            'bankAccounts' => $bankAccounts,
            'optimum' => $optimum,
        ]);
    }

    public function store(AdvanceRequest $advanceRequest)
    {
        $company = Company::first();

        $movementType = MovementType::where('company_id', $company->id)
            ->where('code', 'AES')
            ->first();

        $company->advances()->create(
            [...$advanceRequest->all(), 'movement_type_id' => $movementType->id]
        );
        $journalEntries = [];
        if ($advanceRequest->payment_type === 'banco') {
            $bankAccount = BankAccount::where('bank_accounts.data_additional->company_id', $company->id)
                ->where('id', $advanceRequest->payment_method_id)
                ->first();
            $bankAccount->decrement('current_balance', abs($advanceRequest->amount));
            $journalEntries[] = [
                'account_id' => $movementType->account_id,
                'debit' => $advanceRequest->amount,
                'have' => 0,
            ];
            $journalEntries[] = [
                'account_id' => $bankAccount->account_id,
                'debit' => 0,
                'have' => $advanceRequest->amount,
            ];
        } else {
            $cash = CashSession::where('id', $advanceRequest->payment_method_id)
                ->first();
            $cash->increment('egress', $advanceRequest->amount);
            $cash->update([
                'balance' => $cash->initial_value + $cash->ingress - $cash->egress
            ]);

            $box = Box::where('company_id', $company->id)
                ->where('id', $cash->box_id)
                ->first();
            $journalEntries[] = [
                'account_id' => $movementType->account_id,
                'debit' => $advanceRequest->amount,
                'have' => 0,
            ];
            $journalEntries[] = [
                'account_id' => $box->account_id,
                'debit' => 0,
                'have' => $advanceRequest->amount,
            ];
        }

        $user = auth()->user();
        //fecha actual
        $date = Carbon::now();

        $inputs = [
            'description' => "Adelanto empleado",
            'date' => $date,
            'user_id' => $user->id,
            'document_id' => $advanceRequest->id,
            'table' => 'advances',
        ];

        $journal = $company->journals()->create($inputs);
        $journal->journalentries()->createMany($journalEntries);

        return redirect()->route('advances.index');
    }

    public function destroy(Advance $advance)
    {
        $company = Company::first();

        $movementType = MovementType::where('company_id', $company->id)
            ->where('code', 'AES')
            ->first();

        $journalEntries = [];
        if ($advance->payment_type === 'banco') {
            $bankAccount = BankAccount::where('bank_accounts.data_additional->company_id', $company->id)
                ->where('id', $advance->payment_method_id)
                ->first();
            $bankAccount->increment('current_balance', abs($advance->amount));
            $journalEntries[] = [
                'account_id' => $bankAccount->account_id,
                'debit' => $advance->amount,
                'have' => 0,
            ];
            $journalEntries[] = [
                'account_id' => $movementType->account_id,
                'debit' => 0,
                'have' => $advance->amount,

            ];
        } else {
            $cash = CashSession::where('id', $advance->payment_method_id)
                ->first();
            $cash->decrement('egress', $advance->amount);
            $cash->update([
                'balance' => $cash->initial_value + $cash->ingress - $cash->egress
            ]);

            $box = Box::where('company_id', $company->id)
                ->where('id', $cash->box_id)
                ->first();
            $journalEntries[] = [
                'account_id' => $box->account_id,
                'debit' => $advance->amount,
                'have' => 0,
            ];
            $journalEntries[] = [
                'account_id' => $movementType->account_id,
                'debit' => 0,
                'have' => $advance->amount,
            ];
        }

        $user = auth()->user();
        //fecha actual
        $date = Carbon::now();

        $inputs = [
            'description' => "Ajuste Adelanto empleado",
            'date' => $date,
            'user_id' => $user->id,
            'document_id' => $advance->id,
            'table' => 'advances',
        ];

        $journal = $company->journals()->create($inputs);
        $journal->journalentries()->createMany($journalEntries);
        $advance->delete(); // Esto usará SoftDeletes
    }
}