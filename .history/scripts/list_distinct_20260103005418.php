<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Distinct categories in products (category => count):\n";
$cats = DB::table('products')
    ->select('category', DB::raw('count(*) as cnt'))
    ->groupBy('category')
    ->orderByDesc('cnt')
    ->get();

foreach ($cats as $c) {
    printf("%s => %d\n", $c->category ?? '(null)', $c->cnt);
}

echo "\nDistinct brands in products (brand => count):\n";
$brands = DB::table('products')
    ->select('brand', DB::raw('count(*) as cnt'))
    ->groupBy('brand')
    ->orderByDesc('cnt')
    ->get();

foreach ($brands as $b) {
    printf("%s => %d\n", $b->brand ?? '(null)', $b->cnt);
}

echo "\nTop 30 product samples:\n";
$samples = DB::table('products')->select('id','name','sku','category','brand','price','stock')->limit(30)->get();
foreach ($samples as $s) {
    printf("%d | %s | %s | %s | %s | %s | %d\n", $s->id, $s->name, $s->sku, $s->category, $s->brand, number_format($s->price,0,',','.'), $s->stock);
}
