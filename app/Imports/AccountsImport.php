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

            // Hacer un select con order by desc
            $accounts = Account::select('id', 'code')
                ->where('company_id', $company->id)
                ->orderBy('id', 'desc')->get();

            //toca pasar a un array
            $accountBefore = null;
            $arrayAccountBefore = null;
            $childs = [];
            foreach ($accounts as $account) {
                // pasamos el codigo a un string
                $arrayCode = explode('.', $account->code);

                if ($accountBefore !== null && count($arrayCode) < count($arrayAccountBefore)) {
                    //todas cuentas de array child actualiza el id actual $cuenta->id al partnet_id
                    Account::whereIn('id', $childs)->update(['parent_id' => $account->id]);
                    $childs = null;
                    $childs = [];
                } else if ($accountBefore !== null && count($arrayCode) > count($arrayAccountBefore)) {
                    $childs = []; // reniciar
                }

                $childs[] = $account->id;
                $accountBefore = $account;
                $arrayAccountBefore = $arrayCode;
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
