<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\CashSession;
use App\Models\Company;
use App\Models\Employee;
use App\Models\TransactionBox;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BoxController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::first();
        $search = $request->input('search', '');

        $boxes = Box::query()
            ->select('boxes.id', 'boxes.name', 'employees.name as employee_name', 'cash_sessions.state_box')
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
            'name' => 'nullable|min:3|max:300',
        ]);
        //llamada a la compania
        $company = Company::first();

        //creacion de los estableicmientos 
        $company->boxes()->create($request->all());
    }

    public function update(Request $request, Box $box)
    {
        $request->validate([
            'name' => 'nullable|min:3|max:300',
        ]);

        //actulizar los establecimientos
        $box->update($request->all());
    }

    public function destroy(Box $box)
    {
        $box->delete(); // Esto usará SoftDeletes

        return response()->json([
            'success' => true,
            'message' => 'Caja eliminado correctamente.',
        ]);
    }

    public function open(Request $request, Box $box)
    {
        $request->validate([
            'initial_value' => 'required|numeric|min:0',
        ]);

        $box->cashSessions()->create([
            'open_employee_id' => 1,//TODO revisar id
            'close_employee_id' => null,
            'initial_value' => $request->initial_value,
            'ingress' => 0,
            'egress' => 0,
            'balance' => $request->initial_value,
            'state_box' => 'open',//poner en espaniol
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Caja abierta correctamente.',
        ]);
    }

    //metodo para traer datos de la seccion de cjas y las transacciones
    public function closeinformation(Box $box)
    {
        //TODO analizar en el caso que se elimine la caja por la foreing key presente en las migraciones
        $boxe = CashSession::where('box_id', $box->id)
            ->orderBy('id', 'desc')
            ->first();

        $ingres = (float) (TransactionBox::where('cash_session_id', $boxe->id)
            ->where('type', 'Ingreso')
            ->sum('amount') ?? 0);

        $egres = (float) (TransactionBox::where('cash_session_id', $boxe->id)
            ->where('type', 'Egreso')
            ->sum('amount') ?? 0);

        $initial_value = (float) $boxe->initial_value;  // Si initial_value también viene como string

        $balance = ($ingres + $boxe->initial_value) - $egres;

        // Retornar los datos como JSON
        return response()->json([
            'ingres' => $ingres,
            'egres' => $egres,
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
            'ingress' => 'required|numeric|min:0',
            'egress' => 'required|numeric|min:0',
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

        $ingress = $request->input('ingress');
        $egress = $request->input('egress');
        $initialValue = $cashSession->initial_value;
        $balance = $initialValue + $ingress - $egress;

        // TODO Asumimos que el empleado que cierra la caja se obtiene del usuario autenticado.
        $closeEmployeeId = auth()->user()->employee_id ?? null;

        $cashSession->update([
            'ingress' => $ingress,
            'egress' => $egress,
            'balance' => $balance,
            'close_employee_id' => $closeEmployeeId,
            'state_box' => 'close',//cerrar
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Caja cerrada correctamente.',
        ]);
    }
}