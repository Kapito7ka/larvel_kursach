<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Show extends Model
{
    use HasFactory;

    protected $fillable = [
        'performance_id',
        'datetime',
        'price',
        'hall_id',
    ];

    protected $casts = [ // Замість методу casts()
        'datetime' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function performance()
    {
        return $this->belongsTo(Performance::class, 'performance_id');
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class, 'hall_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}