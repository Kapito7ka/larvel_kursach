<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProducerRequest;
use App\Models\Producer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProducersController extends Controller
{
    public function index()
    {
        $producers = Producer::all();
        return response()->json($producers);
    }

    public function store(StoreProducerRequest $request)
    {
        try {
            $dateOfBirth = Carbon::createFromFormat('d/m/Y', $request->date_of_birth)->format('Y-m-d');
            
            $producer = Producer::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'date_of_birth' => $dateOfBirth,
            ]);

            return response()->json([
                'message' => 'Продюсера успішно створено',
                'producer' => $producer
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Помилка створення продюсера: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'message' => 'Помилка при створенні продюсера',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Producer $producer)
    {
        return response()->json($producer);
    }

    public function update(Request $request, Producer $producer)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:producers,email,' . $producer->id,
            'phone_number' => 'required|string|max:20',
        ]);

        $producer->update($validated);
        return response()->json($producer);
    }

    public function destroy(Producer $producer)
    {
        $producer->delete();
        return response()->json(null, 204);
    }
}
