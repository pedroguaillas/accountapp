<?php

namespace App\Imports;

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

            foreach ($rows as $row) {
                $accounts[] = [
                    'code' => $row[0],
                    'type' => $row[1],
                    'name' => $row[2],
                ];
            }

            $company->accounts()->createMany($accounts);
        }
    }
}
