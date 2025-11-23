<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeederAll extends Seeder
{
    public function run(): void
    {
        $users = [
            // Admin: limited user access, full income/expense, plus create/update for pemasukkan & modal (as requested)
            [
                'email' => 'admin@example.com',
                'name' => 'Admin',
                'password' => 'password',
                'role' => 'admin',
                'permissions' => [
                    // user
                    'users.read' => true,
                    // income (all)
                    'income.read' => true,
                    'income.create' => true,
                    'income.update' => true,
                    'income.delete' => true,
                    'income.export' => true,
                    // session report approvals
                    'income.approve' => true,
                    // expense (all)
                    'expense.read' => true,
                    'expense.create' => true,
                    'expense.update' => true,
                    'expense.delete' => true,
                    'expense.export' => true,
                    // pemasukkan table specific grants (alias to income if needed)
                    'pemasukkan.read' => true,
                    'pemasukkan.create' => true,
                    'pemasukkan.update' => true,
                    // modal table grants (create/update; read implied by owner only but set true for clarity)
                    'modal.read' => true,
                    'modal.create' => true,
                    'modal.update' => true,
                    // salary module grants
                    'salary.read' => true,
                    'salary.create' => true,
                    'salary.update' => true,
                    'salary.delete' => true,
                    // saldo management grants
                    'saldo.read' => true,
                    'saldo.create' => true,
                    'saldo.update' => true,
                    'saldo.delete' => true,
                    'saldo.export' => true,
                ]
            ],
            // Owner: owner role implicitly has all permissions via hasPermission() method (no explicit JSON needed)
            [
                'email' => 'owner@example.com',
                'name' => 'Owner',
                'password' => 'owner123',
                'role' => 'owner',
            ],
            // Kasir: cannot access pemasukkan table (owner-admin only request) and cannot access modal at all
            [
                'email' => 'kasir@example.com',
                'name' => 'Kasir',
                'password' => 'kasir123',
                'role' => 'kasir',
                'permissions' => [
                    // income partial
                    'income.read' => true,
                    'income.create' => true,
                    'income.update' => true,
                    // session report submit
                    'income.submit' => true,
                    // expense partial
                    'expense.read' => true,
                    'expense.create' => true,
                    'expense.update' => true,
                ]
            ],
        ];

        foreach ($users as $u) {
            $existing = User::where('email', $u['email'])->first();
            $passwordHashed = Hash::make($u['password']);
            if ($existing) {
                // Update role/permissions if changed (keep existing password if already set differently?)
                $existing->role = $u['role'];
                if (isset($u['permissions'])) {
                    $existing->permissions = $u['permissions'];
                }
                // Only reset password if original is default (optional) - we skip to avoid overwriting manual changes
                // $existing->password = $passwordHashed; // commented intentionally
                $existing->save();
                continue;
            }

            $payload = [
                'name' => $u['name'],
                'password' => $passwordHashed,
                'role' => $u['role'],
            ];
            if (isset($u['permissions'])) {
                $payload['permissions'] = $u['permissions'];
            }
            User::create(array_merge(['email' => $u['email']], $payload));
        }
    }
}
