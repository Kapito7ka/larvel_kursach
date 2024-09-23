<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    use HasFactory;

    protected $table = 'actors';

    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'passport',
        'phone_number',
    ];
}
