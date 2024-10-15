<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ContributorType;
use App\Models\EconomicActivity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::all();
        $economyActivities = EconomicActivity::all();
        $contributorTypes = ContributorType::all();

        return Inertia::render('Company/Index', [
            'companies' => $companies,
            'economyActivities' => $economyActivities,
            'contributorTypes' => $contributorTypes,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ruc' => 'required|min:13|max:13',
            'company' => 'required|min:3|max:300',
            'economic_activity_id' => 'required',
            'contributor_type_id' => 'required',
        ]);

        Company::create($request->all());
    }
}
