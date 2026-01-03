<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Brand::all()]);
    }
    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Brand::create($request->all());
        return response()->json(['message' => 'Brand dibuat']);
    }
    public function destroy($id)
    {
        Category::destroy($id);
        return response()->json(['message' => 'Brand dihapus']);
    }
}
