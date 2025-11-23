<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pemasukkan;
use Illuminate\Http\Request;

class PemasukkanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        if (!($user && $user instanceof \App\Models\User && method_exists($user, 'hasPermission') && ($user->hasPermission('pemasukkan.read') || $user->role === 'owner'))) {
            abort(403);
        }
        $pemasukkan = Pemasukkan::latest()->get();
        return view('owner.pemasukkan.index', compact('pemasukkan'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth()->user();
        if (!($user && $user instanceof \App\Models\User && method_exists($user, 'hasPermission') && ($user->hasPermission('pemasukkan.create') || $user->role === 'owner'))) {
            abort(403);
        }
        return view('owner.pemasukkan.create');
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
        if (!($user && $user instanceof \App\Models\User && method_exists($user, 'hasPermission') && ($user->hasPermission('pemasukkan.create') || $user->role === 'owner'))) {
            abort(403);
        }
        $request->validate([
            'nama_pemasukkan' => 'required|string|max:255|unique:pemasukkan',
        ]);

        Pemasukkan::create([
            'nama_pemasukkan' => $request->nama_pemasukkan,
        ]);

        return redirect()->route('owner.pemasukkan.index')
            ->with('success', 'Data pemasukkan berhasil ditambahkan!');
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
        if (!($user && $user instanceof \App\Models\User && method_exists($user, 'hasPermission') && ($user->hasPermission('pemasukkan.update') || $user->role === 'owner'))) {
            abort(403);
        }
        $pemasukkan = Pemasukkan::findOrFail($id);
        return view('owner.pemasukkan.edit', compact('pemasukkan'));
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
        if (!($user && $user instanceof \App\Models\User && method_exists($user, 'hasPermission') && ($user->hasPermission('pemasukkan.update') || $user->role === 'owner'))) {
            abort(403);
        }
        $pemasukkan = Pemasukkan::findOrFail($id);

        $request->validate([
            'nama_pemasukkan' => 'required|string|max:255|unique:pemasukkan,nama_pemasukkan,'.$id,
        ]);

        $pemasukkan->update([
            'nama_pemasukkan' => $request->nama_pemasukkan,
        ]);

        return redirect()->route('owner.pemasukkan.index')
            ->with('success', 'Data pemasukkan berhasil diperbarui!');
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
        if (!($user && $user instanceof \App\Models\User && method_exists($user, 'hasPermission') && ($user->hasPermission('pemasukkan.delete') || $user->role === 'owner'))) {
            abort(403);
        }
        $pemasukkan = Pemasukkan::findOrFail($id);
        $pemasukkan->delete();

        return redirect()->route('owner.pemasukkan.index')
            ->with('success', 'Data pemasukkan berhasil dihapus!');
    }
}