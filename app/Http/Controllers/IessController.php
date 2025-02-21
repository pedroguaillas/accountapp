<?php

namespace App\Http\Controllers;

use App\Models\Landlord\Iess as LandlordIess;
use App\Models\Iess;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IessController extends Controller
{
    // Método para mostrar los métodos de pago globales en la vista
    public function index()
    {
        $globalIess = LandlordIess::all(); // Puedes usar paginate() si lo deseas
        $tenantCodes = Iess::pluck('id')->toArray();
        $globalIess = $globalIess->map(function ($method) use ($tenantCodes) {
            $method->selected = in_array($method->id, $tenantCodes);
            return $method;
        });

        return Inertia::render('Business/General/Iess/Index', [
            'iess' => $globalIess,
        ]);
    }

    // Método para guardar en el tenant los métodos seleccionados
    public function store(Request $request)
    {
        $request->validate([
            'selected' => 'required|array',
        ]);

        // Obtener los códigos de los métodos de pago existentes en el tenant
        $iess = Iess::all()->pluck('id')->toArray();

        // Lista de códigos seleccionados desde el frontend
        $selectedIds = $request->input('selected');

        // Filtrar los códigos seleccionados que no están en la base de datos
        $selectedIds = array_diff($selectedIds, $iess);

        // Recuperar los métodos globales seleccionados que aún no están en el tenant
        $globalIess = LandlordIess::whereIn('id', $selectedIds)->get();


        // Insertar los métodos de pago nuevos en el tenant
        foreach ($globalIess as $method) {
            Iess::create([
                'type' => $method->type,
                'name' => $method->name,
                'percentage' => $method->percentage,
            ]);
        }

        // **Eliminar los métodos de pago que ya están en el tenant pero no están en los seleccionados**
        Iess::whereNotIn('id', $request->input('selected'))->delete();
        //NOTA verificar el eliminar porque puede depender de otras tablas si depende solo eliminar [pr el sofdelete sino eliminar total]
    }
}