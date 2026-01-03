<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Brand;

// Gadget canonical lists
$canonicalCategories = [
    'Smartphone','Aksesoris','Tablet','iPad','Laptop','Smartwatch','Audio','Powerbank','Charger','Kamera','Monitor','Printer'
];

$allowedBrands = [
    'Apple','Samsung','Xiaomi','OPPO','Vivo','Realme','Infinix','Lenovo','Asus','Acer',
    'Sony','JBL','Anker','Huawei','Honor','OnePlus','Google','Motorola','Nokia'
];

function mapCategory(string $c) use ($canonicalCategories) {
    $s = strtolower($c);
    if (str_contains($s, 'phone') || str_contains($s, 'smart')) return 'Smartphone';
    if (str_contains($s, 'ipad')) return 'iPad';
    if (str_contains($s, 'tablet') || str_contains($s, 'tab')) return 'Tablet';
    if (str_contains($s, 'charger')) return 'Charger';
    if (str_contains($s, 'powerbank') || str_contains($s, 'power')) return 'Powerbank';
    if (str_contains($s, 'watch')) return 'Smartwatch';
    if (str_contains($s, 'laptop')) return 'Laptop';
    if (str_contains($s, 'monitor')) return 'Monitor';
    if (str_contains($s, 'printer')) return 'Printer';
    if (str_contains($s, 'camera') || str_contains($s, 'kamera')) return 'Kamera';
    if (str_contains($s, 'audio') || str_contains($s, 'earbud') || str_contains($s, 'head') || str_contains($s, 'speaker')) return 'Audio';
    if (str_contains($s, 'akses') || str_contains($s, 'case') || str_contains($s, 'casing') || str_contains($s, 'tempered') || str_contains($s, 'charger') || str_contains($s, 'cable')) return 'Aksesoris';
    // default fallback to Aksesoris
    return 'Aksesoris';
}

echo "Starting gadget cleanup mapping...\n";

// Ensure canonical categories & brands exist in categories/brands tables
foreach ($canonicalCategories as $cat) {
    Category::firstOrCreate(['name' => $cat]);
}
foreach ($allowedBrands as $b) {
    Brand::firstOrCreate(['name' => $b]);
}
Brand::firstOrCreate(['name' => 'Generic']);

// Map product categories
$distinct = DB::table('products')->select('category', DB::raw('count(*) as cnt'))->groupBy('category')->get();
$changed = 0;
foreach ($distinct as $d) {
    $old = $d->category ?? '';
    $new = mapCategory($old);
    if ($old !== $new) {
        $affected = DB::table('products')->where('category', $old)->update(['category' => $new]);
        printf("Mapped category '%s' -> '%s' (updated %d rows)\n", $old, $new, $affected);
        $changed += $affected;
    }
}

// Map brands: keep allowed list, others -> Generic
$brandDistinct = DB::table('products')->select('brand', DB::raw('count(*) as cnt'))->groupBy('brand')->get();
$brandChanged = 0;
foreach ($brandDistinct as $b) {
    $old = $b->brand ?? '';
    if (! in_array($old, $allowedBrands) ) {
        $affected = DB::table('products')->where('brand', $old)->update(['brand' => 'Generic']);
        if ($affected) {
            printf("Mapped brand '%s' -> 'Generic' (updated %d rows)\n", $old, $affected);
            $brandChanged += $affected;
        }
    }
}

echo "\nSummary:\n";
echo "Total product category updates: $changed\n";
echo "Total product brand updates: $brandChanged\n";

// Show counts after cleanup
echo "\nCounts after cleanup:\n";
$p = DB::table('products')->count();
$cats = DB::table('products')->select('category', DB::raw('count(*) as cnt'))->groupBy('category')->orderByDesc('cnt')->get();
echo "Products: $p\n";
foreach ($cats as $c) {
    printf("%s => %d\n", $c->category ?? '(null)', $c->cnt);
}

echo "\nBrands (product counts):\n";
$bs = DB::table('products')->select('brand', DB::raw('count(*) as cnt'))->groupBy('brand')->orderByDesc('cnt')->get();
foreach ($bs as $b) {
    printf("%s => %d\n", $b->brand ?? '(null)', $b->cnt);
}

echo "\nDone.\n";
