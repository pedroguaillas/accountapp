<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\ContributorType;
use App\Models\EconomicActivity;
use App\Models\PayMethod;
use App\Models\ActiveType;
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

        ActiveType::create(['name' => 'Terrenos', 'depreciation_time' => 0]);
        ActiveType::create(['name' => 'Edificios', 'depreciation_time' => 20]);
        ActiveType::create(['name' => 'Maquinaria, equipo e instalaciones', 'depreciation_time' => 10]);
        ActiveType::create(['name' => 'Muebles enseres y equipos de oficina ', 'depreciation_time' => 10]);
        ActiveType::create(['name' => 'Vehículos', 'depreciation_time' => 5]);
        ActiveType::create(['name' => 'Equipo de computo, tecnológico y otros', 'depreciation_time' => 3]);

        Company::create([
            'ruc' => '1100167694001',
            'company' => 'EJEMPLO COMPANIA',
            'economic_activity_id' => 1,
            'contributor_type_id' => 1,
        ]);
    }

}
