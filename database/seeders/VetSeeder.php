<?php

namespace Database\Seeders;

use App\Models\Vet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vets = User::factory()
            ->count(5)
            ->create([
                'role' => 'vet',
            ]);

        foreach ($vets as $vetUser) {
            Vet::create([
                'vet_id' => $vetUser->id,
                'specialization' => fake()->randomElement([
                    'Dermatology',
                    'Surgery',
                    'Dentistry',
                    'Cardiology',
                    'General Medicine',
                ]),
                'experience' => fake()->numberBetween(1, 15),
                'available_slots' => json_encode([
                    'Mon 10:00-14:00',
                    'Wed 14:00-18:00',
                    'Fri 09:00-13:00',
                ]),
            ]);
        }
    }
}
