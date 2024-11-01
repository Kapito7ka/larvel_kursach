<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

class ActorsController extends Controller
{
    public function index()
    {
        return Inertia::render('Actors/Index', [
            'actors' => Actor::paginate(),
            'filters' => Request::all('search', 'trashed'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Actors/Create');
    }

    public function store()
    {
        Actor::create(
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
                    'max:50',
                ],
                'date_of_birth' => [
                    'nullable',
                    'date',
                ],
                'passport' => [
                    'nullable',
                    'string',
                    'max:50',
                ],
            ])
        );

        return Redirect::route('actors')->with('success', 'Actor created.');
    }

    public function edit(Actor $actor)
    {
        return Inertia::render('Actors/Edit', [
            'actor' => [
                'id' => $actor->id,
                'first_name' => $actor->first_name,
                'last_name' => $actor->last_name,
                'phone_number' => $actor->phone_number,
                'date_of_birth' => $actor->date_of_birth,
                'passport' => $actor->passport,
                'deleted_at' => $actor->deleted_at,
            ],
        ]);
    }

    public function update(Actor $actor)
    {
        $actor->update(
            Request::validate([
                'first_name' => ['required', 'max:50'],
                'last_name' => ['required', 'max:50'],
                'phone_number' => ['nullable', 'max:50'],
                'date_of_birth' => ['nullable', 'date'],
                'passport' => [
                    'nullable',
                    'string',
                    'max:50',
                ],
            ])
        );

        return Redirect::back()->with('success', 'Actor updated.');
    }

    public function destroy(Actor $actor)
    {
        $actor->delete();

        return Redirect::back()->with('success', 'Actor deleted.');
    }

    public function restore(Actor $actor)
    {
        $actor->restore();

        return Redirect::back()->with('success', 'Actor restored.');
    }
}
