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

        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'sku' => strtoupper($this->faker->bothify('PRD-####')),
            'category' => $category?->name ?? $this->faker->word(),
            'brand' => $brand?->name ?? $this->faker->company(),
            'buy_price' => $buy,
            'price' => $price,
            'stock' => $this->faker->numberBetween(0, 500),
            'min_stock' => $this->faker->numberBetween(0, 20),
            'image' => null,
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
