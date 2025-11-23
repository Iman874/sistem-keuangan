<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeSessionReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'cashier_id',
        'date',
        'session',
        'verified_by_cashier',
        'submitted_at',
        'status',
        'attempt',
        'manager_id',
        'decided_at',
        'note',
        'total_cash',
        'total_qris',
    ];

    protected $casts = [
        'date' => 'date',
        'submitted_at' => 'datetime',
        'decided_at' => 'datetime',
        'verified_by_cashier' => 'boolean',
        'total_cash' => 'decimal:2',
        'total_qris' => 'decimal:2',
        'attempt' => 'integer',
    ];

    public function incomes()
    {
        return $this->hasMany(Income::class, 'session_report_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
