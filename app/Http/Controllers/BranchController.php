<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
       

        return Inertia::render('Branch/Index', [
            'branches' => $branches,
      
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|min:1|max:999',
            'name' => 'nullable|min:3|max:300',
            'city' => 'required',
            'address' => 'required',
            //'is_matriz' => '',
            'enviroment_type' => 'required',

        ]);

        $company= Company::first();

        $company->branches()->create($request->all());

    }

    public function update(Request $request,Branch $branch)
    {
        $request->validate([
            'number' => 'required|min:1|max:999',
            'name' => 'nullable|min:3|max:300',
            'city' => 'required',
            'address' => 'required',
            //'is_matriz' => '',
            'enviroment_type' => 'required',
        ]);
        if($request->is_matriz)
        {
            $company= Company::first();

            $company->branches()->update(['is_matriz'=>false]);
        }
        
        $branch->update($request->all());
    }

    public function delete(Request $request,Branch $branch)
    {
        //no tenga relacion el establecimiento con otras
        //if ()
        //
        //else
        //return(response)
        $branch->delete();
    }
}
