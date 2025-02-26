<?php

namespace App\Http\Controllers\Landlord;

use App\Models\Landlord\Withholding as LandlordWithholding;
use App\Models\Withholding;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WithholdingController extends Controller
{
    // Método para mostrar los métodos de pago globales en la vista
    public function index()
    {
        $withholdings = Withholding::all(); // Obtiene todos los métodos de pago
        $excludedCodes = $withholdings->pluck('code'); // Extrae solo los códigos de PayMethod

        $globalWithholdings = LandlordWithholding::whereNotIn('code', $excludedCodes)->get(); // Filtra los que no están en PayMethod

        return Inertia::render('Business/General/Withholding/Index', [
            'withholdings' => $withholdings,
            'globalWithholdings' => $globalWithholdings,
        ]);
    }

    // Método para guardar en el tenant los métodos seleccionados
    public function store(Request $request)
    {
        Withholding::create(['code'=>$request->code,'name'=>$request->name,'percentage'=>$request->percentage,'type'=>$request->type]);
    
    }
}