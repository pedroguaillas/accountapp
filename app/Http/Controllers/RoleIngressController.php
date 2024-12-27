<?php

namespace App\Http\Controllers;

use App\Models\RoleIngress;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleIngressController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::first();
        // Construimos la consulta base
        $ingreses = RoleIngress::select("*")
            ->where('company_id', $company->id)
            ->whereNull('deleted_at');
        $ingreses = $ingreses->paginate(20)->withQueryString(); // Importante usar withQueryString()

        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Business/Setting/RoleIngress', [
            'ingresses' => $ingreses,
        ]);
    }


    public function store(Request $request)
    {
        $company = Company::first();
        RoleIngress::create([...$request->all(), 'company_id' => $company->id]);
    }

    public function update(Request $request, RoleIngress $roleingress)
    {
        $roleingress->update($request->all());
    }

    public function destroy(int $ingressId)
    {
        $ingress =RoleIngress::findOrFail($ingressId);
        $ingress->delete(); // Esto usará SoftDeletes
    }

}
