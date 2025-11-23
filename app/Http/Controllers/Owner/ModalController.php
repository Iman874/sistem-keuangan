<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Modal;
use Illuminate\Http\Request;

class ModalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        if (!($user && $user instanceof \App\Models\User && method_exists($user, 'hasPermission') && ($user->hasPermission('modal.read') || $user->role === 'owner'))) {
            abort(403);
        }
        $modals = Modal::latest()->get();
        return view('owner.modal.index', compact('modals'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth()->user();
        if (!($user && $user instanceof \App\Models\User && method_exists($user, 'hasPermission') && ($user->hasPermission('modal.create') || $user->role === 'owner'))) {
            abort(403);
        }
        return view('owner.modal.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function store(Request $request)
     {
         $user = auth()->user();
         if (!($user && $user instanceof \App\Models\User && method_exists($user, 'hasPermission') && ($user->hasPermission('modal.create') || $user->role === 'owner'))) {
             abort(403);
         }
         $request->validate([
             'modal_items.*.nama_barang' => 'required|string|max:255',
             'modal_items.*.harga' => 'required|numeric|min:0',
             'modal_items.*.tanggal' => 'nullable|date',
             'modal_items.*.deskripsi' => 'nullable|string',
         ]);
     
         foreach ($request->modal_items as $item) {
             Modal::create([
                 'nama_barang' => $item['nama_barang'],
                 'harga' => $item['harga'],
                 'tanggal' => $item['tanggal'] ?? null,
                 'deskripsi' => $item['deskripsi'] ?? null,
             ]);
         }
     
         return redirect()->route('owner.modal.index')
             ->with('success', 'Data modal berhasil ditambahkan!');
     }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = auth()->user();
        if (!($user && $user instanceof \App\Models\User && method_exists($user, 'hasPermission') && ($user->hasPermission('modal.update') || $user->role === 'owner'))) {
            abort(403);
        }
        $modal = Modal::findOrFail($id);
        return view('owner.modal.edit', compact('modal'));
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
        $user = auth()->user();
        if (!($user && $user instanceof \App\Models\User && method_exists($user, 'hasPermission') && ($user->hasPermission('modal.update') || $user->role === 'owner'))) {
            abort(403);
        }
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'tanggal' => 'nullable|date',
            'deskripsi' => 'nullable|string',
        ]);

        $modal = Modal::findOrFail($id);
        $modal->update([
            'nama_barang' => $request->nama_barang,
            'harga' => $request->harga,
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('owner.modal.index')
            ->with('success', 'Data modal berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        if (!($user && $user instanceof \App\Models\User && method_exists($user, 'hasPermission') && ($user->hasPermission('modal.delete') || $user->role === 'owner'))) {
            abort(403);
        }
        $modal = Modal::findOrFail($id);
        $modal->delete();

        return redirect()->route('owner.modal.index')
            ->with('success', 'Data modal berhasil dihapus!');
    }
}