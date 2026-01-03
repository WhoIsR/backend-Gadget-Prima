<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'sku' => 'required|unique:products',
            'category' => 'required',
            'buy_price' => 'required|numeric',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'min_stock' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = url('storage/' . $imagePath);
        }

        $product = Product::create($data);

        // --- CATAT RIWAYAT STOK AWAL ---
        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => Auth::id() ?? 1,
            'type' => 'in',
            'amount' => $product->stock,
            'current_stock' => $product->stock,
            'reason' => 'Stok Awal oleh ' . (Auth::user()->name ?? 'System'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan',
            'data' => $product
        ], 201);
    }


    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $product]);
    }


    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) return response()->json(['message' => 'Not Found'], 404);

        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'buy_price' => 'numeric',
            'price' => 'numeric',
            'stock' => 'numeric',
            'min_stock' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $oldStock = $product->stock;
        $newStock = (int) $request->stock;

        $data = $request->all();
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = url('storage/' . $imagePath);
        } else {
            unset($data['image']);
        }

        $product->update($data);

        if ($oldStock != $newStock) {
            $diff = $newStock - $oldStock;
            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => Auth::id() ?? 1,
                'type' => $diff > 0 ? 'in' : 'out',
                'amount' => abs($diff),
                'current_stock' => $newStock,
                'reason' => 'Koreksi Stok oleh ' . (Auth::user()->role ?? 'test'),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produk update',
            'data' => $product
        ]);
    }


    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ]);
    }

    public function history($id)
    {
        $history = StockMovement::with('user')
            ->where('product_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $history]);
    }
}
