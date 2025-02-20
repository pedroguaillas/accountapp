<?php

namespace App\Http\Controllers;

use App\Models\Landlord\PayMethod as LandlordPayMethod;
use App\Models\PayMethod;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PayMethodController extends Controller
{
    // Método para mostrar los métodos de pago globales en la vista
    public function index()
    {
        $globalMethods = LandlordPayMethod::all(); // Puedes usar paginate() si lo deseas
        $tenantCodes = PayMethod::pluck('code')->toArray();

        $globalMethods = $globalMethods->map(function ($method) use ($tenantCodes) {
            $method->selected = in_array($method->code, $tenantCodes);
            return $method;
        });

        return Inertia::render('Business/General/PayMethod/Index', [
            'paymethods' => $globalMethods,
        ]);
    }

    // Método para guardar en el tenant los métodos seleccionados
    public function store(Request $request)
    {
        $request->validate([
            'selected' => 'required|array',
        ]);

        // Obtener los códigos de los métodos de pago existentes en el tenant
        $paymethods = PayMethod::all()->pluck('code')->toArray();

        // Lista de códigos seleccionados desde el frontend
        $selectedIds = $request->input('selected');

        // Filtrar los códigos seleccionados que no están en la base de datos
        $selectedIds = array_diff($selectedIds, $paymethods);

        // Recuperar los métodos globales seleccionados que aún no están en el tenant
        $globalMethods = LandlordPayMethod::whereIn('code', $selectedIds)->get();

        // Insertar los métodos de pago nuevos en el tenant
        foreach ($globalMethods as $method) {
            PayMethod::create([
                'code' => $method->code,
                'name' => $method->name
            ]);
        }

        // **Eliminar los métodos de pago que ya están en el tenant pero no están en los seleccionados**
        PayMethod::whereNotIn('code', $request->input('selected'))->delete();
        //NOTA verificar el eliminar porque puede depender de otras tablas si depende solo eliminar [pr el sofdelete sino eliminar total]
    }
}