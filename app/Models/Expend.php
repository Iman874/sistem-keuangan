<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Expend Model
 *
 * @method static \Illuminate\Database\Eloquent\Builder whereDate(string $column, $value)
 * @method static \Illuminate\Database\Eloquent\Builder with($relations)
 * @method static \Illuminate\Database\Eloquent\Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder count()
 * @method static \Illuminate\Database\Eloquent\Builder sum(string $column)
 */
class Expend extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session',
        'amount',
        'date',
        'time',
        'type',
        'category_id',
        'description',
        'receipt_image',
        'invoice_id'
    ];

    protected $casts = [
        'date' => 'datetime',
        'time' => 'datetime',
    ];

    /**
     * Scope for daily expenses
     */
    public function scopeDaily($query)
    {
        return $query->where('type', 'harian');
    }

    /**
     * Scope for monthly expenses
     */
    public function scopeMonthly($query)
    {
        return $query->where('type', 'bulanan');
    }

    /**
     * Get the user that owns the expense.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that this expense belongs to.
     */
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
