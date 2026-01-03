<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Prune and normalize products (gadget-focused)...\n";

$allowedBrands = [
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
    'Nokia',
    'Generic'
];

// Desired maximum counts per category after pruning
$desired = [
    'Smartphone' => 300,
    'Aksesoris' => 300,
    'Tablet' => 60,
    'iPad' => 30,
    'Laptop' => 80,
    'Smartwatch' => 60,
    'Audio' => 60,
    'Charger' => 40,
    'Powerbank' => 40,
    'Monitor' => 30,
    'Printer' => 20,
    'Kamera' => 40
];

$totalDeleted = 0;

foreach ($desired as $cat => $limit) {
    $count = DB::table('products')->where('category', $cat)->count();
    if ($count <= $limit) continue;

    $toDelete = $count - $limit;
    echo "Category $cat: current $count, pruning $toDelete...\n";

    // delete Generic-brand items first
    $idsToDelete = DB::table('products')
        ->where('category', $cat)
        ->where('brand', 'Generic')
        ->orderBy('id')
        ->limit($toDelete)
        ->pluck('id')
        ->toArray();

    $remaining = $toDelete - count($idsToDelete);
    if ($remaining > 0) {
        // need more deletions from other brands (oldest first)
        $more = DB::table('products')
            ->where('category', $cat)
            ->whereNotIn('id', $idsToDelete)
            ->orderBy('id')
            ->limit($remaining)
            ->pluck('id')
            ->toArray();
        $idsToDelete = array_merge($idsToDelete, $more);
    }

    if (!empty($idsToDelete)) {
        // remove dependent order_items first to satisfy foreign key constraints
        $oiDeleted = DB::table('order_items')->whereIn('product_id', $idsToDelete)->delete();
        if ($oiDeleted) printf("Deleted %d order_items linked to products in %s\n", $oiDeleted, $cat);

        $deleted = DB::table('products')->whereIn('id', $idsToDelete)->delete();
        printf("Deleted %d products from category %s\n", $deleted, $cat);
        $totalDeleted += $deleted;
    }
}

// After pruning, normalize product names for key categories
echo "\nNormalizing product names...\n";

$randSuffix = function () {
    $suf = ['Pro', 'Pro Max', 'Plus', 'Lite', 'SE', 'Ultra', 'Mini', 'X', 'S', 'Z'];
    return $suf[array_rand($suf)] . ' ' . rand(1, 20);
};

// Smartphones
$phones = DB::table('products')->where('category', 'Smartphone')->get();
foreach ($phones as $p) {
    $brand = trim($p->brand ?: 'Generic');
    $model = $randSuffix();
    $name = $brand . ' ' . $model;
    DB::table('products')->where('id', $p->id)->update(['name' => $name]);
}

// Tablets and iPad
$tabs = DB::table('products')->whereIn('category', ['Tablet', 'iPad'])->get();
foreach ($tabs as $t) {
    $brand = trim($t->brand ?: 'Generic');
    $model = 'Tab ' . rand(3, 12);
    $name = $brand . ' ' . $model;
    DB::table('products')->where('id', $t->id)->update(['name' => $name]);
}

// Laptops
$laptops = DB::table('products')->where('category', 'Laptop')->get();
foreach ($laptops as $l) {
    $brand = trim($l->brand ?: 'Generic');
    $model = 'Model ' . chr(65 + rand(0, 12)) . rand(100, 999);
    $name = $brand . ' ' . $model;
    DB::table('products')->where('id', $l->id)->update(['name' => $name]);
}

// Smartwatch
$watches = DB::table('products')->where('category', 'Smartwatch')->get();
foreach ($watches as $w) {
    $brand = trim($w->brand ?: 'Generic');
    $model = 'Watch ' . rand(1, 7);
    $name = $brand . ' ' . $model;
    DB::table('products')->where('id', $w->id)->update(['name' => $name]);
}

// Audio & Charger & Powerbank & Accessories: give descriptive names
$accCategories = ['Aksesoris', 'Audio', 'Charger', 'Powerbank'];
$accNames = ['Casing Transparan', 'Tempered Glass', 'Charger 20W', 'Powerbank 10000mAh', 'Earbuds', 'Kabel USB-C', 'Headset', 'Car Charger', 'Speaker Portable'];
foreach ($accCategories as $cat) {
    $items = DB::table('products')->where('category', $cat)->get();
    foreach ($items as $it) {
        $brand = trim($it->brand ?: 'Generic');
        $name = $accNames[array_rand($accNames)] . ($brand !== 'Generic' ? ' ' . $brand : '');
        DB::table('products')->where('id', $it->id)->update(['name' => $name]);
    }
}

// Delete brands not in allowedBrands (keep 'Generic')
$keep = $allowedBrands;
echo "\nTrimming brands table (keeping gadget brands + Generic)...\n";
$deletedBrands = DB::table('brands')->whereNotIn('name', $keep)->delete();
echo "Deleted $deletedBrands brand records.\n";

echo "\nPrune and normalize complete. Total products deleted: $totalDeleted\n";

// Show final counts
$products = DB::table('products')->count();
$brandsCount = DB::table('brands')->count();
echo "Products now: $products\nBrands now: $brandsCount\n";
