<?php

namespace App\Http\Controllers;

use App\Models\Landlord\Ice as LandlordIce;
use App\Models\Ice;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IceController extends Controller
{
    // Método para mostrar los métodos de pago globales en la vista
    public function index()
    {
        $globalIce = LandlordIce::all(); // Puedes usar paginate() si lo deseas
        $tenantCodes = Ice::pluck('code')->toArray();

        $globalIce = $globalIce->map(function ($method) use ($tenantCodes) {
            $method->selected = in_array($method->code, $tenantCodes);
            return $method;
        });

        return Inertia::render('Ice/Index', [
            'ices' => $globalIce,
        ]);
    }

    // Método para guardar en el tenant los métodos seleccionados
    public function store(Request $request)
    {
        $request->validate([
            'selected' => 'required|array',
        ]);

        // Obtener los códigos de los métodos de pago existentes en el tenant
        $ices = Ice::all()->pluck('code')->toArray();

        // Lista de códigos seleccionados desde el frontend
        $selectedIds = $request->input('selected');

        // Filtrar los códigos seleccionados que no están en la base de datos
        $selectedIds = array_diff($selectedIds, $ices);

        // Recuperar los métodos globales seleccionados que aún no están en el tenant
        $globalIces = LandlordIce::whereIn('code', $selectedIds)->get();

        // Insertar los métodos de pago nuevos en el tenant
        foreach ($globalIces as $ice) {
            Ice::create([
                'code' => $ice->code,
                'name' => $ice->name
            ]);
        }

        // **Eliminar los métodos de pago que ya están en el tenant pero no están en los seleccionados**
        Ice::whereNotIn('code', $request->input('selected'))->delete();
        //NOTA verificar el eliminar porque puede depender de otras tablas si depende solo eliminar [pr el sofdelete sino eliminar total]
    }
}