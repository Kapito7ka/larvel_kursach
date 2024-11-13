<?php

namespace App\Http\Controllers;

use App\Models\Performance;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\StorePerformanceRequest;
use Illuminate\Support\Facades\DB;

class PerformanceController extends Controller
{
    public function index()
    {
        return Inertia::render('Performances/Index', [
            'performances' => Performance::paginate(),
            'filters' => Request::all('search', 'trashed')
        ]);
    }

    public function create()
    {
        return Inertia::render('Performances/Create');
    }

    public function store(StorePerformanceRequest $request)
    {
        \Log::info('Отримані дані:', $request->validated());
        
        try {
            DB::beginTransaction();
            
            $performance = Performance::create($request->validated());
            
            \Log::info('Створена вистава:', ['id' => $performance->id]);
            
            if ($request->has('actors')) {
                \Log::info('Синхронізація акторів:', ['actors' => $request->actors]);
                $performance->actors()->sync($request->actors);
            }
            
            DB::commit();
            
            // Завантажуємо зв'язані дані перед поверненням
            $performance->load('actors');
            
            return response()->json([
                'success' => true,
                'performance' => $performance,
                'message' => 'Виставу успішно створено'
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Помилка при створенні вистави:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Помилка при створенні вистави: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Performance $performance)
    {
        return Inertia::render('Performances/Edit', [
            'performance' => [
                'id' => $performance->id,
                'title' => $performance->title,
                'duration' => $performance->duration,
                'deleted_at' => $performance->deleted_at,
            ]
        ]);
    }

    public function update(Performance $performance)
    {
        $performance->update(
            Request::validate([
                'title' => ['required', 'max:255'],
                'duration' => ['required', 'integer', 'min:1'],
            ])
        );
        return Redirect::back()->with('success', 'Performance updated.');
    }

    public function destroy(Performance $performance)
    {
        $performance->delete();

        return Redirect::back()->with('success', 'Performance deleted.');
    }

    public function restore(Performance $performance)
    {
        $performance->restore();

        return Redirect::back()->with('success', 'Performance restored.');
    }
}
