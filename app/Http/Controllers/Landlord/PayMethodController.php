<?php

namespace App\Http\Controllers\Landlord;

use App\Models\Landlord\PayMethod as LandlordPayMethod;
use App\Models\PayMethod;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PayMethodController extends Controller
{
    // Método para mostrar los métodos de pago globales en la vista
    public function index()
    {
        $payMethods = PayMethod::all(); // Obtiene todos los métodos de pago
        $excludedCodes = $payMethods->pluck('code'); // Extrae solo los códigos de PayMethod

        $globalMethods = LandlordPayMethod::whereNotIn('code', $excludedCodes)->get(); // Filtra los que no están en PayMethod

        return Inertia::render('Business/General/PayMethod/Index', [
            'payMethods' => $payMethods,
            'globalPayMethods' => $globalMethods
        ]);
    }

    // Método para guardar en el tenant los métodos seleccionados
    public function store(Request $request)
    {
        PayMethod::create(['code'=>$request->code, 'name'=>$request->name,'max'=>$request->max]);
    }
}