<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'description', 'type'];

    // Relasi ke User (Siapa pelakunya)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}