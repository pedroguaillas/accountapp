<?php

namespace App\Http\Controllers;

use App\Models\TransactionBox;
use App\Models\CashSession;
use App\Models\MovementType;
use App\Models\Person;
use App\Models\Company;
use App\Http\Requests\TransactionStoreBoxRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

    public function store(TransactionStoreBoxRequest $request)
    {
        $company = Company::first();

        $data = ["company_id" => $company->id];

        // Crear la transacción con los datos recibidos
        TransactionBox::create([...$request->all(), 'data_additional' => $data]);
    }

    public function edit(int $transactionId)
    {
        $transaction = TransactionBox::findOrFail($transactionId);
        $cash = CashSession::select('cash_sessions.id', 'cash_sessions.box_id', 'boxes.name')
            ->join('boxes', 'boxes.id', '=', 'cash_sessions.box_id') // Aquí corriges la relación
            ->where('cash_sessions.state_box', 'open')
            ->get();

        return Inertia::render('TransactionBox/Edit', [
            'transaction' => $transaction,
            'cashSessions' => $cash
        ]);
    }

    public function update(Request $request, TransactionBox $transaction)
    {
        $transaction->update($request->all());
    }

    public function destroy(TransactionBox $transaction)
    {
        $transaction->delete();
    }
}
