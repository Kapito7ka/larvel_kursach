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
        'producer',
        'image'
    ];

    protected $casts = [
        'duration' => 'integer',
        'producer' => 'integer'
    ];

    public function producer()
    {
        return $this->belongsTo(Producer::class, 'producer');
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'performance_genres');
    }

    public function shows()
    {
        return $this->hasMany(Show::class);
    }
}
