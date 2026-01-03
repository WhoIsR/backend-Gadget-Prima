<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Expense::orderBy('date', 'desc')->get()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required',
            'category' => 'required',
            'amount' => 'required|numeric'
        ]);

        Expense::create($validated);
        return response()->json(['message' => 'Pengeluaran dicatat']);
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);
        $expense->update($request->all());
        return response()->json(['message' => 'Pengeluaran diupdate']);
    }

    public function destroy($id)
    {
        Expense::destroy($id);
        return response()->json(['message' => 'Pengeluaran dihapus']);
    }
}
