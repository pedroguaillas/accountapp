<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Company;
use App\Models\Account;
use App\Models\CostCenter;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JournalController extends Controller
{
    public function index()
    {
        $journals = Journal::all();


        return Inertia::render('Journal/Index', [
            'journals' => $journals,

        ]);
    }


    public function create()
    {
        $company = Company::first();
        $accounts = Account::select('id', 'code', 'name')->where('company_id', $company->id)->get();
        $costCenters = CostCenter::select('id', 'code', 'name')->where('company_id', $company->id)->get();


        return Inertia::render('Journal/Create', [
            'accounts' => $accounts,
            'costCenters' => $costCenters,

        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|min:1|max:999',
            'journalEntries.*.account_id' => 'required|min:1|max:999', // validar que exista esa cuenta 
            'journalEntries.*.debit' => 'required|min:0',
            'journalEntries.*.have' => 'required|min:0',
            // 'is_deductible'=> 'required',
        ]);

        $company = Company::first();
        $user = auth()->user();

        $inputs = [
            ...$request->except('journalEntries'),
            'user_id' => $user->id, // Aquí se asigna el ID del usuario autenticado
        ];

        $journal = $company->journals()->create($inputs);

        $requestJournalEntries = $request->journalEntries;
        $journalEntries = [];

        foreach ($requestJournalEntries as $journalEntry) {
            $journalEntries[] = [
                'account_id' => $journalEntry['account_id'],

                'debit' => $journalEntry['debit'],
                'have' => $journalEntry['have'],
            ];
        }

        $journal->journalentries()->createMany($journalEntries);
        
       // return Inertia::render('journal.index');

        return to_route('journal.index');

    }
    public function delete(Request $request, Journal $journal)
    {

        $journal->delete();
    }
}
