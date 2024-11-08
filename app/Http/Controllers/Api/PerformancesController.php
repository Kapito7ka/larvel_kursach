<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Builder;
use App\Filters\PerformanceFilter;

class PerformancesController extends ApiController
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
