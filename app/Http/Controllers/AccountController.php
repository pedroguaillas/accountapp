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
        $accounts = Account::selectRaw('accounts.id, accounts.code, accounts.name, accounts.parent_id, COALESCE(SUM(je.debit) - SUM(je.have), 0) AS saldo')
            ->leftJoin('journal_entries as je', function ($join) {
                $join->on('accounts.id', '=', 'je.account_id')
                    ->whereNull('je.deleted_at'); // Excluye los eliminados suavemente
            })
            ->where('accounts.company_id', $company->id)
            ->groupBy('accounts.id', 'accounts.code', 'accounts.name', 'accounts.parent_id')
            // ->orderByRaw('LENGTH(code) ')
            ->orderBy('accounts.code')
            ->get();

        // Convertir las cuentas a una estructura jerárquica
        $accountsTree = $accounts->groupBy('parent_id');

        // Calcular los saldos desde el nivel raíz
        $rootAccounts = $accountsTree->get(null) ?? collect();
        foreach ($rootAccounts as $rootAccount) {
            $rootAccount->saldo += $this->calculateAccountBalances($rootAccount->id, $accountsTree);
        }

        // Formatear los datos para retornarlos
        $formattedAccounts = $accounts->map(function ($account) {
            return [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'parent_id' => $account->parent_id,
                'saldo' => $account->saldo, // Suma acumulada del saldo
            ];
        });

        return Inertia::render('Account/Index', [
            'accounts' => $formattedAccounts // Pasar directamente como un array asociativo
        ]);
    }

    /**
     * Función recursiva para calcular los saldos de las cuentas hijas y sumar en los padres.
     */
    private function calculateAccountBalances($parentId, $accountsTree)
    {
        // Obtener las cuentas hijas
        $children = $accountsTree->get($parentId) ?? collect();
        $totalSaldo = 0;

        foreach ($children as $child) {
            // Calcular el saldo de los hijos de forma recursiva
            $childSaldo = $this->calculateAccountBalances($child->id, $accountsTree);
            $child->saldo += $childSaldo; // Sumar el saldo acumulado del hijo al propio
            $totalSaldo += $child->saldo; // Agregar el saldo del hijo al total acumulado
        }

        return $totalSaldo; // Retornar el saldo total acumulado
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
                ->orderByRaw('LENGTH(code) DESC')  // Ordenar por longitud del código primero
                ->orderBy('code', 'desc')          // Luego, ordenar por el valor del código
                ->first();

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
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'type' => 'required',
        ]);

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
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'type' => 'required',
        ]);
        $account->update($request->all());
    }

    public function destroy(Account $account)
    {
        $hasChildren = Account::where('parent_id', $account->id)->exists();
        if (!$hasChildren) {
            $account->delete();
        } else {
            return response()->json(["sms" => "Error al eliminar porque es una cuenta superior"], 400);
        }
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
