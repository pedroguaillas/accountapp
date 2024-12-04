<?php

namespace App\Imports;

use App\Models\Account;
use App\Models\Company;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class AccountsImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // validar formato de cuenta Ej. 1.1.1 bien, 1.10001.1 mal
        // Verificar que no se repita las cuentas
        // Verificar espacios en blanco
        // Verificar que una cuenta no termine en punto

        $company = Company::first();

        if ($company) {
            // Paso 1: Crear cuentas desde el Excel
            $accounts = [];
            foreach ($rows as $cols) {
                $accounts[] = [
                    'code' => $cols[0],
                    'type' => $this->typeAccount($cols[0]),
                    'name' => str_replace('  ', ' ', trim($cols[1])),
                ];
            }

            $company->accounts()->createMany($accounts);

            // Paso 2: Recuperar todas las cuentas y construir la jerarquía
            $allAccounts = Account::select('id', 'code')
                ->where('company_id', $company->id)
                ->orderBy('code', 'asc') // Ordenar por código jerárquico
                ->get();

            // Crear un mapa de referencia de cuentas por código
            $accountsMap = $allAccounts->keyBy('code');

            // Asignar `parent_id` basándonos en el código jerárquico
            foreach ($allAccounts as $account) {
                $parentCode = substr($account->code, 0, strrpos($account->code, '.'));
                if ($parentCode && isset($accountsMap[$parentCode])) {
                    $account->parent_id = $accountsMap[$parentCode]->id;
                    $account->save();
                }
            }

            // Paso 3: Marcar cuentas sin hijos como `is_detail = true`
            // foreach ($allAccounts as $account) {
            //     $hasChildren = Account::where('parent_id', $account->id)->exists();
            //     if (!$hasChildren) {
            //         $account->is_detail = true;
            //         $account->save();
            //     }
            // }

            // Paso 3: Marcar cuentas sin hijos como `is_detail = true`
            $accountsKeys = [];
            foreach ($allAccounts as $account) {
                $hasChildren = Account::where('parent_id', $account->id)->exists();
                if (!$hasChildren) {
                    $accountsKeys[] = $account->id;
                }
            }
            Account::whereIn('id', $accountsKeys)->update(['is_detail' => true]);
        }
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
