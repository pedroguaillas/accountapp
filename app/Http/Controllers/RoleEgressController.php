<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\RoleEgress;
use App\Models\PaymentRole;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleEgressController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::first();
        $search = $request->input('search', '');

        $egresses = RoleEgress::where('company_id', $company->id)
            ->whereNull('deleted_at')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'LIKE', "%$search%")
                        ->orWhere('name', 'LIKE', "%$search%");
                });
            })
            ->paginate(10)
            ->withQueryString();

        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Business/Setting/RoleEgress', [
            'egresses' => $egresses,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $company = Company::first();
        RoleEgress::create([...$request->all(), 'company_id' => $company->id]);
    }

    public function update(Request $request, RoleEgress $roleegress)
    {
        $roleegress->update($request->all());
    }

    public function destroy(int $egressId)
    {
        $egress = RoleEgress::findOrFail($egressId);
        $egress->delete(); // Esto usará SoftDeletes
    }
}