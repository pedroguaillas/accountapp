<?php

namespace App\Http\Controllers\Landlord;

use App\Models\Landlord\Iva as LandlordIva;
use App\Models\Iva;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IvaController extends Controller
{
    // Método para mostrar los métodos de pago globales en la vista
    public function index()
    {
        $ivas = Iva::all(); // Obtiene todos los métodos de pago
        $excludedCodes = $ivas->pluck('code'); // Extrae solo los códigos de PayMethod

        $globalIvas = LandlordIva::whereNotIn('code', $excludedCodes)->get(); // Filtra los que no están en PayMethod

        return Inertia::render('Business/General/Iva/Index', [
            'ivas' => $ivas,
            'globalIvas' => $globalIvas,
        ]);
    }

    // Método para guardar en el tenant los métodos seleccionados
    public function store(Request $request)
    {
        Iva::create(['code'=>$request->code,'name'=>$request->name,'percentage'=>$request->percentage]);
    }
}