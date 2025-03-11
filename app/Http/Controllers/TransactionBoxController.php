<?php

namespace App\Http\Controllers;

use App\Models\TransactionBox;
use App\Models\CashSession;
use App\Models\Box;
use App\Models\MovementType;
use App\Models\Journal;
use App\Models\Person;
use App\Models\Company;
use App\Http\Requests\TransactionStoreBoxRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class TransactionBoxController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $transactionBoxes = TransactionBox::query()
            ->select('transaction_boxes.*', 'boxes.name')
            ->join('cash_sessions', 'cash_sessions.id', '=', 'transaction_boxes.cash_session_id')
            ->join('boxes', 'boxes.id', '=', 'cash_sessions.box_id') // Aquí corriges la relación
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('TransactionBox/Index', [
            'filters' => ['search' => $search],
            'transactionboxes' => $transactionBoxes,
        ]);
    }

    public function create()
    {
        $company = Company::first();

        $cash = CashSession::select('cash_sessions.id', 'cash_sessions.box_id', 'boxes.name')
            ->join('boxes', 'boxes.id', '=', 'cash_sessions.box_id') // Aquí corriges la relación
            ->where('cash_sessions.state_box', 'open')
            ->get();
        $movementtypes = MovementType::where('company_id', $company->id)
            ->where(function ($query) {
                $query->where('venue', 'ambos')
                    ->orWhere('venue', 'caja');
            })
            ->get();

        $peopleCount = Person::count();
        $people = [];
        $people = Person::where('company_id', $company->id)->get();

        return Inertia::render('TransactionBox/Create', [
            'cashSessions' => $cash,
            'movementtypes' => $movementtypes,
            'people' => $people,
            'countperson' => $peopleCount,
        ]);
    }

    public function store(TransactionStoreBoxRequest $transactionStoreBoxRequest)
    {
        //TODO validar el monto en trasactionStoreBoxRequest
        $company = Company::first();

        $journal = Journal::where('description', 'ASIENTO DE SITUACION INICIAL')
            ->where('company_id', $company->id)
            ->first();

        if ($journal === null) {
            return to_route('journal.create');
        }
        // usuario autentificado
        $user = auth()->user();
        //fecha actual
        $date = Carbon::now();


        $data = ["company_id" => $company->id];

        // Crear la transacción con los datos recibidos
        TransactionBox::create([...$transactionStoreBoxRequest->all(), 'data_additional' => $data]);

        $movementtype= MovementType::find($transactionStoreBoxRequest->movement_type_id);

        // Actualizar el saldo de la cuenta bancaria restando el monto de la transacción
        
        $transaction = TransactionBox::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        $cash=CashSession::find($transaction->cash_session_id);
        $box=Box::find($cash->box_id); 
      
        $movementtype=MovementType::find($transaction->movement_type_id);

    }
}
