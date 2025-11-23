<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dailyCategories = ExpenseCategory::daily()->get();
        $monthlyCategories = ExpenseCategory::monthly()->get();

        return view('owner.expense-categories.index', compact('dailyCategories', 'monthlyCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('owner.expense-categories.create');
    }

    /**
     * Store a newly created resource in storage.
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
            'created_by' => 'owner',
            'is_active' => true,
        ]);

        return redirect()->route('owner.expense-categories.index')
            ->with('success', 'Kategori pengeluaran berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = ExpenseCategory::findOrFail($id);
        return view('owner.expense-categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = ExpenseCategory::findOrFail($id);
        return view('owner.expense-categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $category = ExpenseCategory::findOrFail($id);
        $category->name = $request->name;

        // Type cannot be changed as it might break existing expenses

        // Handle active status update if the field was submitted
        if ($request->has('is_active')) {
            $category->is_active = $request->is_active;
        }

        $category->save();

        return redirect()->route('owner.expense-categories.index')
            ->with('success', 'Kategori pengeluaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = ExpenseCategory::findOrFail($id);

        // Cek apakah kategori digunakan oleh pengeluaran
        if ($category->expenses()->count() > 0) {
            return back()->with('error', 'Kategori ini tidak dapat dihapus karena sudah digunakan dalam pengeluaran.');
        }

        $category->delete();

        return redirect()->route('owner.expense-categories.index')
            ->with('success', 'Kategori pengeluaran berhasil dihapus.');
    }
}
