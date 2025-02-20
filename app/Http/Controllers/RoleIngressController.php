<?php

namespace App\Http\Controllers;

use App\Models\PaymentRoleIngress;
use App\Models\RoleIngress;
use App\Models\Company;
use App\Models\PaymentRole;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleIngressController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::first();
        $search = $request->input('search', '');

        $ingreses = RoleIngress::where('company_id', $company->id)
            ->whereNull('deleted_at')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'LIKE', "%$search%")
                        ->orWhere('name', 'LIKE', "%$search%");
                });
            })
            ->paginate(20)
            ->withQueryString();

        // Renderizamos la vista con los datos necesarios
        return inertia('Business/Setting/RoleIngress', [
            'ingresses' => $ingreses,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }


    public function store(Request $request)
    {
        $company = Company::first();
        $roleingress = RoleIngress::create([...$request->all(), 'company_id' => $company->id]);

        $paymentRoles = PaymentRole::where('company_id', $company->id)->get();

        // Insertar cada RoleEgress en los PaymentRole
    //     $data = [
    //         "company_id" => $company->id,
    //     ];
    //     $inputpaymentroleingress = [];
    //     foreach ($paymentRoles as $paymentRole) {
    //         $inputpaymentroleingress[] = [
    //             'payment_role_id' => $paymentRole->id,
    //             'role_ingress_id' => $roleingress->id,
    //             'value' => 0,
    //             'data_additional' => json_encode($data),
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ];
    //     }
    //     PaymentRoleIngress::insert($inputpaymentroleingress);
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
