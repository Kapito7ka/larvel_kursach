<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Performance extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'duration',
    ];

    public function producer()
    {
        return $this->belongsToMany(Producer::class, 'performance_producers');
    }
}
