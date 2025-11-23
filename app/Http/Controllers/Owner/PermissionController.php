<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PermissionController extends Controller
{
    // Owner-only endpoint to update role-level permissions snapshot stored on a special config user (id null) or in .env
    // For simplicity, we store per-role defaults in a simple settings row on the owner (id=auth user) or broadcast to all users of a role when chosen.

    public function update(Request $request)
    {
        // Ensure only owner can update
        if (!auth()->check() || auth()->user()->role !== 'owner') {
            abort(403);
        }

        $data = $request->validate([
            'role' => 'required|in:admin,kasir',
            'permissions' => 'required|array'
        ]);

        // Normalize values to booleans (incoming may contain "true"/"false" strings)
        $normalized = [];
        foreach ($data['permissions'] as $key => $val) {
            $normalized[$key] = filter_var($val, FILTER_VALIDATE_BOOLEAN);
        }

        User::where('role', $data['role'])->update(['permissions' => json_encode($normalized)]);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Return current permissions map for a role (from the first user with that role).
     */
    public function show(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'owner') {
            abort(403);
        }

        $data = $request->validate([
            'role' => 'required|in:admin,kasir',
        ]);

        $user = User::where('role', $data['role'])->first();
        $perms = $user?->permissions ?? [];
        if (!is_array($perms)) {
            $perms = [];
        }
        // Cast any string values to booleans for frontend reliability
        $perms = array_map(function($v){
            return is_bool($v) ? $v : filter_var($v, FILTER_VALIDATE_BOOLEAN);
        }, $perms);
        return response()->json(['permissions' => $perms]);
    }
}
