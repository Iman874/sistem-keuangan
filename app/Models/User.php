<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        // JSON kolom permissions (nullable)
        'permissions'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array'
    ];

    /**
     * Get incomes created by this user (kasir)
     */
    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    /**
     * Get expenses created by this user (kasir)
     */
    public function expends()
    {
        return $this->hasMany(Expend::class);
    }

    /**
     * Simple permission checker.
     * Owner always true. Admin/cashier read from permissions JSON.
     */
    public function hasPermission(string $key, bool $default = false): bool
    {
        if ($this->role === 'owner') {
            return true;
        }
        $perms = $this->permissions ?? [];
        if (!is_array($perms)) {
            return $default;
        }
        return (bool)($perms[$key] ?? $default);
    }
}
