<?php

namespace Database\Seeders;

use App\Models\Tenant;
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
            'username' => 'ariel',
            'email' => 'ariel@example.com',
        ]);

        // Tenant example ariel
        $tenant = Tenant::create(['id' => 'ariel']);

        // Many to Many User with Tenant 
        $user->tenants()->attach($tenant);
    }
}
