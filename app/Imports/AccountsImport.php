<?php

namespace App\Imports;

use App\Models\Account;
use App\Models\Company;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class AccountsImport implements ToCollection
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        $company = Company::first();

        if ($company) {

            $accounts = [];

            foreach ($rows as $cols) {
                $accounts[] = [
                    'code' => $cols[0],
                    'type' => $this->typeAccount($cols[0]),
                    'name' => str_replace('  ', ' ', trim($cols[1])),
                ];
            }

            $company->accounts()->createMany($accounts);

            // // Hacer un select con order by desc
            // $accounts = Account::select('id', 'code')
            //     ->where('company_id', $company->id)
            //     ->orderBy('id', 'desc')->get();

            // //toca pasar a un array
            // $accountBefore = null;
            // $arrayAccountBefore = null;
            // $childs = [];
            // foreach ($accounts as $account) {
            //     // pasamos el codigo a un string
            //     $arrayCode = explode('.', $account->code);

            //     if ($accountBefore !== null && count($arrayCode) < count($arrayAccountBefore)) {
            //         //todas cuentas de array child actualiza el id actual $cuenta->id al partnet_id
            //         Account::whereIn('id', $childs)->update(['parent_id' => $account->id]);
            //         $childs = null;
            //         $childs = [];
            //     } else if ($accountBefore !== null && count($arrayCode) > count($arrayAccountBefore)) {
            //         $childs = []; // reniciar
            //     }

            //     $childs[] = $account->id;
            //     $accountBefore = $account;
            //     $arrayAccountBefore = $arrayCode;
            // }

            // Paso 2: Recuperar todas las cuentas y ordenarlas por `code` para construir la jerarquía
            $allAccounts = Account::where('company_id', $company->id)
                ->orderBy('code', 'asc') // Ordenar por código jerárquico
                ->get();

            // Paso 3: Crear un mapa de referencia de cuentas por código
            $accountsMap = $allAccounts->keyBy('code');

            // Paso 4: Asignar `parent_id` basándonos en el código jerárquico
            foreach ($allAccounts as $account) {
                // Determinar el código del padre extrayendo el código previo al último punto
                $parentCode = substr($account->code, 0, strrpos($account->code, '.'));

                // Si hay un `parentCode`, buscarlo en el mapa y actualizar el `parent_id`
                if ($parentCode && isset($accountsMap[$parentCode])) {
                    $account->parent_id = $accountsMap[$parentCode]->id;
                    $account->save();
                }
            }
        }
    }

    function typeAccount($account)
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
