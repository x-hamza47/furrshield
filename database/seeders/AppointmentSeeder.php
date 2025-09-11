<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pets = Pet::with('owner')->inRandomOrder()->take(10)->get();


        $vets = User::where('role', 'vet')->get();

        foreach ($pets as $pet) {
            if ($vets->count() === 0) {
                continue; 
            }

            Appointment::create([
                'pet_id' => $pet->id,
                'owner_id' => $pet->owner_id, 
                'vet_id' => $vets->random()->id,
                'appointment_time' => now()->addDays(rand(1, 14)),
                'status' => 'pending',
            ]);
        }
    }
}
