<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    protected $table = 'genres';

    protected $fillable = [
        'id',
        'name'
    ];

    public function scopeOrderByNameAsc($query)
    {
        return $query->orderBy('name', 'asc');
    }

    public function scopeOrderByNameDesc($query)
    {
        return $query->orderBy('name', 'desc');
    }

    public function scopeOrderByIdAsc($query)
    {
        return $query->orderBy('id', 'asc');
    }

    public function scopeOrderByIdDesc($query)
    {
        return $query->orderBy('id', 'desc');
    }
}
