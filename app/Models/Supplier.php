<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    
    protected $table = 'suppliers'; // Explicitly define table name

    protected $guarded = [];

    // Casting sangat penting agar nilai_kriteria dibaca sebagai Array/JSON otomatis
    protected $casts = [
        'nilai_kriteria' => 'array',
    ];
    
    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}