<?php

namespace App\Http\Controllers;

use App\Models\Performance;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

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

    public function store()
    {
        Performance::create(
            Request::validate([
                'title' => [
                    'required',
                    'max:255'
                ],
                'duration' => [
                    'required',
                    'integer',
                    'min:1'
                ],
            ])
        );
        return Redirect::route('performances')->with('success', 'Performance created.');
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
