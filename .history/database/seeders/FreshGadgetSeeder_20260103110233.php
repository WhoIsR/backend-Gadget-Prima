<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockMovement;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;

class FreshGadgetSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Truncate all tables (disable FK checks temporarily)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::table('stock_movements')->truncate();
        DB::table('expenses')->truncate();
        DB::table('products')->truncate();
        DB::table('categories')->truncate();
        DB::table('brands')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        echo "Tables truncated.\n";

        // 2. Create categories
        $categories = ['Smartphone', 'Tablet', 'Laptop', 'Smartwatch', 'Audio', 'Aksesoris'];
        foreach ($categories as $cat) {
            Category::create(['name' => $cat]);
        }

        // 3. Create brands
        $brands = ['Apple', 'Samsung', 'Xiaomi', 'OPPO', 'Vivo', 'Realme', 'Asus', 'Lenovo', 'Sony', 'JBL', 'Anker'];
        foreach ($brands as $b) {
            Brand::create(['name' => $b]);
        }

        echo "Categories and brands created.\n";

        // 4. Create 50 realistic products with specs
        $products = $this->getRealisticProducts();
        foreach ($products as $p) {
            Product::create($p);
        }

        echo "50 products created.\n";

        // 5. Create users if not exist
        $this->call(UserSeeder::class);

        // 6. Create orders with varied timestamps (past 6 months)
        $this->createOrdersWithVariedDates();

        // 7. Create expenses with varied dates
        $this->createExpenses();

        echo "Seeding complete!\n";
    }

    private function getRealisticProducts(): array
    {
        return [
            // SMARTPHONES (20)
            ['name' => 'Apple iPhone 15 Pro Max', 'sku' => 'IP15PM-256-BLK', 'category' => 'Smartphone', 'brand' => 'Apple', 'description' => 'Warna: Black Titanium | Storage: 256GB | RAM: 8GB | Layar: 6.7" Super Retina XDR | Chip: A17 Pro | Kamera: 48MP + 12MP + 12MP', 'buy_price' => 19500000, 'price' => 22999000, 'stock' => 15, 'min_stock' => 3, 'status' => 'active'],
            ['name' => 'Apple iPhone 15 Pro', 'sku' => 'IP15P-128-WHT', 'category' => 'Smartphone', 'brand' => 'Apple', 'description' => 'Warna: White Titanium | Storage: 128GB | RAM: 8GB | Layar: 6.1" Super Retina XDR | Chip: A17 Pro | Kamera: 48MP + 12MP + 12MP', 'buy_price' => 16500000, 'price' => 18999000, 'stock' => 20, 'min_stock' => 5, 'status' => 'active'],
            ['name' => 'Apple iPhone 15', 'sku' => 'IP15-128-PNK', 'category' => 'Smartphone', 'brand' => 'Apple', 'description' => 'Warna: Pink | Storage: 128GB | RAM: 6GB | Layar: 6.1" Super Retina XDR | Chip: A16 Bionic | Kamera: 48MP + 12MP', 'buy_price' => 12500000, 'price' => 14999000, 'stock' => 25, 'min_stock' => 5, 'status' => 'active'],
            ['name' => 'Samsung Galaxy S24 Ultra', 'sku' => 'SGS24U-512-GRY', 'category' => 'Smartphone', 'brand' => 'Samsung', 'description' => 'Warna: Titanium Gray | Storage: 512GB | RAM: 12GB | Layar: 6.8" Dynamic AMOLED 2X | Chip: Snapdragon 8 Gen 3 | Kamera: 200MP + 12MP + 50MP + 10MP', 'buy_price' => 18000000, 'price' => 21499000, 'stock' => 12, 'min_stock' => 3, 'status' => 'active'],
            ['name' => 'Samsung Galaxy S24+', 'sku' => 'SGS24P-256-VLT', 'category' => 'Smartphone', 'brand' => 'Samsung', 'description' => 'Warna: Cobalt Violet | Storage: 256GB | RAM: 12GB | Layar: 6.7" Dynamic AMOLED 2X | Chip: Exynos 2400 | Kamera: 50MP + 12MP + 10MP', 'buy_price' => 13000000, 'price' => 15999000, 'stock' => 18, 'min_stock' => 4, 'status' => 'active'],
            ['name' => 'Samsung Galaxy A55 5G', 'sku' => 'SGA55-256-NVY', 'category' => 'Smartphone', 'brand' => 'Samsung', 'description' => 'Warna: Awesome Navy | Storage: 256GB | RAM: 8GB | Layar: 6.6" Super AMOLED | Chip: Exynos 1480 | Kamera: 50MP + 12MP + 5MP', 'buy_price' => 5200000, 'price' => 6499000, 'stock' => 30, 'min_stock' => 5, 'status' => 'active'],
            ['name' => 'Xiaomi 14 Ultra', 'sku' => 'XI14U-512-BLK', 'category' => 'Smartphone', 'brand' => 'Xiaomi', 'description' => 'Warna: Black | Storage: 512GB | RAM: 16GB | Layar: 6.73" LTPO AMOLED | Chip: Snapdragon 8 Gen 3 | Kamera: Leica 50MP Quad', 'buy_price' => 14500000, 'price' => 17999000, 'stock' => 10, 'min_stock' => 2, 'status' => 'active'],
            ['name' => 'Xiaomi Redmi Note 13 Pro', 'sku' => 'XIRN13P-256-GRN', 'category' => 'Smartphone', 'brand' => 'Xiaomi', 'description' => 'Warna: Forest Green | Storage: 256GB | RAM: 8GB | Layar: 6.67" AMOLED 120Hz | Chip: Snapdragon 7s Gen 2 | Kamera: 200MP + 8MP + 2MP', 'buy_price' => 3200000, 'price' => 3999000, 'stock' => 40, 'min_stock' => 8, 'status' => 'active'],
            ['name' => 'OPPO Find X7 Ultra', 'sku' => 'OPFX7U-512-BRN', 'category' => 'Smartphone', 'brand' => 'OPPO', 'description' => 'Warna: Sepia Brown | Storage: 512GB | RAM: 16GB | Layar: 6.82" LTPO AMOLED | Chip: Snapdragon 8 Gen 3 | Kamera: Hasselblad 50MP Quad', 'buy_price' => 15000000, 'price' => 18499000, 'stock' => 8, 'min_stock' => 2, 'status' => 'active'],
            ['name' => 'OPPO Reno 11 5G', 'sku' => 'OPRN11-256-BLU', 'category' => 'Smartphone', 'brand' => 'OPPO', 'description' => 'Warna: Wave Blue | Storage: 256GB | RAM: 12GB | Layar: 6.7" AMOLED 120Hz | Chip: Dimensity 7050 | Kamera: 50MP + 32MP + 8MP', 'buy_price' => 4800000, 'price' => 5999000, 'stock' => 25, 'min_stock' => 5, 'status' => 'active'],
            ['name' => 'Vivo X100 Pro', 'sku' => 'VVX100P-512-ORG', 'category' => 'Smartphone', 'brand' => 'Vivo', 'description' => 'Warna: Asteroid Black | Storage: 512GB | RAM: 16GB | Layar: 6.78" LTPO AMOLED | Chip: Dimensity 9300 | Kamera: ZEISS 50MP Triple', 'buy_price' => 13500000, 'price' => 16499000, 'stock' => 10, 'min_stock' => 2, 'status' => 'active'],
            ['name' => 'Vivo V30 5G', 'sku' => 'VVV30-256-PRP', 'category' => 'Smartphone', 'brand' => 'Vivo', 'description' => 'Warna: Peacock Purple | Storage: 256GB | RAM: 12GB | Layar: 6.78" AMOLED 120Hz | Chip: Snapdragon 7 Gen 3 | Kamera: 50MP + 50MP', 'buy_price' => 5000000, 'price' => 6299000, 'stock' => 22, 'min_stock' => 4, 'status' => 'active'],
            ['name' => 'Realme GT 5 Pro', 'sku' => 'RMGT5P-256-BLK', 'category' => 'Smartphone', 'brand' => 'Realme', 'description' => 'Warna: Bright Moon | Storage: 256GB | RAM: 12GB | Layar: 6.78" LTPO AMOLED | Chip: Snapdragon 8 Gen 3 | Kamera: 50MP + 8MP + 50MP', 'buy_price' => 7500000, 'price' => 9499000, 'stock' => 15, 'min_stock' => 3, 'status' => 'active'],
            ['name' => 'Realme 12 Pro+ 5G', 'sku' => 'RM12PP-256-BLU', 'category' => 'Smartphone', 'brand' => 'Realme', 'description' => 'Warna: Submarine Blue | Storage: 256GB | RAM: 8GB | Layar: 6.7" AMOLED 120Hz | Chip: Snapdragon 7s Gen 2 | Kamera: 64MP + 8MP + 32MP', 'buy_price' => 4500000, 'price' => 5699000, 'stock' => 28, 'min_stock' => 5, 'status' => 'active'],

            // TABLETS (8)
            ['name' => 'Apple iPad Pro 12.9" M2', 'sku' => 'IPDP129-256-SLV', 'category' => 'Tablet', 'brand' => 'Apple', 'description' => 'Warna: Silver | Storage: 256GB | RAM: 8GB | Layar: 12.9" Liquid Retina XDR | Chip: M2 | WiFi + Cellular | Face ID', 'buy_price' => 16000000, 'price' => 18999000, 'stock' => 8, 'min_stock' => 2, 'status' => 'active'],
            ['name' => 'Apple iPad Air 5', 'sku' => 'IPDA5-64-BLU', 'category' => 'Tablet', 'brand' => 'Apple', 'description' => 'Warna: Blue | Storage: 64GB | RAM: 8GB | Layar: 10.9" Liquid Retina | Chip: M1 | WiFi | Touch ID', 'buy_price' => 8500000, 'price' => 10499000, 'stock' => 15, 'min_stock' => 3, 'status' => 'active'],
            ['name' => 'Samsung Galaxy Tab S9 Ultra', 'sku' => 'SGTS9U-512-GRF', 'category' => 'Tablet', 'brand' => 'Samsung', 'description' => 'Warna: Graphite | Storage: 512GB | RAM: 12GB | Layar: 14.6" Dynamic AMOLED 2X | Chip: Snapdragon 8 Gen 2 | S Pen included', 'buy_price' => 15000000, 'price' => 18499000, 'stock' => 6, 'min_stock' => 2, 'status' => 'active'],
            ['name' => 'Samsung Galaxy Tab S9 FE', 'sku' => 'SGTS9FE-128-MNT', 'category' => 'Tablet', 'brand' => 'Samsung', 'description' => 'Warna: Mint | Storage: 128GB | RAM: 6GB | Layar: 10.9" TFT LCD 90Hz | Chip: Exynos 1380 | S Pen included | IP68', 'buy_price' => 5500000, 'price' => 6999000, 'stock' => 20, 'min_stock' => 4, 'status' => 'active'],
            ['name' => 'Xiaomi Pad 6', 'sku' => 'XIPAD6-256-GRY', 'category' => 'Tablet', 'brand' => 'Xiaomi', 'description' => 'Warna: Gravity Gray | Storage: 256GB | RAM: 8GB | Layar: 11" IPS LCD 144Hz | Chip: Snapdragon 870 | Quad speakers', 'buy_price' => 4200000, 'price' => 5299000, 'stock' => 18, 'min_stock' => 4, 'status' => 'active'],
            ['name' => 'Lenovo Tab P12 Pro', 'sku' => 'LNTP12P-256-GRY', 'category' => 'Tablet', 'brand' => 'Lenovo', 'description' => 'Warna: Storm Grey | Storage: 256GB | RAM: 8GB | Layar: 12.6" AMOLED 120Hz | Chip: Snapdragon 870 | JBL Quad speakers', 'buy_price' => 7500000, 'price' => 9299000, 'stock' => 10, 'min_stock' => 2, 'status' => 'active'],

            // LAPTOPS (8)
            ['name' => 'Apple MacBook Air M3 13"', 'sku' => 'MBAM3-256-MDN', 'category' => 'Laptop', 'brand' => 'Apple', 'description' => 'Warna: Midnight | Storage: 256GB SSD | RAM: 8GB | Layar: 13.6" Liquid Retina | Chip: Apple M3 8-core | Baterai: 18 jam', 'buy_price' => 16500000, 'price' => 18999000, 'stock' => 12, 'min_stock' => 3, 'status' => 'active'],
            ['name' => 'Apple MacBook Pro 14" M3 Pro', 'sku' => 'MBP14M3P-512-SPC', 'category' => 'Laptop', 'brand' => 'Apple', 'description' => 'Warna: Space Black | Storage: 512GB SSD | RAM: 18GB | Layar: 14.2" Liquid Retina XDR | Chip: M3 Pro 11-core | Baterai: 17 jam', 'buy_price' => 28000000, 'price' => 32999000, 'stock' => 6, 'min_stock' => 2, 'status' => 'active'],
            ['name' => 'Asus ROG Zephyrus G14', 'sku' => 'ROGZG14-512-WHT', 'category' => 'Laptop', 'brand' => 'Asus', 'description' => 'Warna: Moonlight White | Storage: 512GB SSD | RAM: 16GB DDR5 | Layar: 14" QHD+ 165Hz | CPU: AMD Ryzen 9 8945HS | GPU: RTX 4060', 'buy_price' => 22000000, 'price' => 26499000, 'stock' => 8, 'min_stock' => 2, 'status' => 'active'],
            ['name' => 'Asus Vivobook 15 OLED', 'sku' => 'ASVB15O-512-SLV', 'category' => 'Laptop', 'brand' => 'Asus', 'description' => 'Warna: Cool Silver | Storage: 512GB SSD | RAM: 16GB | Layar: 15.6" OLED FHD | CPU: Intel Core i5-13500H | Baterai: 8 jam', 'buy_price' => 9500000, 'price' => 11999000, 'stock' => 15, 'min_stock' => 3, 'status' => 'active'],
            ['name' => 'Lenovo ThinkPad X1 Carbon Gen 11', 'sku' => 'TPX1C11-512-BLK', 'category' => 'Laptop', 'brand' => 'Lenovo', 'description' => 'Warna: Black | Storage: 512GB SSD | RAM: 16GB | Layar: 14" 2.8K OLED | CPU: Intel Core i7-1365U vPro | Baterai: 15 jam', 'buy_price' => 24000000, 'price' => 28999000, 'stock' => 5, 'min_stock' => 1, 'status' => 'active'],
            ['name' => 'Lenovo IdeaPad Slim 5', 'sku' => 'LIPS5-512-GRY', 'category' => 'Laptop', 'brand' => 'Lenovo', 'description' => 'Warna: Storm Grey | Storage: 512GB SSD | RAM: 16GB | Layar: 14" 2.8K OLED | CPU: AMD Ryzen 7 7730U | Baterai: 12 jam', 'buy_price' => 10500000, 'price' => 12999000, 'stock' => 12, 'min_stock' => 3, 'status' => 'active'],

            // SMARTWATCH (6)
            ['name' => 'Apple Watch Ultra 2', 'sku' => 'AWU2-49-TIT', 'category' => 'Smartwatch', 'brand' => 'Apple', 'description' => 'Warna: Natural Titanium | Case: 49mm | Layar: Always-On Retina LTPO OLED | Chip: S9 SiP | GPS + Cellular | WR100 | Baterai: 36 jam', 'buy_price' => 12500000, 'price' => 14999000, 'stock' => 10, 'min_stock' => 2, 'status' => 'active'],
            ['name' => 'Apple Watch Series 9', 'sku' => 'AWS9-45-MDN', 'category' => 'Smartwatch', 'brand' => 'Apple', 'description' => 'Warna: Midnight Aluminum | Case: 45mm | Layar: Always-On Retina LTPO OLED | Chip: S9 SiP | GPS | WR50 | Baterai: 18 jam', 'buy_price' => 6500000, 'price' => 7999000, 'stock' => 18, 'min_stock' => 4, 'status' => 'active'],
            ['name' => 'Samsung Galaxy Watch 6 Classic', 'sku' => 'SGW6C-47-SLV', 'category' => 'Smartwatch', 'brand' => 'Samsung', 'description' => 'Warna: Silver | Case: 47mm | Layar: 1.5" Super AMOLED | Rotating Bezel | Chip: Exynos W930 | GPS + LTE | WR50 | Baterai: 40 jam', 'buy_price' => 5200000, 'price' => 6499000, 'stock' => 15, 'min_stock' => 3, 'status' => 'active'],
            ['name' => 'Samsung Galaxy Watch FE', 'sku' => 'SGWFE-40-PNK', 'category' => 'Smartwatch', 'brand' => 'Samsung', 'description' => 'Warna: Pink Gold | Case: 40mm | Layar: 1.2" Super AMOLED | Chip: Exynos W920 | GPS | WR50 | Baterai: 30 jam', 'buy_price' => 2800000, 'price' => 3499000, 'stock' => 25, 'min_stock' => 5, 'status' => 'active'],
            ['name' => 'Xiaomi Watch 2 Pro', 'sku' => 'XIW2P-46-BLK', 'category' => 'Smartwatch', 'brand' => 'Xiaomi', 'description' => 'Warna: Black | Case: 46mm | Layar: 1.43" AMOLED | Chip: Snapdragon W5+ Gen 1 | GPS + LTE | WR50 | Baterai: 65 jam', 'buy_price' => 2800000, 'price' => 3599000, 'stock' => 20, 'min_stock' => 4, 'status' => 'active'],

            // AUDIO (8)
            ['name' => 'Apple AirPods Pro 2nd Gen', 'sku' => 'AAPP2-USB-WHT', 'category' => 'Audio', 'brand' => 'Apple', 'description' => 'Warna: White | Tipe: TWS In-Ear | ANC + Transparency Mode | Chip: H2 | Spatial Audio | MagSafe Charging Case | Baterai: 6 jam (30 jam with case)', 'buy_price' => 3200000, 'price' => 3999000, 'stock' => 30, 'min_stock' => 6, 'status' => 'active'],
            ['name' => 'Apple AirPods Max', 'sku' => 'AAPM-SPC', 'category' => 'Audio', 'brand' => 'Apple', 'description' => 'Warna: Space Gray | Tipe: Over-Ear Headphone | ANC + Transparency | Chip: H1 | Spatial Audio | Baterai: 20 jam', 'buy_price' => 7500000, 'price' => 9299000, 'stock' => 8, 'min_stock' => 2, 'status' => 'active'],
            ['name' => 'Samsung Galaxy Buds 3 Pro', 'sku' => 'SGB3P-SLV', 'category' => 'Audio', 'brand' => 'Samsung', 'description' => 'Warna: Silver | Tipe: TWS In-Ear | ANC + Ambient Sound | 360 Audio | IP57 | Baterai: 7 jam (30 jam with case)', 'buy_price' => 2800000, 'price' => 3499000, 'stock' => 25, 'min_stock' => 5, 'status' => 'active'],
            ['name' => 'Sony WH-1000XM5', 'sku' => 'SYWH1KXM5-BLK', 'category' => 'Audio', 'brand' => 'Sony', 'description' => 'Warna: Black | Tipe: Over-Ear Headphone | Industry-leading ANC | LDAC | 360 Reality Audio | Baterai: 30 jam', 'buy_price' => 4200000, 'price' => 5299000, 'stock' => 12, 'min_stock' => 3, 'status' => 'active'],
            ['name' => 'Sony WF-1000XM5', 'sku' => 'SYWF1KXM5-SLV', 'category' => 'Audio', 'brand' => 'Sony', 'description' => 'Warna: Platinum Silver | Tipe: TWS In-Ear | ANC | LDAC | 360 Reality Audio | IPX4 | Baterai: 8 jam (24 jam with case)', 'buy_price' => 3500000, 'price' => 4399000, 'stock' => 18, 'min_stock' => 4, 'status' => 'active'],
            ['name' => 'JBL Flip 6', 'sku' => 'JBLFLIP6-RED', 'category' => 'Audio', 'brand' => 'JBL', 'description' => 'Warna: Red | Tipe: Portable Bluetooth Speaker | Output: 30W | IP67 Waterproof | PartyBoost | Baterai: 12 jam', 'buy_price' => 1400000, 'price' => 1799000, 'stock' => 35, 'min_stock' => 7, 'status' => 'active'],
            ['name' => 'JBL Charge 5', 'sku' => 'JBLCHG5-BLU', 'category' => 'Audio', 'brand' => 'JBL', 'description' => 'Warna: Blue | Tipe: Portable Bluetooth Speaker | Output: 40W | IP67 Waterproof | Powerbank Function | Baterai: 20 jam', 'buy_price' => 2000000, 'price' => 2499000, 'stock' => 28, 'min_stock' => 5, 'status' => 'active'],

            // AKSESORIS (6)
            ['name' => 'Anker 737 Power Bank 24K', 'sku' => 'ANK737-24K', 'category' => 'Aksesoris', 'brand' => 'Anker', 'description' => 'Kapasitas: 24000mAh | Output: 140W USB-C PD | Input: 140W | Port: 2x USB-C, 1x USB-A | Layar Smart Digital', 'buy_price' => 1800000, 'price' => 2299000, 'stock' => 25, 'min_stock' => 5, 'status' => 'active'],
            ['name' => 'Anker Nano II 65W Charger', 'sku' => 'ANKN2-65W', 'category' => 'Aksesoris', 'brand' => 'Anker', 'description' => 'Output: 65W GaN | Port: 1x USB-C | PPS Support | Foldable Plug | Kompatibel MacBook/iPhone/Samsung', 'buy_price' => 550000, 'price' => 699000, 'stock' => 40, 'min_stock' => 8, 'status' => 'active'],
            ['name' => 'Anker 543 USB-C to USB-C Cable', 'sku' => 'ANK543-C2C', 'category' => 'Aksesoris', 'brand' => 'Anker', 'description' => 'Panjang: 1.8m | Material: Bio-Nylon | Rating: 240W USB-C PD | USB 2.0 Data Transfer | Warna: Black', 'buy_price' => 180000, 'price' => 249000, 'stock' => 60, 'min_stock' => 12, 'status' => 'active'],
            ['name' => 'Apple MagSafe Charger', 'sku' => 'APMAGSF-1M', 'category' => 'Aksesoris', 'brand' => 'Apple', 'description' => 'Panjang Kabel: 1m | Output: 15W Wireless | Kompatibel: iPhone 12/13/14/15 Series | Magnetic Alignment', 'buy_price' => 550000, 'price' => 699000, 'stock' => 35, 'min_stock' => 7, 'status' => 'active'],
            ['name' => 'Samsung 45W Travel Adapter', 'sku' => 'SG45WTA-WHT', 'category' => 'Aksesoris', 'brand' => 'Samsung', 'description' => 'Output: 45W Super Fast Charging 2.0 | Port: 1x USB-C | PPS Support | Warna: White | Cable not included', 'buy_price' => 350000, 'price' => 449000, 'stock' => 45, 'min_stock' => 9, 'status' => 'active'],
            ['name' => 'Xiaomi 67W GaN Charger', 'sku' => 'XI67WGAN-BLK', 'category' => 'Aksesoris', 'brand' => 'Xiaomi', 'description' => 'Output: 67W GaN | Port: 1x USB-C | Support: Xiaomi HyperCharge | Foldable Plug | Warna: Black', 'buy_price' => 280000, 'price' => 379000, 'stock' => 50, 'min_stock' => 10, 'status' => 'active'],
        ];
    }

    private function createOrdersWithVariedDates(): void
    {
        $users = User::all();
        $products = Product::all();
        $paymentMethods = ['cash', 'card', 'e-wallet'];

        // Create 80 orders over the past 6 months
        for ($i = 0; $i < 80; $i++) {
            // Random date in past 6 months
            $daysAgo = rand(0, 180);
            $orderDate = Carbon::now()->subDays($daysAgo)->setTime(rand(8, 21), rand(0, 59), rand(0, 59));

            $user = $users->random();
            $invoice = 'INV-' . $orderDate->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            $order = Order::create([
                'invoice_number' => $invoice,
                'user_id' => $user->id,
                'total_amount' => 0,
                'tax' => 0,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // Add 1-3 items per order
            $itemCount = rand(1, 3);
            $selectedProducts = $products->random($itemCount);
            $total = 0;

            foreach ($selectedProducts as $product) {
                $qty = rand(1, 3);
                $price = $product->price;
                $subtotal = $price * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                // Create stock movement for sale
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'type' => 'out',
                    'amount' => $qty,
                    'current_stock' => $product->stock - $qty,
                    'reason' => 'Penjualan ' . $invoice,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                $total += $subtotal;
            }

            $tax = round($total * 0.11); // PPN 11%
            $order->update(['total_amount' => $total, 'tax' => $tax]);
        }

        echo "80 orders created with varied dates.\n";
    }

    private function createExpenses(): void
    {
        $expenseData = [
            ['category' => 'Operasional', 'desc' => 'Listrik dan Air bulan '],
            ['category' => 'Operasional', 'desc' => 'Internet dan Telepon bulan '],
            ['category' => 'Gaji', 'desc' => 'Gaji Karyawan bulan '],
            ['category' => 'Marketing', 'desc' => 'Iklan Instagram bulan '],
            ['category' => 'Maintenance', 'desc' => 'Service AC Toko'],
            ['category' => 'Operasional', 'desc' => 'ATK dan Supplies'],
            ['category' => 'Maintenance', 'desc' => 'Perbaikan Komputer Kasir'],
        ];

        $months = ['Juli 2025', 'Agustus 2025', 'September 2025', 'Oktober 2025', 'November 2025', 'Desember 2025'];

        foreach ($months as $idx => $month) {
            $monthDate = Carbon::now()->subMonths(6 - $idx);

            foreach ($expenseData as $exp) {
                $description = str_contains($exp['desc'], 'bulan') ? $exp['desc'] . $month : $exp['desc'];
                $amount = match($exp['category']) {
                    'Gaji' => rand(8000000, 15000000),
                    'Operasional' => rand(500000, 3000000),
                    'Marketing' => rand(300000, 2000000),
                    'Maintenance' => rand(200000, 1500000),
                    default => rand(100000, 500000),
                };

                Expense::create([
                    'date' => $monthDate->format('Y-m-d'),
                    'description' => $description,
                    'category' => $exp['category'],
                    'amount' => $amount,
                    'created_at' => $monthDate,
                    'updated_at' => $monthDate,
                ]);
            }
        }

        echo "Expenses created.\n";
    }
}
