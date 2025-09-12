<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Adoption;
use App\Models\AdoptionRequest;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdoptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shelters = User::where('role', 'shelter')->get();
        $owners   = User::where('role', 'owner')->get();


        if ($shelters->isEmpty()) {
            $shelters = User::factory(3)->create(['role' => 'shelter']);
        }

        if ($owners->isEmpty()) {
            $owners = User::factory(10)->create(['role' => 'owner']);
        }
 
        $shelters->each(function ($shelter) use ($owners) {
            Adoption::factory(5)->create([
                'shelter_id' => $shelter->id,
            ])->each(function ($adoption) use ($owners) {
                $randomOwners = $owners->random(rand(2, 4));


                foreach ($randomOwners as $owner) {
                    AdoptionRequest::query()->create([
                        'adoption_id' => $adoption->id,
                        'user_id'     => $owner->id,
                        'status'      => fake()->randomElement(['pending', 'approved','rejected']),
                        'message'     => fake()->sentence(),
                    ]);
                }


                $approved = $adoption->requests()->inRandomOrder()->first();
                if ($approved) {
                    $approved->update(['status' => 'approved']);
                    $adoption->update(['status' => 'adopted']);
                }
            });
        });
    }
}
