<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\FixedAsset;
use App\Models\PayMethod;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FixedAssetController extends Controller
{
    public function index()
    {
        $company = Company::first();
        $fixedAssets = FixedAsset::where('company_id', $company->id)->get();

        return Inertia::render('FixedAsset/Index', [
            'fixedAssets' => $fixedAssets,

        ]);
    }


    public function create()
    {
        $payMethods = PayMethod::all();

        return Inertia::render('FixedAsset/Create', [
            'payMethods' => $payMethods,
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

        $company = Company::first();

        $company->branches()->create($request->all());

    }


    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'number' => 'required|min:1|max:999',
            'name' => 'nullable|min:3|max:300',
            'city' => 'required',
            'address' => 'required',
            //'is_matriz' => '',
            'enviroment_type' => 'required',
        ]);
        if ($request->is_matriz) {
            $company = Company::first();

            $company->branches()->update(['is_matriz' => false]);
        }

        $branch->update($request->all());
    }

    public function delete(Request $request, Branch $branch)
    {
        //no tenga relacion el establecimiento con otras
        //if ()
        //
        //else
        //return(response)
        $branch->delete();
    }
}
