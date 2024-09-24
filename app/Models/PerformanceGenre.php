<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceGenre extends Model
{
    protected $fillable = [
        'performance_id',
        'genre_id',
    ];
}
