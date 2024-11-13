<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProducerController extends Controller
{
    // Показати всі продюсери
    public function index()
    {
        try {
            $producers = Producer::all();
            return response()->json([
                'producers' => $producers
            ]);
        } catch (\Exception $e) {
            Log::error('Помилка отримання продюсерів', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'Помилка при отриманні продюсерів',
                'error' => config('app.debug') ? $e->getMessage() : 'Внутрішня помилка сервера'
            ], 500);
        }
    }

    // Показати форму для створення нового продюсера
    public function create()
    {
        return response()->json([
            'message' => 'Форма створення продюсера'
        ]);
    }

    // Зберегти нового продюсера
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:producers,email',
                'phone_number' => 'required|string|max:20',
            ]);

            $producer = Producer::create($validated);

            return response()->json([
                'message' => 'Продюсера успішно створено',
                'producer' => $producer
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Помилка валідації',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Помилка створення продюсера', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return response()->json([
                'message' => 'Помилка при створенні продюсера',
                'error' => config('app.debug') ? $e->getMessage() : 'Внутрішня помилка сервера'
            ], 500);
        }
    }

    // Показати форму для редагування продюсера
    public function edit(Producer $producer)
    {
        return response()->json([
            'producer' => $producer
        ]);
    }

    // Оновити продюсера
    public function update(Request $request, Producer $producer)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:producers,email,' . $producer->id,
                'phone_number' => 'required|string|max:20',
            ]);

            $producer->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'Продюсера успішно оновлено',
                'producer' => $producer
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Помилка валідації',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Помилка оновлення продюсера', [
                'error' => $e->getMessage(),
                'producer_id' => $producer->id,
                'data' => $request->all()
            ]);
            return response()->json([
                'message' => 'Помилка при оновленні продюсера',
                'error' => config('app.debug') ? $e->getMessage() : 'Внутрішня помилка сервера'
            ], 500);
        }
    }

    // Видалити продюсера
    public function destroy(Producer $producer)
    {
        try {
            DB::beginTransaction();

            $producer->delete();

            DB::commit();

            return response()->json([
                'message' => 'Продюсера успішно видалено'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Помилка видалення продюсера', [
                'error' => $e->getMessage(),
                'producer_id' => $producer->id
            ]);
            return response()->json([
                'message' => 'Помилка при видаленні продюсера',
                'error' => config('app.debug') ? $e->getMessage() : 'Внутрішня помилка сервера'
            ], 500);
        }
    }
}
