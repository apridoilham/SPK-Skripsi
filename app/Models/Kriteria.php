<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode', 
        'nama', 
        'bobot', 
        'jenis', 
        'opsi'
    ];

    protected $casts = [
        'opsi' => 'array',
    ];
}