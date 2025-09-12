<?php

namespace Database\Seeders;

use Carbon\Carbon;
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
            // if ($vets->count() === 0) {
            //     continue;
            // }

            // Generate random appointment date and time
            $randomDateTime = Carbon::now()->addDays(rand(1, 14))->setHour(rand(9, 17))->setMinute([0, 15, 30, 45][array_rand([0, 15, 30, 45])]);

            Appointment::create([
                'pet_id' => $pet->id,
                'owner_id' => $pet->owner_id,
                'vet_id' => 12,
                'appt_date' => $randomDateTime->toDateString(),
                'appt_time' => $randomDateTime->format('H:i:s'),
                'status' => 'pending',
                // 'status' => ['pending', 'approved', 'completed'][array_rand(['pending', 'approved', 'completed'])],
            ]);
        }
    }
}
