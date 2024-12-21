<?php

namespace App\Http\Controllers;


use App\Models\Company;
use App\Models\PaymentRole;
use App\Models\PaymentRoleIngress;
use App\Models\RoleIngress;
use App\Jobs\ProcessPaymenRole;
use App\Models\RoleEgress;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentRoleController extends Controller
{
    public function index(Request $request)
    {
        // Obtener el término de búsqueda desde la solicitud
        $search = $request->input('search', ''); // Usar un valor por defecto vacío si no hay búsqueda
        $company = Company::first(); // Obtener la compañía (puedes ajustar esto según tu lógica)

        $paymentRole= new PaymentRole();
        $job = new ProcessPaymenRole($paymentRole);
        $job->handle();


        // Construir la consulta
        $paymentroles = PaymentRole::query()
            ->rightJoin('employees as e', 'e.id', '=', 'payment_roles.employee_id') // Relación con empleados
            // ->rightJoin('payment_role_ingresses as pi','pi.payment_role_id','=','payment_roles.id')
            // ->rightJoin('role_ingresses as ri','ri.id','=','pi.payment_role_id')

            //->where('payment_roles.company_id', $company->id) // Filtrar por ID de compañía
            ->when($search, function ($query, $search) {
                $query->where('e.name', 'LIKE', '%' . $search . '%'); // Filtrar por nombre
            })
            ->paginate(10); // Paginar resultados (10 por página)

        $roleingress = RoleIngress::where('company_id', $company->id)->get();

        $roleegress = RoleEgress::where('company_id', $company->id)->get();

        // Retornar la vista con los datos
        return Inertia::render('PaymentRol/Index', [
            'filters' => [
                'search' => $request->search,
            ],
            'paymentroles' => $paymentroles, // Cambiar el nombre para que coincida con Vue
            'roleingress' => $roleingress,
            'roleegress' => $roleegress,
        ]);
    }

}
