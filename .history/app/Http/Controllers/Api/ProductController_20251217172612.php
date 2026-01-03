<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // 1. TAMPILKAN SEMUA PRODUK
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    // 2. TAMBAH PRODUK BARU
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required',
            'sku' => 'required|unique:products',
            'category' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        // Simpan ke database
        $product = Product::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan',
            'data' => $product
        ], 201);
    }

    // 3. SHOW DETAIL (Opsional)
    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }
        return response()->json(['success' => true, 'data' => $product]);
    }

    // 4. UPDATE PRODUK
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        // Validasi simpel (sku boleh sama kalau punya diri sendiri)
        $request->validate([
            'name' => 'required',
            'price' => 'numeric',
            'stock' => 'numeric',
        ]);

        $product->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diupdate',
            'data' => $product
        ]);
    }

    // 5. HAPUS PRODUK
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
}
