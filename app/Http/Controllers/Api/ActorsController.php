<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Actor;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

class ActorsController extends Controller
{
    public function index()
    {
        return response()->json([
            'actors' => Actor::paginate(),
            'filters' => Request::all('search', 'trashed'),
        ]);
    }

    public function create()
    {
        return response()->json([
            'actors' => Actor::all(),
        ]);
    }

    public function store()
    {
        try {
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
            return response()->json([
                'message' => 'Актор створений',
                'actor' => $actor
            ], 201);
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Помилка при створенні актора',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);    
        }
    }

    public function edit(Actor $actor)
    {
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

        return response()->json([
            'message' => 'Актор видалений',
        ]);
    }

    public function restore(Actor $actor)
    {
        $actor->restore();

        return response()->json([
            'message' => 'Актор відновлений',
        ]);
    }
}
