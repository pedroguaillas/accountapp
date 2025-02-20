<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Landlord\PayMethod;
use App\Models\Landlord\Iva;
use App\Models\Landlord\Ice;
use App\Models\Landlord\Withholding;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User Admin
        User::factory()->create([
            'name' => 'Pedro Guaillas',
            'username' => 'peters',
            'email' => 'peters@example.com',
        ]);

        // User Tenant example Ariel
        $user = User::factory()->create([
            'name' => 'Ariel Torres',
            'username' => 'ariels',
            'email' => 'ariel4@example.com',
        ]);

        // Tenant example ariel
        $tenant = Tenant::create(['id' => 'ariels']);

        // Many to Many User with Tenant 
        $user->tenants()->attach($tenant, ['is_owner' => true]); //relacion 

        //dominio
        $tenant->domains()->create(['domain' => 'ariels.example.test']);

        PayMethod::create(['name' => 'SIN UTILIZACION DEL SISTEMA FINANCIERO', 'code' => 1,'max'=>500]);
        PayMethod::create(['name' => 'COMPENSACIÓN DE DEUDAS', 'code' => 15]);
        PayMethod::create(['name' => 'TARJETA DE DÉBITO', 'code' => 16]);
        PayMethod::create(['name' => 'DINERO ELECTRÓNICO', 'code' => 17]);
        PayMethod::create(['name' => 'TARJETA PREPAGO', 'code' => 18]);
        PayMethod::create(['name' => 'TARJETA DE CRÉDITO', 'code' => 19]);
        PayMethod::create(['name' => 'OTROS CON UTILIZACIÓN DEL SISTEMA FINANCIERO', 'code' => 20]);
        PayMethod::create(['name' => 'ENDOSO DE TÍTULOS', 'code' => 21]);

        Iva::create(['code' => 0, 'name' => '0', 'fee' => 0.00]);
        Iva::create(['code' => 2, 'name' => '12', 'fee' => 0.12]);
        Iva::create(['code' => 3, 'name' => '14', 'fee' => 0.14]);
        Iva::create(['code' => 4, 'name' => '15', 'fee' => 0.15]);
        Iva::create(['code' => 5, 'name' => '5', 'fee' => 0.05]);
        Iva::create(['code' => 8, 'name' => 'IVA diferenciado', 'fee' => 0.08]);

        Ice::create(['code' => 3011, 'name' => 'ICE Cigarrillos Rubios', 'specific_fee' => 0.17]);
        Ice::create(['code' => 3021, 'name' => 'ICE Cigarrillos Negros', 'specific_fee' => 0.17]);
        Ice::create([
            'code' => 3023,
            'name' => 'ICE Productos del Tabaco y Sucedáneos del Tabaco excepto Cigarrillos',
            'fee' => 1.5
        ]);
        Ice::create(['code' => 3031, 'name' => 'ICE Bebidas Alcohólicas', 'fee' => 0.75, 'specific_fee' => 10.36]);

        Withholding::create([
            'code' => '9', 
            'name' => '10%',
            'fee' => 0.10, 
            'type' => 'IVA' 
        ]);
        Withholding::create([
            'code' => '332', 
            'name' => 'Otras retenciones',
            'fee' => 0, 
            'type' => 'RENTA' 
        ]);
    }
}