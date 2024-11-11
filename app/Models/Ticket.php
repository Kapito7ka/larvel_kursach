<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'date',
        'time',
        'show_id',
        'seat_id',
        'user_id',
        'price',
        'discount_id'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'string',
        'price' => 'decimal:2',
    ];

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}