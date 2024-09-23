<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Composition extends Model
{
    protected $table = 'compositions';

    protected $fillable = [
        'performance_id',
        'actor_id',
    ];
}
