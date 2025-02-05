<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Bank;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BankAccountController extends Controller
{
    public function index(Request $request, Bank $bank)
    {
        $company = Company::first();

        // Construimos la consulta base
        $bankaccounts = BankAccount::query()
            ->where('company_id', $company->id)
            ->where('bank_id',$bank->id);

        // Aplicamos el filtro si existe
        if ($request->has('search') && !empty($request->search)) {
            $bankaccounts->where('name', 'LIKE', '%' . $request->search . '%');
        }

        // Paginamos los resultados y preservamos los filtros en la URL
        $bankaccounts = $bankaccounts->paginate(10)->withQueryString();

        // Renderizamos la vista con los datos necesarios
        return Inertia::render('BankAccount/Index', [
            'filters' => [
                'search' => $request->search, // Retornar el filtro de código
            ],
            'bankaccounts' => $bankaccounts,
            'bank_id' => $bank->id,
        ]);
    }


    public function store(Request $request)
    {
        //validacion de datos
        $request->validate([
            'account_number' => 'required|min:1|max:999',
        ]);
        //llamada a la compania
        $company = Company::first();

        //creacion de los estableicmientos 
        $company->bankaccounts()->create($request->all());

    }

    public function update(Request $request, BankAccount $bankaccount)
    {
        $request->validate([
            'account_number' => 'required|min:1|max:999',
        ]);


        //actulizar los establecimientos
        $bankaccount->update($request->all());
    }

    public function destroy(BankAccount $bankaccount)
    {
        $bankaccount->delete(); // Esto usará SoftDeletes

        return response()->json([
            'success' => true,
            'message' => 'Cuenta eliminada correctamente.',
        ]);
    }

    public function updateState($id)
    {
        $bankaccount = BankAccount::findOrFail($id);
        $bankaccount ->state = !$bankaccount ->state; // Toggle the state
        $bankaccount ->save();

        return response()->json(['success' => true]);
    }
}
