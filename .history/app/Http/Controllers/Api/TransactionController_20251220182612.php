<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{

    public function index()
    {
        $orders = Order::with(['items', 'cashier'])
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();


        $formatted = $orders->map(function ($order) {
            return [
                'id' => $order->invoice_number,
                'date' => $order->created_at,
                'total' => (float)$order->total_amount,
                'cashierName' => $order->cashier->name ?? 'Unknown',
                'cashierId' => $order->user_id,
                'paymentMethod' => $order->payment_method,
                'items' => $order->items->map(function ($item) {
                    return [
                        'productName' => $item->product_name,
                        'quantity' => $item->quantity,
                        'price' => (float)$item->price,
                        'subtotal' => (float)$item->subtotal
                    ];
                })
            ];
        });

        return response()->json(['data' => $formatted]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'payment_method' => 'required|in:cash,card,e-wallet',
            'total' => 'required|numeric',
            'tax' => 'required|numeric'
        ]);

        // MULAI TRANSAKSI DATABASE
        DB::beginTransaction();

        try {
            // A. Buat Order Header
            $order = Order::create([
                'invoice_number' => 'TRX-' . time() . rand(100, 999),
                'user_id' => Auth::id(),
                'total_amount' => $request->total,
                'tax' => $request->tax,
                'payment_method' => $request->payment_method,
            ]);

            // B. Loop Barang Belanjaan
            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['productId']);

                // Cek Stok
                if (!$product || $product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$item['productName']} tidak cukup!");
                }

                // C. Kurangi Stok
                $product->decrement('stock', $item['quantity']);

                // D. Catat Order Item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                // E. Catat Kartu Stok (History Gudang)
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'out', // Barang Keluar
                    'amount' => $item['quantity'],
                    'current_stock' => $product->stock,
                    'reason' => 'Terjual di TRX ' . $order->invoice_number,
                ]);
            }

            DB::commit(); // Simpan Permanen kalau sukses semua

            return response()->json([
                'success' => true,
                'message' => 'Transaksi Berhasil',
                'transaction_id' => $order->invoice_number
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua kalau ada error
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
