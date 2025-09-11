<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pet>
 */
class PetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(), 
            'name' => $this->faker->firstName(),
            'species' => $this->faker->randomElement(['Dog', 'Cat', 'Bird']),
            'breed' => $this->faker->word(),
            'age' => $this->faker->numberBetween(1, 15),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'medical_history' => $this->faker->sentence(),
        ];
    }
}
