<?php

namespace Database\Seeders;

use App\Models\ContributorType;
use App\Models\EconomicActivity;
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

        ContributorType::create(['name' => 'RÉGIMEN GENERAL']);
        ContributorType::create(['name' => 'RÉGIMEN RIMPE']);
        ContributorType::create(['name' => 'RÉGIMEN NEGOCIO POPULAR - RÉGIMEN RIMPE']);
    }
}
