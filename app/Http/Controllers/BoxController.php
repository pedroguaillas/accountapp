<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\CashSession;
use App\Models\Company;
use App\Models\MovementType;
use App\Models\Journal;
use App\Models\Employee;
use App\Models\TransactionBox;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class BoxController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::first();
        $search = $request->input('search', '');

        $boxes = Box::query()
            ->select('boxes.id', 'boxes.name', 'employees.name as employee_name', 'cash_sessions.state_box', 'cash_sessions.balance')
            ->leftJoin('cash_sessions', function ($query) {
                $query->on('boxes.id', 'box_id')
                    ->where('state_box', 'open');
            })
            ->join('employees', 'boxes.owner_id', '=', 'employees.id')
            ->where('boxes.company_id', $company->id)
            ->when($search, function ($query, $search) {
                $query->whereRaw("LOWER(boxes.name) LIKE ?", ["%" . strtolower($search) . "%"]);
            })
            ->paginate(10)
            ->withQueryString();

        $employees = Employee::where('company_id', $company->id)->get();

        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Box/Index', [
            'filters' => [
                'search' => $search, // Retornar el filtro de búsqueda
            ],
            'boxes' => $boxes,
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        //validacion de datos
        $request->validate([
            'owner_id' => 'required|exists:employees,id',
            'name' => 'required',
            'type' => 'required',
        ]);

        //llamada a la compania
        $company = Company::first();

        //creacion de los estableicmientos 
        $company->boxes()->create($request->all());
    }

    public function update(Request $request, Box $box)
    {
        //validacion de datos
        $request->validate([
            'owner_id' => 'required|exists:employees,id',
            'name' => 'required',
            'type' => 'required',
        ]);
        //actulizar los establecimientos
        $box->update($request->all());
    }

    public function destroy(Box $box)
    {
        $box = Box::findOrFail($box->id);
        $box->delete();

        return redirect()->route('boxes.index');
    }

    public function open(Request $request, Box $box)
    {
        $request->validate([
            'initial_value' => 'required|numeric|min:0',
        ]);

        $company = Company::first();

        $journal = Journal::where('description', 'ASIENTO DE SITUACION INICIAL')
            ->where('company_id', $company->id)
            ->first();

        if (!$journal) {
            return redirect()->route('journal.create')->withErrors([
                'warning' => 'Debes crear el asiento inicial antes de continuar.',
            ]);
        }

        $boxAccountValidate = Box::whereNull('account_id')
            ->where('boxes.company_id', $company->id)
            ->exists();

        if ($boxAccountValidate) {
            return redirect()->route('setting.account.box.index')->withErrors([
                'warning' => 'Debes vincular las cuentas antes de continuar.',
            ]);
        }

        $general = Box::where('name', 'Caja General')->first();

        $cashgeneral = CashSession::where('box_id', $general->id)
            ->where('state_box', 'open')
            ->latest()
            ->first();

        if ($request->initial_value <= $cashgeneral->balance) {
            $box->cashSessions()->create([
                'open_employee_id' => 1,//TODO revisar id
                'close_employee_id' => null,
                'initial_value' => $request->initial_value,
                'ingress' => 0,
                'egress' => 0,
                'balance' => $request->initial_value,
                'state_box' => 'open',//poner en espaniol
            ]);
            $cashgeneral->increment('egress', $request->initial_value);
            $cashgeneral->update([
                'balance' => $cashgeneral->initial_value + $cashgeneral->ingress - $cashgeneral->egress
            ]);

            $journalEntries = [];
            $journalEntries[] = [
                'account_id' => $box->account_id,
                'debit' => $request->initial_value,
                'have' => 0,
            ];
            $journalEntries[] = [
                'account_id' => $general->account_id,
                'debit' => 0,
                'have' => $request->initial_value,
            ];
            $description = "Desenbolso o reposición de caja";
            $user = auth()->user();
            //fecha actual
            $date = Carbon::now();

            $inputs = [
                'description' => $description,
                'date' => $date,
                'user_id' => $user->id,
                'document_id' => $box->id,
                'table' => 'transaction_boxes',
            ];
            $journal = $company->journals()->create($inputs);
            $journal->journalentries()->createMany($journalEntries);
        }
    }

    //metodo para traer datos de la seccion de cjas y las transacciones
    public function closeinformation(Box $box)
    {
        //TODO analizar en el caso que se elimine la caja por la foreing key presente en las migraciones
        $boxe = CashSession::where('box_id', $box->id)
            ->orderBy('id', 'desc')
            ->first();

        $ingres = (float) (TransactionBox::join('movement_types as mt', 'mt.id', '=', 'transaction_boxes.movement_type_id')
            ->where('transaction_boxes.cash_session_id', '=', $boxe->id)
            ->where('mt.type', '=', 'Ingreso')
            ->sum('transaction_boxes.amount') ?? 0);

        $egres = (float) (TransactionBox::join('movement_types as mt', 'mt.id', '=', 'transaction_boxes.movement_type_id')
            ->where('transaction_boxes.cash_session_id', '=', $boxe->id)
            ->where('mt.type', 'Egreso')
            ->sum('amount') ?? 0);

        $initial_value = (float) $boxe->initial_value;  // Si initial_value también viene como string

        $balance = ($ingres + $boxe->initial_value) - $egres;

        // Retornar los datos como JSON
        return response()->json([
            'ingress' => $ingres,
            'egress' => $egres,
            'balance' => $balance,
            'initial_value' => $initial_value
        ]);
    }

    /**
     * Método para actualizar caja.
     */
    public function close(Request $request, Box $box)
    {
        $request->validate([
            'real_balance' => 'required|numeric|min:0',
        ]);

        $cashSession = CashSession::where(['box_id' => $box->id, 'state_box' => 'open'])
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$cashSession) {
            return response()->json([
                'success' => false,
                'message' => 'No hay caja abierta para cerrar.',
            ], 404);
        }

        // TODO Asumimos que el empleado que cierra la caja se obtiene del usuario autenticado.
        $closeEmployeeId = auth()->user()->employee_id ?? null;
        $company = Company::first();
        $general = Box::where('name', 'Caja General')->first();

        $cashgeneral = CashSession::where('box_id', $general->id)
            ->where('state_box', 'open')
            ->latest()
            ->first();
        $cashgeneral->increment('ingress', $request->initial_value);
        $cashgeneral->update([
            'balance' => $cashgeneral->initial_value + $cashgeneral->ingress - $cashgeneral->egress
        ]);

        $journalEntries = [];
        $journalEntries[] = [
            'account_id' => $general->account_id,
            'debit' => $request->balance,
            'have' => 0,
        ];
        $journalEntries[] = [
            'account_id' => $box->account_id,
            'debit' => 0,
            'have' => $request->balance,
        ];
        $description = "Cierre de caja ";
        $user = auth()->user();
        //fecha actual
        $date = Carbon::now();

        $inputs = [
            'description' => $description,
            'date' => $date,
            'user_id' => $user->id,
            'document_id' => $box->id,
            'table' => 'transaction_boxes',
        ];
        $journal = $company->journals()->create($inputs);
        $journal->journalentries()->createMany($journalEntries);
        $cashSession->update([
            ...$request->all(),
            'state_box' => 'close',
            'close_employee_id' => $closeEmployeeId,
        ]);

        //asiento cuando existe sobrante o faltante de dinero
        if ($request->balance === $request->real_balance)
            return;
        $journalEntries = [];
        if ($request->balance < $request->real_balance)//(50<60)
        {
            $movementType = MovementType::where(['code' => 'SC', 'company_id' => $company->id])->first();

            $journalEntries[] = [
                'account_id' => $box->account_id,
                'debit' => $request->cash_difference,
                'have' => 0,
            ];
            $journalEntries[] = [
                'account_id' => $movementType->account_id,
                'debit' => 0,
                'have' => $request->cash_difference,
            ];
            $description = "Sobrante en caja";

        } else {

            $movementType = MovementType::where(['code' => 'FC', 'company_id' => $company->id])->first();

            $journalEntries[] = [
                'account_id' => $movementType->account_id,
                'debit' => abs($request->cash_difference),
                'have' => 0,
            ];
            $journalEntries[] = [
                'account_id' => $box->account_id,
                'debit' => 0,
                'have' => abs($request->cash_difference),
            ];
            $description = "Faltante en caja";
        }

        $user = auth()->user();
        //fecha actual
        $date = Carbon::now();

        $inputs = [
            'description' => $description,
            'date' => $date,
            'user_id' => $user->id,
            'document_id' => $box->id,
            'table' => 'transaction_boxes',
        ];
        $journal = $company->journals()->create($inputs);
        $journal->journalentries()->createMany($journalEntries);
    }
}