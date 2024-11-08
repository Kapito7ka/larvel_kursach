<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class PerformanceFilter 
{
    public function actor($actorId)
    {
        return $this->builder->whereHas('actors', function (Builder $query) use ($actorId) {
            $query->where('id', $actorId);
        });
    }

    public function genre($genreId)
    {
        return $this->builder->where('genre_id', $genreId);
    }

    public function date($date)
    {
        return $this->builder->whereDate('performance_date', '=', $date);
    }
}
