<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\User;

class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();
        $type = $this->faker->randomElement(['in', 'out']);
        $amount = $this->faker->numberBetween(1, 100);

        $current = $product?->stock ?? $this->faker->numberBetween(0, 500);
        if ($type === 'in') {
            $current = $current + $amount;
        } else {
            $current = max(0, $current - $amount);
        }

        return [
            'product_id' => $product?->id,
            'user_id' => $user?->id,
            'type' => $type,
            'amount' => $amount,
            'current_stock' => $current,
            'reason' => $this->faker->sentence(3),
        ];
    }
}
