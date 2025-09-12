<?php

namespace Database\Factories;

use App\Models\Shelter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Adoption>
 */
class AdoptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
          return [
            'name'        => fake()->firstName(),
            'species'     => fake()->randomElement(['Dog', 'Cat', 'Rabbit']),
            'breed'       => fake()->word(),
            'age'         => fake()->numberBetween(1, 12),
            'status'      => fake()->randomElement(['available', 'adopted']),
            'description' => fake()->sentence(),
            'image'       => null, 
        ];
    }
}
