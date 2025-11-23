<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_salary_id','month','year','paid_date','base_salary','amount','method','fund_source','reference','description','created_by',
        'deduction_type','deduction_value','deduction_desc','bonus_type','bonus_value','bonus_desc'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'base_salary' => 'decimal:2',
        'deduction_value' => 'decimal:2',
        'bonus_value' => 'decimal:2',
        'paid_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(UserSalary::class, 'user_salary_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
