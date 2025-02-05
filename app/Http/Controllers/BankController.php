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

        // Construimos la consulta base
        $banks = Bank::query()
            ->where('company_id', $company->id);

        $search = $request->input('search', '');

        $banks->where('name', 'LIKE', '%' . $search . '%');
        // Paginamos los resultados y preservamos los filtros en la URL
        $banks = $banks->paginate(10)->withQueryString();



        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Bank/Index', [
            'filters' => $request->search,
            'banks' => $banks,
        ]);
    }


    public function store(Request $request)
    {
        //validacion de datos
        $request->validate([
            'name' => 'nullable|min:3|max:300',
        ]);
        //llamada a la compania
        $company = Company::first();

        //creacion de los estableicmientos 
        $company->banks()->create($request->all());

    }

    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'name' => 'nullable|min:3|max:300',
        ]);

        //actulizar los establecimientos
        $bank->update($request->all());
    }

    public function destroy(Bank $bank)
    {
        $bank->delete(); // Esto usará SoftDeletes

        return response()->json([
            'success' => true,
            'message' => 'Banco eliminado correctamente.',
        ]);
    }

    public function updateState($id)
    {
        $bank = Bank::findOrFail($id);
        $bank->state = !$bank->state; // Toggle the state
        $bank->save();

        return response()->json(['success' => true]);
    }
}
