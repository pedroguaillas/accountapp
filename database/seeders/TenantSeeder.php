<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\ContributorType;
use App\Models\EconomicActivity;
use App\Models\TypeBank;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        EconomicActivity::create(['name' => 'Diseño']);
        EconomicActivity::create(['name' => 'Construcción']);
        EconomicActivity::create(['name' => 'Servicios Profesiones']);

        ContributorType::create(['name' => 'GENERAL']);
        ContributorType::create(['name' => 'RIMPE']);
        ContributorType::create(['name' => 'NEGOCIO POPULAR - RÉGIMEN RIMPE']);

        $company = Company::create([
            'ruc' => '1100167694001',
            'company' => 'EJEMPLO COMPANIA',
            'economic_activity_id' => 1,
            'contributor_type_id' => 1,
        ]);

        $company->activeTypes()->createMany([
            ['name' => 'Terrenos', 'depreciation_time' => 0],
            ['name' => 'Edificios', 'depreciation_time' => 20],
            ['name' => 'Maquinaria, equipo e instalaciones', 'depreciation_time' => 10],
            ['name' => 'Muebles enseres y equipos de oficina', 'depreciation_time' => 10],
            ['name' => 'Vehículos', 'depreciation_time' => 5],
            ['name' => 'Equipo de computo, tecnológico y otros', 'depreciation_time' => 3]
        ]);

        $company->roleingress()->createMany([
            ['name' => "Décimo tercero", 'code' => 'XIII', 'type' => 'fijo'],
            ['name' => "Décimo cuarto", 'code' => 'XIV', 'type' => 'fijo'],
            ['name' => "Fondo de reserva", 'code' => 'FDR', 'type' => 'fijo'],
            ['name' => "Horas extra", 'code' => 'HE', 'type' => 'fijo'],
            ['name' => "Horas ordinarias", 'code' => 'HO', 'type' => 'fijo'],
            ['name' => "Remuneración", 'code' => 'RE', 'type' => 'fijo'],
            ['name' => "Adelanto de utilidad", 'code' => 'AU', 'type' => 'fijo'],
            ['name' => "Vacaciones", 'code' => 'VC', 'type' => 'fijo'],
            ['name' => "Alimentación", 'code' => 'AL', 'type' => 'fijo'],
            ['name' => "Otros ingresos", 'code' => 'OI', 'type' => 'fijo'],
        ]);

        $company->roleEgress()->createMany([
            ['name' => "IESS patronal", 'code' => 'IESSPA', 'type' => 'fijo'],
            ['name' => "IESS personal", 'code' => 'IESSPE', 'type' => 'fijo'],
            ['name' => "Adelanto de sueldo", 'code' => 'AS', 'type' => 'fijo'],
            ['name' => "Sueldo a pagar", 'code' => 'SP', 'type' => 'fijo'],
            ['name' => "Otros egresos", 'code' => 'OE', 'type' => 'fijo'],
        ]);

    }
}