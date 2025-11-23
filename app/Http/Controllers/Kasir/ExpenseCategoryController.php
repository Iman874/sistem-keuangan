<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * Menampilkan form untuk membuat kategori baru
     */
    public function create()
    {
        return view('kasir.expense-categories.create');
    }

    /**
     * Menyimpan kategori baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:harian,bulanan',
        ]);

        ExpenseCategory::create([
            'name' => $request->name,
            'type' => $request->type,
            'created_by' => 'kasir',
            'is_active' => true,
        ]);

        return redirect()->route('kasir.expend.create')
            ->with('success', 'Kategori pengeluaran berhasil ditambahkan.');
    }

    /**
     * Mendapatkan daftar kategori berdasarkan jenis (harian/bulanan)
     */
    public function getByType(Request $request)
    {
        $type = $request->type;

        if (!in_array($type, ['harian', 'bulanan'])) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis kategori tidak valid'
            ]);
        }

        $categories = ExpenseCategory::where('type', $type)
            ->where('is_active', true)
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }
}
