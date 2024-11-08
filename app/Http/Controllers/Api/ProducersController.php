<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProducersController extends Controller
{
    public function index(): JsonResponse
    {
        $producers = Producer::all();
        return response()->json($producers);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:producers,email',
            'phone_number' => 'required|string|max:20',
        ]);

        $producer = Producer::create($validated);
        return response()->json($producer, 201);
    }

    public function show(Producer $producer): JsonResponse
    {
        return response()->json($producer);
    }

    public function update(Request $request, Producer $producer): JsonResponse
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

    public function destroy(Producer $producer): JsonResponse
    {
        $producer->delete();
        return response()->json(null, 204);
    }
}
