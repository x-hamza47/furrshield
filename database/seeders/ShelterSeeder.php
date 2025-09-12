<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Shelter;
use App\Models\Adoption;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ShelterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shelters = User::factory()
            ->count(10)
            ->create([
                'role' => 'shelter',
            ]);

        foreach ($shelters as $shelterUser) {

            $shelter = Shelter::create([
                'shelter_id' => $shelterUser->id,
                'shelter_name' => fake()->company() . " Shelter",
                'contact_person' => fake()->name(),
                'description' => fake()->sentence(),
            ]);


            Adoption::factory()
                ->count(rand(2, 5)) 
                ->create([
                    'shelter_id' => $shelter->id,
                ]);
        }
    }
}
