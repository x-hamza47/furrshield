<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shelter;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shelters = Shelter::all();


        if ($shelters->count() === 0) {
            $this->command->info('No shelters found, skipping product seeding.');
            return;
        }
        Product::factory(50)->make()->each(function ($product) use ($shelters) {
            $product->shelter_id = $shelters->random()->id; 
            $product->save();
        });
    }
}
