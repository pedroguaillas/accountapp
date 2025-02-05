<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Person;
use App\Models\MovementType;
use App\Models\BankAccount;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;


class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $company = Company::first();

        // Construimos la consulta base
        $transactions = Transaction::join('bank_accounts', 'bank_accounts.id', '=', 'transactions.bank_account_id')
            ->join('banks', 'banks.id', '=', 'bank_accounts.bank_id')
            ->where('bank_accounts.company_id', $company->id)
            ->select('transactions.*', 'banks.name', 'bank_accounts.account_number'); // Puedes seleccionar lo que necesites

        // Aplicamos el filtro si existe
        $search = $request->input('search', ''); // Usar un valor por defecto vacío si no hay búsqueda
        $transactions->where('banks.name', 'LIKE', "%$search%");

        // Paginamos los resultados y preservamos los filtros en la URL
        $transactions = $transactions->paginate(10)->withQueryString();

        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Transaction/Index', [
            'filters' => [
                'search' => $search,
            ],
            'transactions' => $transactions,
        ]);
    }

    public function create()
    {
        $company = Company::first();

        // Obtenemos las cuentas bancarias de la compañía
        $bankAccounts = BankAccount::join('banks', 'banks.id', '=', 'bank_accounts.bank_id')
            ->where('bank_accounts.company_id', $company->id)
            ->select('bank_accounts.id', 'bank_accounts.account_number', 'banks.name') // Puedes seleccionar lo que necesites
            ->get();

        // Obtenemos los tipos de movimiento de la compañía
        $movementtypes = MovementType::where('company_id', $company->id)->get();

        $people = Person::where('company_id', $company->id)->get();
        // Renderizamos la vista con los datos necesarios
        return Inertia::render('Transaction/Create', [
            'bankaccounts' => $bankAccounts,
            'movementtypes' => $movementtypes,
            'people' => $people,
        ]);
    }

    public function store(Request $request)
    {
        //validacion de datos
        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'transaction_date' => 'required|date',
            'amount' => 'required|min:0.01',
        ]);
        //llamada a la compania
        $company = Company::first();

        //creacion de los estableicmientos 
        $company->transactions()->create($request->all());

    }

    public function update(Request $request, Transaction $transaction)
    {
        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
        ]);

        //actulizar los establecimientos
        $transaction->update($request->all());
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete(); // Esto usará SoftDeletes

        return response()->json([
            'success' => true,
            'message' => 'Movimiento bancario eliminado correctamente',
        ]);
    }
}
