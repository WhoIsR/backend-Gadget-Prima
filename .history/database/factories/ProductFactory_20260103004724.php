<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $category = Category::inRandomOrder()->first();
        $brand = Brand::inRandomOrder()->first();
        $price = $this->faker->numberBetween(50000, 25000000);
        $buy = (int) ($price * $this->faker->randomFloat(2, 0.5, 0.9));

        $brandName = $brand?->name ?? $this->faker->company();
        $catName = strtolower($category?->name ?? 'gadget');

        // domain-specific name generation
        if (str_contains($catName, 'phone') || str_contains($catName, 'smart')) {
            $models = ['Pro', 'Max', 'Plus', 'SE', 'Lite', 'Mini', 'Ultra'];
            $model = $this->faker->bothify('##') . ' ' . $this->faker->randomElement($models);
            $name = $brandName . ' ' . $model;
        } elseif (str_contains($catName, 'ipad') || str_contains($catName, 'tablet')) {
            $variants = ['Tab', 'Pad', 'Air', 'S'];
            $name = $brandName . ' ' . $this->faker->randomElement($variants) . ' ' . $this->faker->numberBetween(1, 6) . 'th Gen';
        } elseif (str_contains($catName, 'aksesoris') || str_contains($catName, 'accessory') || str_contains($catName, 'aksesori')) {
            $acc = ['Casing Transparan', 'Tempered Glass', 'Charger 20W', 'Powerbank 10000mAh', 'Earbuds', 'Kabel USB-C', 'Headset', 'Car Charger'];
            $name = $this->faker->randomElement($acc) . ' ' . $brandName;
        } elseif (str_contains($catName, 'audio') || str_contains($catName, 'speaker')) {
            $name = $brandName . ' ' . $this->faker->randomElement(['Earbuds', 'Headphones', 'Soundbar', 'Speaker']) . ' ' . $this->faker->bothify('##');
        } else {
            $name = $brandName . ' ' . $this->faker->words(2, true);
        }

        return [
            'name' => $name,
            'description' => $this->faker->sentence(),
            'sku' => strtoupper(uniqid('PRD-')),
            'category' => $category?->name ?? 'Gadget',
            'brand' => $brandName,
            'buy_price' => $buy,
            'price' => $price,
            'stock' => $this->faker->numberBetween(0, 500),
            'min_stock' => $this->faker->numberBetween(0, 20),
            'image' => null,
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
