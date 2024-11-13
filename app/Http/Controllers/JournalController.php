<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Company;
use App\Models\Account;
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
        $accounts = Account::all();
       

        return Inertia::render('Journal/Create', [
            'accounts' => $accounts,
      
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
      
        
            'description'=> 'required|min:1|max:999',
            'is_deductible'=> 'required',
        
        ]);

    
        $company= Company::first();

        $journal=$company->journals()->create($request->except('journalEntries'));
        $requestJournalEntries=$request->journalEntries;
       // $requestJunrnalEntries=json_decode(json_encode($requestJournalEntries));
        $journalEntries=[];
        foreach($requestJournalEntries as $journalEntry){ 
            $journalEntries[]=[ 
                'account_id'=>$journalEntry->account_id, //los atributos
              //  'account_id'=>$journalEntry['account_id'],//puede ser la otra forma
            ];
        }
        $journal->journalentries()->createMany($journalEntries);
    }

    public function update(Request $request,Journal $journal)
    {
        $request->validate([
            'reference'=> 'required|min:1|max:999',
            'description'=> 'required|min:1|max:999',
            'is_deductible'=> 'required',
        ]);
     
        $journal->update($request->all());
    }

    public function delete(Request $request,Journal $journal)
    {
      
        $journal->delete();
    }
}
