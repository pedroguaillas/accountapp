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
use App\Services\TransactionService;
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
    
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function store(TransactionStoreBoxRequest $request)
    {
        return $this->transactionService->storeTransaction($request);
    }
}
