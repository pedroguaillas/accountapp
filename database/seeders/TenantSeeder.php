<?php

namespace Database\Seeders;

use App\Models\ContributorType;
use App\Models\EconomicActivity;
use App\Models\PayMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EconomicActivity::create(['name' => 'Diseño']);
        EconomicActivity::create(['name' => 'Construcción']);
        EconomicActivity::create(['name' => 'Servicios Profesiones']);

        ContributorType::create(['name' => 'GENERAL']);
        ContributorType::create(['name' => 'RIMPE']);
        ContributorType::create(['name' => 'NEGOCIO POPULAR - RÉGIMEN RIMPE']);

        PayMethod::create(['name' => 'SIN UTILIZACION DEL SISTEMA FINANCIERO', 'code' => 1]);
        PayMethod::create(['name' => 'COMPENSACIÓN DE DEUDAS', 'code' => 15]);
        PayMethod::create(['name' => 'TARJETA DE DÉBITO', 'code' => 16]);
        PayMethod::create(['name' => 'DINERO ELECTRÓNICO', 'code' => 17]);
        PayMethod::create(['name' => 'TARJETA PREPAGO', 'code' => 18]);
        PayMethod::create(['name' => 'TARJETA DE CRÉDITO', 'code' => 19]);
        PayMethod::create(['name' => 'OTROS CON UTILIZACIÓN DEL SISTEMA FINANCIERO', 'code' => 20]);
        PayMethod::create(['name' => 'ENDOSO DE TÍTULOS', 'code' => 21]);

    }

}
