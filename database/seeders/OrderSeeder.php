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
        $owners = User::where('role', 'owner')->get();

        $products = Product::all();

        foreach ($owners as $owner) {

            for ($i = 0; $i < rand(1, 3); $i++) {
                $order = Order::create([
                    'owner_id' => $owner->id,
                    'order_date' => now()->subDays(rand(1, 30)),
                    'total_amount' => 0, 
                    'status' => $this->randomStatus(),
                ]);

                $total = 0;

                $items = $products->random(rand(1, 4));
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

                $order->update(['total_amount' => $total]);
            }
        }
    }

    private function randomStatus()
    {
        return collect(['pending', 'completed', 'cancelled'])->random();
    }
}
