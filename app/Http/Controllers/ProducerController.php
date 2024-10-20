<?php

namespace App\Http\Controllers;

use App\Models\Producer;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

class ProducerController extends Controller
{
    public function index()
    {
        return Inertia::render('Producers/Index', [
            'producers' => Producer::paginate(),
            'filters' => Request::all('search', 'trashed')
        ]);
    }

    public function create()
    {
        return Inertia::render('Producers/Create');
    }

    public function store()
    {
        Producer::create(
            Request::validate([
                'first_name' => [
                    'required',
                    'max:50'
                ],
                'last_name' => [
                    'required',
                    'max:50',
                ],
                'phone_number' => [
                    'nullable',
                    'max:20',
                ],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    'unique:producers,email',
                ],
                'date_of_birth' => [
                    'required',
                    'date', // Перевірка на коректний формат дати
                ],
            ])
        );
        return Redirect::route('producers')->with('success', 'Producer created.');
    }

    public function edit(Producer $producer)
    {
        return Inertia::render('Producers/Edit', [
            'producer' => [
                'id' => $producer->id,
                'first_name' => $producer->first_name,
                'last_name' => $producer->last_name,
                'email' => $producer->email,
                'phone_number' => $producer->phone_number,
                'date_of_birth' => $producer->date_of_birth, // Додаємо дату народження
                'deleted_at' => $producer->deleted_at,
            ]
        ]);
    }

    public function update(Producer $producer)
    {
        $producer->update(
            Request::validate([
                'first_name' => ['required', 'max:50'],
                'last_name' => ['required', 'max:50'],
                'phone_number' => ['nullable', 'max:20'],
                'email' => ['required', 'email', 'max:255', 'unique:producers,email,' . $producer->id],
                'date_of_birth' => [
                    'required',
                    'date',
                ],
            ])
        );
        return Redirect::back()->with('success', 'Producer updated.');
    }

    public function destroy(Producer $producer)
    {
        $producer->delete();
        return Redirect::back()->with('success', 'Producer deleted.');
    }

    public function restore(Producer $producer)
    {
        $producer->restore();
        return Redirect::back()->with('success', 'Producer restored.');
    }
}
