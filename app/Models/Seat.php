<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'hall_id',
        'number',
        'row',
    ];

    public function hall()
    {
        return $this->belongsTo(Hall::class, 'hall_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}