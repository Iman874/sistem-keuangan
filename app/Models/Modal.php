<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modal extends Model
{
    use HasFactory;

    protected $table = 'modals';
    
    protected $fillable = [
        'nama_barang',
        'harga',
        'tanggal',
        'deskripsi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}