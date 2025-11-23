<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Income Model
 *
 * @method static \Illuminate\Database\Eloquent\Builder whereDate(string $column, $value)
 * @method static \Illuminate\Database\Eloquent\Builder with($relations)
 * @method static \Illuminate\Database\Eloquent\Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Builder count()
 * @method static \Illuminate\Database\Eloquent\Builder sum(string $column)
 */
class Income extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pemasukkan_id',
        'session',
        'amount',
        'qty',
        'unit_price',
        'date',
        'time',
        'description',
        'customer_name',
        'customer_email',
        'payment_type',
        'session_report_id',
        'invoice_id'
    ];

    protected $casts = [
        'date' => 'datetime',
        'time' => 'datetime',
    ];

    // Relationship with Pemasukkan (income category)
    public function category()
    {
        return $this->belongsTo(Pemasukkan::class, 'pemasukkan_id');
    }

    // Relationship with User (kasir yang input)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sessionReport()
    {
        return $this->belongsTo(IncomeSessionReport::class, 'session_report_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
