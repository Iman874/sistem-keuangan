<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $authUser = auth()->user();
        if (!($authUser instanceof \App\Models\User) || !$authUser->hasPermission('users.read')) {
            abort(403);
        }
        $users = User::all();
        return view('owner.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $authUser = auth()->user();
        if (!($authUser instanceof \App\Models\User) || !$authUser->hasPermission('users.create')) {
            abort(403);
        }
        return view('owner.user.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $authUser = auth()->user();
        if (!($authUser instanceof \App\Models\User) || !$authUser->hasPermission('users.create')) {
            abort(403);
        }
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:kasir,owner,admin'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('owner.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $authUser = auth()->user();
        if (!($authUser instanceof \App\Models\User) || !$authUser->hasPermission('users.update')) {
            abort(403);
        }
        return view('owner.user.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $authUser = auth()->user();
        if (!($authUser instanceof \App\Models\User) || !$authUser->hasPermission('users.update')) {
            abort(403);
        }
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', 'in:owner,kasir,admin'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['string', 'min:8', 'confirmed'],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('owner.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $authUser = auth()->user();
        if (!($authUser instanceof \App\Models\User) || !$authUser->hasPermission('users.delete')) {
            abort(403);
        }
        $user->delete();

        return redirect()->route('owner.users.index')
            ->with('success', 'User deleted successfully.');
    }
}