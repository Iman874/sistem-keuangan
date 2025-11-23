<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoTopup extends Model
{
    use HasFactory;

    protected $fillable = [
        'account','amount','note','user_id','date','time'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i:s',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
