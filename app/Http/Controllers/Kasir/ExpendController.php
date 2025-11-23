<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Expend;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get today's date
        $today = now()->startOfDay()->format('Y-m-d');

        // Get only today's expenses for the current kasir, grouped by date
        $dailyExpenses = Expend::where('user_id', auth()->id())
            ->daily()
            ->whereDate('date', $today)
            ->latest('date')
            ->get()
            ->groupBy('date');

        // For monthly expenses, still show the current month's data
        // but only the current day for display in the UI
        $monthlyExpenses = Expend::where('user_id', auth()->id())
            ->monthly()
            ->whereDate('date', $today)
            ->latest('date')
            ->get()
            ->groupBy(function ($date) {
                return $date->date->format('Y-m'); // Group by year-month
            });

        return view('kasir.expend.index', compact('dailyExpenses', 'monthlyExpenses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dailyCategories = ExpenseCategory::where('type', 'harian')->active()->get();
        $monthlyCategories = ExpenseCategory::where('type', 'bulanan')->active()->get();

        return view('kasir.expend.create', compact('dailyCategories', 'monthlyCategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'session' => 'required|in:pagi,sore',
            'type' => 'required|in:harian,bulanan',
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'required|string',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        $request->validate($rules);

        // Create a new expense with data from the request
        $expend = new Expend();
        $expend->user_id = auth()->id(); // Set the current user ID
        $expend->session = $request->session;
        $expend->type = $request->type;
        $expend->category_id = $request->category_id;
        $expend->amount = $request->amount;
        $expend->date = $request->date;
        $expend->time = now()->format('H:i:s'); // Set current time
        $expend->description = $request->description;

        // Handle image upload
        if ($request->hasFile('receipt_image')) {
            $image = $request->file('receipt_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/receipts', $imageName);
            $expend->receipt_image = $imageName;
        }

        $expend->save();

        return redirect()->route('kasir.expend.index')
            ->with('success', 'Data pengeluaran berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Find expense by ID and ensure it belongs to the current user
        $expend = Expend::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('kasir.expend.show', compact('expend'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Find expense by ID and ensure it belongs to the current user
        $expend = Expend::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('kasir.expend.edit', compact('expend'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Find expense by ID and ensure it belongs to the current user
        $expend = Expend::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $rules = [
            'session' => 'required|in:pagi,sore',
            'type' => 'required|in:harian,bulanan',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'description' => 'required|string',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        $request->validate($rules);

        // Update expense fields
        $expend->session = $request->session;
        $expend->type = $request->type;
        $expend->amount = $request->amount;
        $expend->date = $request->date;
        // Note: We don't update the time on edit to preserve original time
        $expend->description = $request->description;

        // Handle image upload
        if ($request->hasFile('receipt_image')) {
            // Delete old image if exists
            if ($expend->receipt_image) {
                Storage::delete('public/receipts/' . $expend->receipt_image);
            }

            $image = $request->file('receipt_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/receipts', $imageName);
            $expend->receipt_image = $imageName;
        }

        $expend->save();

        return redirect()->route('kasir.expend.index')
            ->with('success', 'Data pengeluaran berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Find expense by ID and ensure it belongs to the current user
        $expend = Expend::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Delete image if exists
        if ($expend->receipt_image) {
            Storage::delete('public/receipts/' . $expend->receipt_image);
        }

        $expend->delete();

        return redirect()->route('kasir.expend.index')
            ->with('success', 'Data pengeluaran berhasil dihapus!');
    }
}
