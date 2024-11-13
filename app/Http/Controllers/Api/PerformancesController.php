<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Builder;
use App\Filters\PerformanceFilter;
use App\Http\Requests\StorePerformanceRequest;
use App\Models\Performance;
use App\Models\Genre;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; 
use App\Models\Show;

class PerformancesController extends ApiController
{
    public function index()
    {
        $query = Performance::query();

        if (request()->has('sort_price')) {
            $direction = request('sort_price') === 'desc' ? 'desc' : 'asc';
            $query->orderBy('price', $direction);
        }

        if (request()->has('sort_date')) {
            $direction = request('sort_date') === 'desc' ? 'desc' : 'asc';
            $query->orderBy('created_at', $direction);
        }

        if (request()->has('genre_id')) {
            $query->whereHas('genres', function($q) {
                $q->where('genres.id', request('genre_id'));
            });
        }

        if (request()->has('search')) {
            $searchTerm = request('search');
            $query->where('title', 'LIKE', "%{$searchTerm}%");
        }

        $performances = $query->with(['genres', 'producer', 'actors'])->get();
        return response()->json($performances);
    }

    public function show(Performance $performance)
    {
        $performance->load(['producer', 'actors', 'genres']);
        return response()->json($performance);
    }

    public function store(StorePerformanceRequest $request)
    {
        try {
            \Log::info('Отримані дані:', $request->all());
            
            $validated = $request->validated();
            
            DB::beginTransaction(); 
            
            $performance = Performance::create([
                'title' => $validated['title'],
                'duration' => $validated['duration'],
                'producer' => $validated['producer'], 
                'image' => $validated['image'] ?? null
            ]);

            if (isset($validated['genre_id'])) { 
                $performance->genres()->attach($validated['genre_id']);
            }

            if (isset($validated['actors'])) {
                $performance->actors()->attach($validated['actors']);
            }

            DB::commit();

            $performance->load(['producer', 'actors', 'genres']);

            return response()->json([
                'message' => 'Виставу успішно створено',
                'performance' => $performance
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
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

    public function showGenres()
    {
        try {
            Log::info('Початок запиту жанрів');
            
            $genres = Genre::all();
            Log::info('Отримані жанри:', $genres->toArray());

            return response()->json($genres);
        } catch (\Exception $e) {
            Log::error('Помилка при отриманні жанрів:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'message' => 'Помилка при отриманні жанрів',
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

    public function shows(Performance $performance)
    {
        try {
            $shows = Show::where('performance_id', $performance->id)
                ->with(['hall'])
                ->orderBy('datetime')
                ->get();

            return response()->json($shows);
        } catch (\Exception $e) {
            Log::error('Помилка при отриманні показів:', [
                'performance_id' => $performance->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Помилка при отриманні показів',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

    public function update(StorePerformanceRequest $request, Performance $performance)
    {
        try {
            DB::beginTransaction();
            
            $validated = $request->validated();
            
            $updateData = [
                'title' => $validated['title'],
                'duration' => $validated['duration'],
                'producer' => $validated['producer']
            ];
            
            // Оновлення зображення, якщо воно надано
            if (isset($validated['image'])) {
                // Видалення старого зображення
                if ($performance->image) {
                    Storage::delete($performance->image);
                }
                $updateData['image'] = $validated['image'];
            }
            
            $performance->update($updateData);
            
            // Оновлення жанрів
            if (isset($validated['genre_id'])) {
                $performance->genres()->sync($validated['genre_id']);
            }
            
            // Оновлення акторів
            if (isset($validated['actors'])) {
                $performance->actors()->sync($validated['actors']);
            }
            
            DB::commit();
            
            $performance->load(['producer', 'actors', 'genres']);
            
            return response()->json([
                'message' => 'Виставу успішно оновлено',
                'performance' => $performance
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Помилка оновлення вистави', [
                'error' => $e->getMessage(),
                'performance_id' => $performance->id,
                'data' => $request->all()
            ]);
            
            return response()->json([
                'message' => 'Помилка при оновленні вистави',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

    public function destroy(Performance $performance)
    {
        try {
            DB::beginTransaction();
            
            // Видалення зображення, якщо воно існує
            if ($performance->image) {
                Storage::delete($performance->image);
            }
            
            // Видалення пов'язаних даних
            $performance->genres()->detach();
            $performance->actors()->detach();
            $performance->delete();
            
            DB::commit();
            
            return response()->json([
                'message' => 'Виставу успішно видалено'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Помилка видалення вистави', [
                'error' => $e->getMessage(),
                'performance_id' => $performance->id
            ]);
            
            return response()->json([
                'message' => 'Помилка при видаленні вистави',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }
}
