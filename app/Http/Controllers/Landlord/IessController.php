<?php

namespace App\Http\Controllers\Landlord;

use App\Models\Landlord\Iess as LandlordIess;
use App\Http\Controllers\Controller;
use App\Models\Iess;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IessController extends Controller
{
    // Método para mostrar los métodos de pago globales en la vista
    public function index()
    {

        $iess = Iess::all(); // Obtiene todos los métodos de pago
        $excludedCodes = $iess->pluck('code'); // Extrae solo los códigos de PayMethod

        $globalIess = LandlordIess::whereNotIn('code', $excludedCodes)->get(); // Filtra los que no están en PayMethod

        return Inertia::render('Business/General/Iess/Index', [
            'iesses' => $iess,
            'globalIess' => $globalIess,
        ]);
    }

    // Método para guardar en el tenant los métodos seleccionados
    public function store(Request $request)
    {
        Iess::create(['code'=>$request->code,'type'=>$request->type, 'name'=>$request->name,'percentage'=>$request->percentage]);
    }
}