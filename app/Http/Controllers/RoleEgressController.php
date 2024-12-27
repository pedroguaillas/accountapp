<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\RoleEgress;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class RoleEgressController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::first();


        $egresses = RoleEgress::select("*")
            ->where('company_id', $company->id);

        $egresses = $egresses->paginate(10)->withQueryString(); // Importante usar withQueryString()

        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Business/Setting/RoleEgress', [
            'egresses' => $egresses,
        ]);
    }


    public function store(Request $request)
    {
        $company = Company::first();
        RoleEgress::create([...$request->all(), 'company_id' => $company->id]);
    }

    public function update(Request $request, RoleEgress $roleEgress)
    {
        $roleEgress->update($request->all());
    }

    public function destroy(int $egressId)
    {
        $egress = RoleEgress::findOrFail($egressId);
        $egress->delete(); // Esto usará SoftDeletes

    }

}
