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
        $search = $request->input('search', '');
        // Construimos la consulta base
        $bankaccounts = BankAccount::query()
            ->select('id', 'account_number', 'account_type', 'current_balance', 'state')
            ->where('data_additional->company_id', $company->id)
            ->where('bank_id', $bank->id)
            ->when($search, function ($query, $search) {
                $query->whereRaw("LOWER(name) LIKE ?", ["%" . strtolower($search) . "%"]);
            })->paginate(10)
            ->withQueryString();

        // Renderizamos la vista con los datos necesarios
        return Inertia::render('BankAccount/Index', [
            'filters' => [
                'search' => $search, // Retornar el filtro de código
            ],
            'bankaccounts' => $bankaccounts,
            'bank' => $bank,
        ]);
    }

    public function store(Request $request)
    {
        //validacion de datos
        $request->validate([
            'account_number' => 'required|min:1|max:999',
        ]);

        $company = Company::first();
        $data = [
            "company_id" => $company->id,
        ];

        //creacion de los estableicmientos 
        BankAccount::create([...$request->all(), 'data_additional' => $data]);
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
        $bankaccount->state = !$bankaccount->state; // Toggle the state
        $bankaccount->save();

        return response()->json(['success' => true]);
    }
}