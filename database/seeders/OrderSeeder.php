<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all owners
        $owners = User::where('role', 'owner')->get();

        // Get all products
        $products = Product::all();

        if ($owners->isEmpty() || $products->isEmpty()) {
            $this->command->info('No owners or products found, skipping orders seeding.');
            return;
        }

        foreach ($owners as $owner) {

            for ($i = 0; $i < rand(1, 3); $i++) {
                $order = Order::create([
                    'owner_id' => $owner->id,
                    'order_date' => now()->subDays(rand(1, 30)),
                    'total_amount' => 0,
                    'status' => $this->randomStatus(),
                ]);

                $total = 0;

                $items = $products->shuffle()->take(rand(1, 4));

                foreach ($items as $product) {
                    $quantity = rand(1, 3);
                    $priceEach = $product->price;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price_each' => $priceEach,
                    ]);

                    $total += $priceEach * $quantity;
                }

                // Update order total
                $order->update(['total_amount' => $total]);
            }
        }
    }

    private function randomStatus()
    {
        return collect(['pending', 'cancelled'])->random();
    }
}
