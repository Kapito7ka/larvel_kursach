<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'datetime',
        'show_id',
        'seat_id',
        'user_id',
        'price',
        'discount_id',
    ];

    protected function casts(): array
    {
        return [
            'datetime' => 'datetime',
            'price' => 'decimal:2',
        ];
    }
}
