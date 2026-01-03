<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with larger test data.
     */
    public function run(): void
    {
        // Base users and roles
        $this->call(UserSeeder::class);

        // Categories and brands
        Category::factory()->count(30)->create();
        Brand::factory()->count(20)->create();

        // Products
        Product::factory()->count(500)->create();

        // Stock movements (random history)
        StockMovement::factory()->count(1200)->create();

        // Expenses
        Expense::factory()->count(300)->create();

        // Orders + order items
        $users = User::all()->pluck('id')->toArray();
        $productIds = Product::all()->pluck('id')->toArray();

        // Create many orders with items
        $orderCount = 400;
        for ($i = 0; $i < $orderCount; $i++) {
            $userId = $users[array_rand($users)];
            $invoice = 'INV-' . now()->format('Ymd') . '-' . strtoupper(uniqid());

            $order = Order::create([
                'invoice_number' => $invoice,
                'user_id' => $userId,
                'total_amount' => 0,
                'tax' => 0,
                'payment_method' => ['cash', 'card', 'transfer'][array_rand(['cash', 'card', 'transfer'])],
            ]);

            $itemsCount = rand(1, 5);
            $total = 0;

            $picked = (array) array_rand($productIds, min($itemsCount, max(1, count($productIds))));
            if (!is_array($picked)) {
                $picked = [$picked];
            }

            foreach ($picked as $key) {
                $productId = $productIds[$key];
                $product = Product::find($productId);
                if (!$product) continue;

                $qty = rand(1, min(5, max(1, $product->stock))); // don't oversell too much
                if ($qty === 0) $qty = 1;
                $price = $product->price ?? rand(50000, 2000000);
                $subtotal = $price * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                // decrement stock
                $product->decrement('stock', $qty);

                // record stock movement for sale
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $userId,
                    'type' => 'out',
                    'amount' => $qty,
                    'current_stock' => $product->stock,
                    'reason' => 'penjualan',
                ]);

                $total += $subtotal;
            }

            $tax = round($total * 0.1);
            $order->update(['total_amount' => $total, 'tax' => $tax]);
        }
    }
}
