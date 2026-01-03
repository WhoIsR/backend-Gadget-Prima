<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();

        // 1. KARTU ATAS (Ringkasan)
        $todaySales = Order::whereDate('created_at', $today)->sum('total_amount');
        $todayTrans = Order::whereDate('created_at', $today)->count();
        $weekSales = Order::whereBetween('created_at', [$startOfWeek, Carbon::now()])->sum('total_amount');

        $totalProducts = Product::count();
        $lowStock = Product::whereColumn('stock', '<=', 'min_stock')->count();

        // 2. CHART GRAFIK (7 Hari Terakhir)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $sum = Order::whereDate('created_at', $date)->sum('total_amount');
            $chartData[] = [
                'date' => $date->format('d M'), 
                'total' => (int)$sum
            ];
        }

        // 3. PIE CHART KATEGORI
        // Hitung produk terjual per kategori via relasi order items
        $categorySales = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.category', DB::raw('sum(order_items.quantity) as total_qty'))
            ->groupBy('products.category')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'cards' => [
                    'today_sales' => $todaySales,
                    'today_transactions' => $todayTrans,
                    'total_products' => $totalProducts,
                    'low_stock' => $lowStock,
                    'week_sales' => $weekSales
                ],
                'chart' => $chartData,
                'categories' => $categorySales
            ]
        ]);
    }
}
