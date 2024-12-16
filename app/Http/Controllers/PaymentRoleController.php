<?php

namespace App\Http\Controllers;


use App\Models\Company;
use App\Models\PaymentRole;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentRoleController extends Controller
{
    public function index(Request $request)
    {
        // Obtener el término de búsqueda desde la solicitud
        $search = $request->input('search', ''); // Usar un valor por defecto vacío si no hay búsqueda
        $company = Company::first(); // Obtener la compañía (puedes ajustar esto según tu lógica)

        // Construir la consulta
        $paymentroles = PaymentRole::query()
            ->join('employees as e', 'e.id', '=', 'payment_roles.employee_id') // Relación con empleados
            ->where('payment_roles.company_id', $company->id) // Filtrar por ID de compañía
            ->when($search, function ($query, $search) {
                $query->where('e.name', 'LIKE', '%' . $search . '%'); // Filtrar por nombre
            })
            ->paginate(10); // Paginar resultados (10 por página)

        // Retornar la vista con los datos
        return Inertia::render('PaymentRol/Index', [
            'filters' => [
                'search' => $request->search,
            ],
            'paymentroles' => $paymentroles, // Cambiar el nombre para que coincida con Vue
        ]);
    }

}
