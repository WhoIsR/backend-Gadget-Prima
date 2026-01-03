<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Brand::all()]);
    }
    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Brands::create($request->all());
        return response()->json(['message' => 'Brand dibuat']);
    }
    public function destroy($id)
    {
        Brand::destroy($id);
        return response()->json(['message' => 'Brand dihapus']);
    }
}
