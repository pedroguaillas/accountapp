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

        if ($request->has('search') && !empty($request->search)) {
            $ingreses->where('code', 'LIKE', '%' . $request->search . '%')
                ->orWhere('name', 'LIKE', '%' . $request->search . '%');
        }

        $ingreses = $ingreses->paginate(20)->withQueryString(); // Importante usar withQueryString()

        // Renderizamos la vista con los datos necesarios
        return inertia('Business/Setting/RoleIngress', [
            'ingresses' => $ingreses,
            'filters' => $request->search,
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
        $ingress = RoleIngress::findOrFail($ingressId);
        $ingress->delete(); // Esto usará SoftDeletes
    }

}
