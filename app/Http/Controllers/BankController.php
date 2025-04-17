<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::first();
        $search = $request->input('search', '');
        // Construimos la consulta base
        $banks = Bank::query()
            ->where('company_id', $company->id)
            ->when($search, function ($query, $search) {
                $query->whereRaw("LOWER(banks.name) LIKE ?", ["%" . strtolower($search) . "%"]);
            })->paginate(10)
            ->withQueryString();

        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Bank/Index', [
            'filters' => [
                'search' => $search, // Retornar el filtro de búsqueda
            ],
            'banks' => $banks,
        ]);
    }

    public function store(Request $request)
    {
        //validacion de datos
        $request->validate([
            'name' => 'required|min:3|max:300',
        ]);
        //llamada a la compania
        $company = Company::first();

        //creacion de los estableicmientos 
        $company->banks()->create($request->all());
    }

    public function update(Request $request, Bank $bank)
    {
        //validacion de datos
        $request->validate([
            'name' => 'required|min:3|max:300',
        ]);
        //actulizar los establecimientos
        $bank->update($request->all());
    }

    public function destroy(Bank $bank)
    {
        $bank = Bank::findOrFail($bank->id);
        $bank->delete();

        return redirect()->route('banks.index');
    }

    public function updateState($id)
    {
        $bank = Bank::findOrFail($id);
        $bank->state = !$bank->state; // Toggle the state
        $bank->save();

        return response()->json(['success' => true]);
    }
}