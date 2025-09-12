<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Adoption;
use App\Models\AdoptionRequest;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdoptionRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owners = User::where('role', 'owner')->get();
        $adoptions = Adoption::all();

        if ($adoptions->isEmpty()) {
            $shelters = User::where('role', 'shelter')->get();
            if ($shelters->isEmpty()) {
                $shelters = User::factory(3)->create(['role' => 'shelter']);
            }
            $adoptions = Adoption::factory(3)->create([
                'shelter_id' => $shelters->random()->id
            ]);
        }

        if ($owners->isEmpty()) {
            $owners = User::factory(10)->create(['role' => 'owner']);
        }

        foreach ($adoptions as $adoption) {
            $requestsCount = rand(1, 3);

            for ($i = 0; $i < $requestsCount; $i++) {
                AdoptionRequest::create([
                    'adoption_id' => $adoption->id,
                    'user_id' => $owners->random()->id,
                    'status' => collect(['pending', 'approved', 'rejected'])->random(),
                    'message' => fake()->sentence(10),
                ]);
            }
        }
    }
}
