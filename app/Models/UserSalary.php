<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','email','phone','role','base_salary','active','notes','start_date','end_date','is_permanent'
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_permanent' => 'boolean',
    ];

    public function payments()
    {
        return $this->hasMany(SalaryPayment::class);
    }

    public function getIsCurrentlyActiveAttribute(): bool
    {
        if (!$this->active) return false;
        if ($this->is_permanent) return true;
        if (empty($this->end_date)) return true;
        return $this->end_date->isFuture() || $this->end_date->isToday();
    }
}
