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
        // Base users and roles — only create if core users don't already exist
        $coreEmails = ['owner@gadgetprima.com', 'admin@gadgetprima.com', 'gudang@gadgetprima.com', 'kasir@gadgetprima.com'];
        $exists = User::whereIn('email', $coreEmails)->exists();
        if (! $exists) {
            $this->call(UserSeeder::class);
        }

        // Categories and brands — create from gadget-focused lists
        $categories = [
            'Smartphone',
            'Aksesoris',
            'Tablet',
            'iPad',
            'Laptop',
            'Smartwatch',
            'Audio',
            'Powerbank',
            'Charger',
            'Kamera',
            'Monitor',
            'Printer'
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat]);
        }

        $brands = [
            'Apple',
            'Samsung',
            'Xiaomi',
            'OPPO',
            'Vivo',
            'Realme',
            'Infinix',
            'Lenovo',
            'Asus',
            'Acer',
            'Sony',
            'JBL',
            'Anker',
            'Huawei',
            'Honor',
            'OnePlus',
            'Google',
            'Motorola',
            'Nokia'
        ];

        foreach ($brands as $b) {
            Brand::firstOrCreate(['name' => $b]);
        }

        // Products: create gadget-focused products with realistic names/prices
        $deviceBrands = [
            'Apple','Samsung','Xiaomi','OPPO','Vivo','Realme','Infinix','Lenovo','Asus','Acer',
            'Sony','JBL','Anker','Huawei','Honor','OnePlus','Google','Motorola','Nokia'
        ];

        $accessoryBrands = ['Anker','JBL','Baseus','Generic','Remax','Spigen','UAG','OtterBox'];

        $categoryPlan = [
            'Smartphone' => 400,
            'Tablet' => 80,
            'iPad' => 40,
            'Laptop' => 120,
            'Smartwatch' => 60,
            'Audio' => 80,
            'Kamera' => 30,
            'Monitor' => 20,
            'Printer' => 10,
            'Aksesoris' => 80,
            'Charger' => 20,
            'Powerbank' => 20,
        ];

        $faker = \Faker\Factory::create();

        // helpers
        $makeSku = function() {
            return strtoupper(uniqid('PRD-'));
        };

        $priceRange = function($cat) {
            return match($cat) {
                'Smartphone' => [1500000, 30000000],
                'Tablet','iPad' => [1000000, 25000000],
                'Laptop' => [3000000, 50000000],
                'Smartwatch' => [500000, 7000000],
                'Audio' => [100000, 8000000],
                'Kamera' => [2000000, 40000000],
                'Monitor' => [1500000, 15000000],
                'Printer' => [1000000, 10000000],
                'Charger' => [50000, 500000],
                'Powerbank' => [80000, 1000000],
                default => [50000, 500000],
            };
        };

        $makeName = function($brand, $cat) use ($faker) {
            $b = $brand ?: 'Generic';
            $catLow = strtolower($cat);

            // brand specific patterns
            $patterns = [
                'Apple' => function($b,$cat) use ($faker){
                    if (str_contains(strtolower($cat),'phone')) return $b . ' iPhone ' . $faker->numberBetween(11, 17) . ' ' . $faker->randomElement(['Pro','Pro Max','Mini']);
                    if (str_contains(strtolower($cat),'ipad')) return $b . ' iPad ' . $faker->randomElement(['Pro','Air','Mini']) . ' ' . $faker->numberBetween(1,6);
                    if (str_contains(strtolower($cat),'laptop')) return $b . ' MacBook ' . $faker->randomElement(['Air','Pro']) . ' ' . $faker->numberBetween(2018,2024);
                    return $b . ' ' . ucfirst($cat);
                },
                'Samsung' => function($b,$cat) use ($faker){
                    if (str_contains($cat,'Smartphone')) return $b . ' Galaxy S' . $faker->numberBetween(20,24) . ' ' . $faker->randomElement(['','Ultra','FE']);
                    if (str_contains($cat,'Tablet')) return $b . ' Galaxy Tab ' . $faker->randomElement(['S8','S7','A8']);
                    return $b . ' ' . ucfirst($cat);
                },
                'Xiaomi' => function($b,$cat) use ($faker){
                    if (str_contains($cat,'Smartphone')) return $b . ' Redmi Note ' . $faker->numberBetween(9,13);
                    return $b . ' ' . $faker->word();
                },
                'OnePlus' => function($b,$cat) use ($faker){
                    return $b . ' Nord ' . $faker->numberBetween(1,5);
                },
                'Google' => function($b,$cat) use ($faker){
                    return $b . ' Pixel ' . $faker->numberBetween(3,8);
                },
                'default' => function($b,$cat) use ($faker){
                    return $b . ' ' . ucfirst($cat) . ' ' . $faker->bothify('##');
                }
            ];

            if (isset($patterns[$brand])) return $patterns[$brand]($brand,$cat);
            return $patterns['default']($brand,$cat);
        };

        foreach ($categoryPlan as $cat => $count) {
            for ($i=0;$i<$count;$i++) {
                // select brand: devices prefer deviceBrands
                $isDevice = in_array($cat, ['Smartphone','Tablet','iPad','Laptop','Smartwatch','Audio','Kamera','Monitor','Printer']);
                $brand = $isDevice ? $faker->randomElement($deviceBrands) : $faker->randomElement($accessoryBrands);

                [$minp,$maxp] = $priceRange($cat);
                $price = $faker->numberBetween($minp, $maxp);
                $buy = (int) ($price * $faker->randomFloat(2, 0.6, 0.85));

                Product::create([
                    'name' => $makeName($brand, $cat),
                    'description' => $faker->sentence(12),
                    'sku' => $makeSku(),
                    'category' => $cat,
                    'brand' => $brand,
                    'buy_price' => $buy,
                    'price' => $price,
                    'stock' => $faker->numberBetween(0, 200),
                    'min_stock' => $faker->numberBetween(0, 10),
                    'image' => null,
                    'status' => $faker->randomElement(['active','inactive']),
                ]);
            }
        }

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

            $methods = ['cash', 'card', 'e-wallet'];
            $order = Order::create([
                'invoice_number' => $invoice,
                'user_id' => $userId,
                'total_amount' => 0,
                'tax' => 0,
                'payment_method' => $methods[array_rand($methods)],
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
