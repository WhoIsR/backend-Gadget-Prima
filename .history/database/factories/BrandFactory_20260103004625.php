<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Brand;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

    public function definition(): array
    {
        $brands = [
            'Apple','Samsung','Xiaomi','OPPO','Vivo','Realme','Infinix','Lenovo','Asus','Acer',
            'Sony','JBL','Anker','Mi','Huawei','Honor','OnePlus','Google','Motorola','Nokia'
        ];

        return [
            'name' => $this->faker->unique()->randomElement($brands),
        ];
    }
}
