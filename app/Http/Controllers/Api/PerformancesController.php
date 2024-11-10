<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Builder;
use App\Filters\PerformanceFilter;
use App\Http\Requests\StorePerformanceRequest;
use App\Models\Performance;
use Illuminate\Support\Facades\Storage;

class PerformancesController extends ApiController
{
    public function index()
    {
        $performances = Performance::all();
        return response()->json($performances);
    }

    public function show(Performance $performance)
    {
        return response()->json($performance);
    }

    public function store(StorePerformanceRequest $request)
    {
        try {
            \Log::info('Отримані дані:', $request->all());
            
            $validated = $request->validated();
            
            $performance = Performance::create([
                'title' => $validated['title'],
                'duration' => $validated['duration'],
                'producer_id' => $validated['producer'],
                'image' => $validated['image'] ?? null
            ]);

            return response()->json([
                'message' => 'Виставу успішно створено',
                'performance' => $performance
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Помилка створення вистави', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'message' => 'Помилка при створенні вистави',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

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
