<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\Brand;

echo 'Products: ' . Product::count() . PHP_EOL;
echo 'Orders: ' . Order::count() . PHP_EOL;
echo 'Categories: ' . Category::count() . PHP_EOL;
echo 'Brands: ' . Brand::count() . PHP_EOL;
