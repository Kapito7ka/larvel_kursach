<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producer extends Model
{
    use HasFactory;

    protected $table = 'producers';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
    ];
}
