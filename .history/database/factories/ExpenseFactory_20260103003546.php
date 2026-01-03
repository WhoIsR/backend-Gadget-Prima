<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Expense;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'date' => $this->faker->dateTimeBetween('-1 years', 'now')->format('Y-m-d'),
            'description' => $this->faker->sentence(),
            'category' => $this->faker->randomElement(['operational', 'marketing', 'maintenance', 'other']),
            'amount' => $this->faker->numberBetween(10000, 5000000),
        ];
    }
}
