<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'created_by',
        'is_active'
    ];

    /**
     * Scope for daily expense categories
     */
    public function scopeDaily($query)
    {
        return $query->where('type', 'harian');
    }

    /**
     * Scope for monthly expense categories
     */
    public function scopeMonthly($query)
    {
        return $query->where('type', 'bulanan');
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the expenses that belong to this category.
     */
    public function expenses()
    {
        return $this->hasMany(Expend::class, 'category_id');
    }
}
