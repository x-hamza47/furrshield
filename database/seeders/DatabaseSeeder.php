<?php

namespace Database\Seeders;

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
            AdminSeeder::class,
            ShelterSeeder::class,
            AppointmentSeeder::class,
            VetSeeder::class,
            OrderSeeder::class,
        ]);
        // User::factory(5)->hasPets(3)->create(['role' => 'owner']);


        // Product::factory(10)->create();

    }
}
