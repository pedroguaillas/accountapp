<?php

namespace App\Http\Controllers\Landlord;

use App\Models\Landlord\Ice as LandlordIce;
use App\Http\Controllers\Controller;
use App\Models\Ice;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IceController extends Controller
{
    // Método para mostrar los métodos de pago globales en la vista
    public function index()
    {
        $ices = Ice::all(); // Obtiene todos los métodos de pago
        $excludedCodes = $ices->pluck('code'); // Extrae solo los códigos de PayMethod

        $globalIces = LandlordIce::whereNotIn('code', $excludedCodes)->get(); // Filtra los que no están en PayMethod

        return Inertia::render('Business/General/Ice/Index', [
            'ices' => $ices,
            'globalIces' => $globalIces,
        ]);

    }

    // Método para guardar en el tenant los métodos seleccionados
    public function store(Request $request)
    {
        Ice::create(['code'=>$request->code,'name'=>$request->name,'percentage'=>$request->percentage]);
    }
}