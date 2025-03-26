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

        $cash = CashSession::select('cash_sessions.id', 'cash_sessions.box_id', 'boxes.name', 'boxes.type as box_type')
            ->join('boxes', 'boxes.id', '=', 'cash_sessions.box_id') // Aquí corriges la relación
            ->where('cash_sessions.state_box', 'open')
            ->get();

        $movementtypes = MovementType::where('company_id', $company->id)
            ->where(function ($query) {
                $query->where('venue', 'ambos')
                    ->orWhere('venue', 'caja');

            })
            ->whereNotIn('code', ['FC', 'SC', 'AEU', 'AES'])
            ->get();

        $peopleCount = Person::count();
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
        $boxAccountValidate = Box::whereNull('account_id')
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
            ->whereNotIn('code', ['RCC', 'PF', 'DCG', 'RFA'])
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
        $transaction = TransactionBox::create([...$transactionStoreBoxRequest->all(), 'data_additional' => $data]);
        $movementType = MovementType::find($transactionStoreBoxRequest->movement_type_id);

        $cash = CashSession::find($transaction->cash_session_id);
        $box = Box::find($cash->box_id);

        if ($box->type === 'general') {
            if (($movementType->type === 'Ingreso')) {
                $cash->increment('ingress', $transaction->amount);
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
                if (($movementType->type === 'Egreso') && !in_array($movementType->code, ['RCC', 'PF'])) {
                    $cash->increment('egress', $transaction->amount);
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
                } else {
                    if ($movementType->code === 'RCC') {
                        $cash->increment('egress', $transaction->amount);
                        $boxAux = Box::find($transaction->box_id);
                        $cashSessionAux = CashSession::where('box_id', $transaction->box_id)
                            ->orderByDesc('id')
                            ->first();
                        $cashSessionAux->increment('ingress', $transaction->amount);
                        $cashSessionAux->update([
                            'balance' => $cashSessionAux->initial_value + $cashSessionAux->ingress - $cashSessionAux->egress
                        ]);
                        $journalEntries[] = [
                            'account_id' => $boxAux->account_id,
                            'debit' => $transaction->amount,
                            'have' => 0,
                        ];
                        $journalEntries[] = [
                            'account_id' => $box->account_id,
                            'debit' => 0,
                            'have' => $transaction->amount,
                        ];
                    } else {
                        if ($movementType->code === 'PF') {
                            $cash->increment('egress', $transaction->amount);
                            $boxAux = Box::find($transaction->box_id);
                            $cashSessionAux = CashSession::where('box_id', $boxAux->id)
                                ->latest()
                                ->first();
                            $cashSessionAux->increment('ingress', $transaction->amount);
                            $cashSessionAux->update([
                                'balance' => $cashSessionAux->initial_value + $cashSessionAux->ingress - $cashSessionAux->egress
                            ]);
                            $journalEntries[] = [
                                'account_id' => $boxAux->account_id,
                                'debit' => $transaction->amount,
                                'have' => 0,
                            ];
                            $journalEntries[] = [
                                'account_id' => $box->account_id,
                                'debit' => 0,
                                'have' => $transaction->amount,
                            ];
                        }
                    }
                }
            }
        } else {
            if ($box->type === 'chica') {
                if ($movementType->code === 'RFA') {
                    $cash->increment('egress', $transaction->amount);
                    $boxAux = Box::find($transaction->box_id);
                    $cashSessionAux = CashSession::where('box_id', $boxAux->id)
                        ->latest()
                        ->first();
                    $cashSessionAux->increment('ingress', $transaction->amount);
                    $cashSessionAux->update([
                        'balance' => $cash->initial_value + $cash->ingress - $cash->egress
                    ]);
                    $journalEntries[] = [
                        'account_id' => $boxAux->account_id,
                        'debit' => $transaction->amount,
                        'have' => 0,
                    ];
                    $journalEntries[] = [
                        'account_id' => $box->account_id,
                        'debit' => 0,
                        'have' => $transaction->amount,
                    ];
                } else {
                    //TODO else por los gastos difetente del reintegro(agua, luz,(servicios basicos o gastos pequenios todo con documento))
                }
            } else if ($box->type === 'otras') {
                if ($movementType->code === 'DCG') {
                    $cash->increment('egress', $transaction->amount);
                    $boxAux = Box::find($transaction->box_id);
                    $cashSessionAux = CashSession::where('box_id', $boxAux->id)
                        ->latest()
                        ->first();
                    $cashSessionAux->increment('ingress', $transaction->amount);
                    $cashSessionAux->update([
                        'balance' => $cash->initial_value + $cash->ingress - $cash->egress
                    ]);
                    $journalEntries[] = [
                        'account_id' => $boxAux->account_id,
                        'debit' => $transaction->amount,
                        'have' => 0,
                    ];
                    $journalEntries[] = [
                        'account_id' => $box->account_id,
                        'debit' => 0,
                        'have' => $transaction->amount,
                    ];
                }

            }
        }


        // Crear el diario
        $inputs = [
            'description' => "Movimiento Caja " . $transaction->description,
            'date' => $date,
            'user_id' => $user->id,
            'document_id' => $transaction->id,
            'table' => 'transaction_boxes',
        ];

        $cash->update([
            'balance' => $cash->initial_value + $cash->ingress - $cash->egress
        ]);
        $journal = $company->journals()->create($inputs);
        $journal->journalentries()->createMany($journalEntries);

    }
}
