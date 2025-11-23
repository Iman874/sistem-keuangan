<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasukkan extends Model
{
    use HasFactory;

    protected $table = 'pemasukkan';
    
    protected $fillable = [
        'nama_pemasukkan',
        'type', // indoor | outdoor
    ];

    protected $casts = [
        'type' => 'string',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!in_array($model->type, ['indoor', 'outdoor'])) {
                $model->type = 'indoor';
            }
        });
        static::updating(function ($model) {
            if (!in_array($model->type, ['indoor', 'outdoor'])) {
                $model->type = 'indoor';
            }
        });
    }
}