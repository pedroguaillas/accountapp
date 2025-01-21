<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Imports\AccountsImport;
use App\Models\Company;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function index()
    {
        // Obtener la primera empresa
        $company = Company::first();

        // Verificar si existe una empresa
        if (!$company) {
            return redirect()->back()->with('error', 'No se encontró una empresa registrada.');
        }

        // Consultar las cuentas con los filtros y relaciones necesarias
        $accounts = Account::select('accounts.id', 'accounts.code', 'accounts.name', 'a2.code AS c2', 'accounts.is_detail')
            ->leftJoin('accounts AS a2', 'accounts.parent_id', '=', 'a2.id')
            ->where('accounts.company_id', $company->id)
            ->orderBy('accounts.code')
            ->get();

        // Retornar la vista con datos
        return Inertia::render('Account/Index', compact('accounts'));
    }

    public function create(Account $account)
    {
        $code = $account->code;
        if ($account->is_detail) {
            // Si es detalle, simplemente agregar ".1" al código actual
            $code = $code . ".1";
        } else {
            // Recuperamos el último hijo de las cuentas con el prefijo del código actual
            $accountChild = Account::where('code', 'LIKE', $account->code . '.%')
                ->orderBy('code', 'desc') // Ordenamos en orden descendente para obtener el último
                ->first(); // Obtenemos el último registro

            if ($accountChild) {
                // Extraemos el último número del código del hijo más reciente
                $lastCode = $accountChild->code;
                $segments = explode('.', $lastCode); // Dividimos el código en partes
                $lastNumber = (int) array_pop($segments); // Tomamos el último número y lo convertimos a entero
                $code = $code . "." . ++$lastNumber; // Reconstruimos el código con el nuevo número
            }
        }
        //retornamos el codigo para colocar por defecto en el campo y que no puedan editar
        return response()->json(['code' => $code]);
    }

    public function store(Request $request)
    {
        $company = Company::first();
        $inputs = [
            ...$request->all(),
            'type' => $this->typeAccount($request->code),
            'is_detail' => true
        ];

        $company->accounts()->create($inputs);
        Account::where('id', $request->parent_id)
            ->update(['is_detail' => false]);

    }

    public function update(Request $request, Account $account)
    {
        $account->update($request->all());
    }

    public function destroy(Account $account)
    {

        $account->delete();
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);

        // Importar el archivo Excel a la tabla 'accounts'
        Excel::import(new AccountsImport, $request->file('file'));

        return to_route('setting.account.index');
    }

    private function typeAccount($account)
    {
        if (str_starts_with($account, '1'))
            return 'activo';
        if (str_starts_with($account, '2'))
            return 'pasivo';
        if (str_starts_with($account, '3'))
            return 'patrimonio';
        if (str_starts_with($account, '4'))
            return 'ingreso';
        if (str_starts_with($account, '5'))
            return 'gasto';
    }
}
