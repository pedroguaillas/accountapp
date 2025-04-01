<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function index()
    {
        $company = Company::first();
        $expens = Expense::where('company_id', $company->id)->get(); // Obtiene todos los métodos de pago 
        
        return Inertia::render('Business/General/Expens/Index', [
            'expenses' => $expens,
        ]);
    }

    // Método para guardar en el tenant los métodos seleccionados
    public function store(Request $request)
    {
        $company = Company::first();

        Expense::create(['company_id' => $company->id, 'name' => $request->name]);
    }
}