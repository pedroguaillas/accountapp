<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CostCenter>
 */
class CostCenterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            
            "company_id"=> 1,
            "code" => strtoupper($this->faker->randomLetter()) . $this->faker->numberBetween(0, 9),
            "name"=> $this->faker->name(), 
        ];
    }
}
