<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Company;
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

    public function store(Request $request)
    {
        $request->validate([
      
            'reference'=> 'required|min:1|max:999',
            'description'=> 'required|min:1|max:999',
            'is_deductible'=> 'required',
        
        ]);

        $company= Company::first();

        $company->journals()->create($request->all());

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
