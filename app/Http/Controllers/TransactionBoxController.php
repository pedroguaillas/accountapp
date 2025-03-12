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
            return redirect()->route('journal.create')->withErrors([
                'warning' => 'Debes crear el asiento inicial para continuar con el proceso. Por favor, regístralo antes de avanzar.',
            ]);
        }
        $boxAccountValidate =Box::whereNull('account_id')
        ->where('boxes.company_id', $company->id)
        ->get();

        if ($boxAccountValidate->count() > 0) {

            return redirect()->route('setting.account.box.index')->withErrors([
                'warning' => 'Debes vincular las cuentas para continuar con el proceso. Por favor, vinculalas antes de avanzar.',
            ]);
        }
           
        $movementTypeValidate = MovementType::whereNull('account_id')
            ->where(function ($query) {
                $query->where('venue', 'ambos')
                    ->orWhere('venue', 'caja');
            })
            ->get();

        if ($movementTypeValidate->count() > 0) {
            return redirect()->route('setting.account.box.index')->withErrors([
                'warning' => 'Debes vincular las cuentas para continuar con el proceso. Por favor, vinculalas antes de avanzar.',
            ]);
        }

        // usuario autentificado
        $user = auth()->user();
        //fecha actual
        $date = Carbon::now();

        $journalEntries = [];
        $data = ["company_id" => $company->id];

        // Crear la transacción con los datos recibidos
        $transaction= TransactionBox::create([...$transactionStoreBoxRequest->all(), 'data_additional' => $data]);
        $movementType= MovementType::find($transactionStoreBoxRequest->movement_type_id);
        
        $cash=CashSession::find($transaction->cash_session_id);
        $box=Box::find($cash->box_id); 

        if ($movementType->type === 'Ingreso') {
            $journalEntries[] = [
                'account_id' => $box->account_id,
                'debit' => $transaction->amount,
                'have' => 0,
            ];
            $journalEntries[] = [
                'account_id' => $movementType->account_id,
                'debit' => 0,
                'have' => $transaction->amount,
            ];
        } else {
            $journalEntries[] = [
                'account_id' => $movementType->account_id,
                'debit' => $transaction->amount,
                'have' => 0,
            ];
            $journalEntries[] = [
                'account_id' => $box->account_id,
                'debit' => 0,
                'have' => $transaction->amount,
            ];
        }
        // Crear el diario
        $inputs = [
            'description' => "Movimiento Caja " . $transaction->description,
            'date' => $date,
            'user_id' => $user->id,
            'document_id' => $transaction->id,
            'table' => 'transaction_boxes',
        ];

        $journal = $company->journals()->create($inputs);
        $journal->journalentries()->createMany($journalEntries);

    }
}
