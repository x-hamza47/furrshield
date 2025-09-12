<?php

namespace Database\Seeders;

use App\Models\AdoptionRequest;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            // AdminSeeder::class,
            // ShelterSeeder::class,
            // VetSeeder::class,
            // OrderSeeder::class,
            AppointmentSeeder::class,

        ]);
        // User::factory(10)->hasPets(3)->create(['role' => 'owner']);

        // $this->call([
        //     AdoptionSeeder::class,

        // ]);

        // Product::factory(15)->create();
    }
}
