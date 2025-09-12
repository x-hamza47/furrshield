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

        if ($owners->isEmpty() || $adoptions->isEmpty()) {
            $this->command->warn('⚠️ No owners or adoptions found, skipping adoption_requests seeding.');
            return;
        }

        foreach ($adoptions as $adoption) {
            // Random number of requests per adoption
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
