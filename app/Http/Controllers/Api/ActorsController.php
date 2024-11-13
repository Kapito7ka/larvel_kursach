<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Actor;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ActorsController extends Controller
{
    public function index()
    {
        try {
            return response()->json([
                'actors' => Actor::paginate(),
                'filters' => Request::all('search', 'trashed'),
            ]);
        } catch (\Exception $e) {
            Log::error('Помилка при отриманні акторів:', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'Помилка при отриманні акторів',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

    public function create()
    {
        try {
            return response()->json([
                'actors' => Actor::all(),
            ]);
        } catch (\Exception $e) {
            Log::error('Помилка при створенні актора:', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'Помилка при створенні актора',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

    public function store()
    {
        try {
            DB::beginTransaction();
            
            $validated = Request::validate([
                'first_name' => ['required', 'max:50'],
                'last_name' => ['required', 'max:50'],
                'phone_number' => ['required', 'max:50'],
                'date_of_birth' => ['required', 'date'],
                'passport' => ['required', 'string', 'max:50'],
            ]);
            
            $actor = Actor::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'phone_number' => $validated['phone_number'],
                'date_of_birth' => $validated['date_of_birth'],
                'passport' => $validated['passport'],
            ]);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Актор створений',
                'actor' => $actor
            ], 201);
        }
        catch (\Exception $e) {
            DB::rollBack();
            Log::error('Помилка при створенні актора:', [
                'error' => $e->getMessage(),
                'data' => Request::all()
            ]);
            return response()->json([
                'message' => 'Помилка при створенні актора',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);    
        }
    }

    public function edit(Actor $actor)
    {
        try {
            return response()->json([
                'actor' => [
                    'id' => $actor->id,
                    'first_name' => $actor->first_name,
                    'last_name' => $actor->last_name,
                    'phone_number' => $actor->phone_number,
                    'date_of_birth' => $actor->date_of_birth,
                    'passport' => $actor->passport,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Помилка при редагуванні актора:', [
                'actor_id' => $actor->id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'Помилка при редагуванні актора',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

    public function update(Actor $actor)
    {
        try {
            DB::beginTransaction();
            
            $validated = Request::validate([
                'first_name' => ['required', 'max:50'],
                'last_name' => ['required', 'max:50'],
                'phone_number' => ['nullable', 'max:50'],
                'date_of_birth' => ['nullable', 'date'],
                'passport' => [
                    'nullable',
                    'string',
                    'max:50',
                ],
            ]);

            $actor->update($validated);
            
            DB::commit();

            return response()->json([
                'message' => 'Актора успішно оновлено',
                'actor' => $actor
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Помилка валідації',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Помилка при оновленні актора:', [
                'actor_id' => $actor->id,
                'error' => $e->getMessage(),
                'data' => Request::all()
            ]);
            return response()->json([
                'message' => 'Помилка при оновленні актора',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

    public function destroy(Actor $actor)
    {
        try {
            DB::beginTransaction();
            
            // Перевірка на пов'язані вистави
            if ($actor->performances()->exists()) {
                return response()->json([
                    'message' => 'Неможливо видалити актора, який бере участь у виставах',
                ], 422);
            }
            
            $actor->delete();
            
            DB::commit();

            return response()->json([
                'message' => 'Актора успішно видалено',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Помилка при видаленні актора:', [
                'actor_id' => $actor->id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'Помилка при видаленні актора',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

    public function restore(Actor $actor)
    {
        try {
            DB::beginTransaction();
            
            $actor->restore();
            
            DB::commit();

            return response()->json([
                'message' => 'Актора успішно відновлено',
                'actor' => $actor
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Помилка при відновленні актора:', [
                'actor_id' => $actor->id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'Помилка при відновленні актора',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }
}
