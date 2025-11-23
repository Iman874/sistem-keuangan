<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'number','type','cashier_id','customer_name','customer_email','payment_type',
        'date','time','subtotal','tax','total','notes'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function expends()
    {
        return $this->hasMany(Expend::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}
