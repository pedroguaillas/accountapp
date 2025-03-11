<?php

namespace App\Http\Controllers\Landlord;

use App\Models\Landlord\MovementType as LandlordMovementType;
use App\Http\Controllers\Controller;
use App\Models\MovementType;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MovementTypeController extends Controller
{
    public function index()
    {
        $movementTypes = MovementType::all(); // Obtiene todos los métodos de pago
        $excludedCodes = $movementTypes->pluck('code'); // Extrae solo los códigos de PayMethod

        $globalMovementTypes = LandlordMovementType::whereNotIn('code', $excludedCodes)->get(); // Filtra los que no están en PayMethod

        return Inertia::render('Business/General/MovementType/Index', [
            'movementTypes' => $movementTypes,
            'globalMovementTypes' => $globalMovementTypes,
        ]);
    }

    // Método para guardar en el tenant los métodos seleccionados
    public function store(Request $request)
    {
        $company = Company::first();

        MovementType::create(['company_id' => $company->id,'code'=>$request->code,'type'=>$request->type, 'name'=>$request->name,'venue'=>$request->venue]);
    }
}