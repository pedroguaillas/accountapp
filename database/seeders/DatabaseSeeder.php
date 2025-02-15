<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\PayMethod;
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
