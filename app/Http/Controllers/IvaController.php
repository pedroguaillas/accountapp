<?php

namespace App\Http\Controllers;

use App\Models\Landlord\Iva as LandlordIva;
use App\Models\Iva;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IvaController extends Controller
{
    // Método para mostrar los métodos de pago globales en la vista
    public function index()
    {

        $globalIva = LandlordIva::all();
        $tenantCodes = Iva::pluck('code')->toArray();

        $globalIva = $globalIva->map(function ($method) use ($tenantCodes) {
            $method->selected = in_array($method->code, $tenantCodes);
            return $method;
        });

       
        return Inertia::render('Iva/Index', [
            'ivas' => $globalIva,
        ]);
    }

    // Método para guardar en el tenant los métodos seleccionados
    public function store(Request $request)
    {
        $request->validate([
            'selected' => 'required|array',
        ]);

        // Obtener los códigos de los métodos de pago existentes en el tenant
        $ivas = Iva::all()->pluck('code')->toArray();

        // Lista de códigos seleccionados desde el frontend
        $selectedIds = $request->input('selected');

        // Filtrar los códigos seleccionados que no están en la base de datos
        $selectedIds = array_diff($selectedIds, $ivas);

        // Recuperar los métodos globales seleccionados que aún no están en el tenant
        $globalIvas = LandlordIva::whereIn('code', $selectedIds)->get();

        // Insertar los métodos de pago nuevos en el tenant
        foreach ($globalIvas as $iva) {
            Iva::create([
                'code' => $iva->code,
                'name' => $iva->name,
                'fee' => $iva->fee
            ]);
        }

        // **Eliminar los métodos de pago que ya están en el tenant pero no están en los seleccionados**
        Iva::whereNotIn('code', $request->input('selected'))->delete();
        //NOTA verificar el eliminar porque puede depender de otras tablas si depende solo eliminar [pr el sofdelete sino eliminar total]
    }

}
