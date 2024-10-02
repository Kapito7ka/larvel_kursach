<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Producer;
use Illuminate\Http\Request;

class ProducerController extends Controller
{
    // Показати всі продюсери
    public function index()
    {
        $producers = Producer::all();
        return inertia('Producers/Index', compact('producers'));
    }

    // Показати форму для створення нового продюсера
    public function create()
    {
        return inertia('Producers/Create');
    }

    // Зберегти нового продюсера
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:producers,email',
            'phone_number' => 'required|string|max:20',
        ]);

        Producer::create($validated);
        return redirect()->route('producers.index');
    }

    // Показати форму для редагування продюсера
    public function edit(Producer $producer)
    {
        return inertia('Producers/Edit', compact('producer'));
    }

    // Оновити продюсера
    public function update(Request $request, Producer $producer)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:producers,email,' . $producer->id,
            'phone_number' => 'required|string|max:20',
        ]);

        $producer->update($validated);
        return redirect()->route('producers.index');
    }

    // Видалити продюсера
    public function destroy(Producer $producer)
    {
        $producer->delete();
        return redirect()->route('producers.index');
    }
}
