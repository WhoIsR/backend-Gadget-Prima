<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Tampilkan semua user
        return response()->json(['data' => User::all()]);
    }

    public function store(Request $request)
    {
        // Validasi & Bikin User Baru
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        $validated['password'] = Hash::make($validated['password']); // Enkripsi password

        $user = User::create($validated);
        return response()->json(['message' => 'User berhasil dibuat', 'data' => $user]);
    }

    public function update(Request $request, $id)
    {
        // Update User
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required'
        ]);

        // Kalau password diisi, update password baru
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return response()->json(['message' => 'User berhasil diupdate']);
    }

    public function destroy($id)
    {
        // Hapus User
        User::destroy($id);
        return response()->json(['message' => 'User berhasil dihapus']);
    }
}
