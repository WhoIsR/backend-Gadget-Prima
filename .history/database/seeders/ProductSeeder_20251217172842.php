<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'iPhone 15 Pro Max',
            'sku' => 'HP-001',
            'category' => 'Smartphone',
            'price' => 24000000,
            'stock' => 10,
            'status' => 'active'
        ]);

        Product::create([
            'name' => 'Samsung S24 Ultra',
            'sku' => 'HP-002',
            'category' => 'Smartphone',
            'price' => 22000000,
            'stock' => 5,
            'status' => 'active'
        ]);

        Product::create([
            'name' => 'Casing Transparan',
            'sku' => 'ACC-001',
            'category' => 'Aksesoris',
            'price' => 50000,
            'stock' => 100,
            'status' => 'active'
        ]);
    }
}
