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
}
