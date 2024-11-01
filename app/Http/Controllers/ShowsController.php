<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\Performance;
use App\Models\Hall;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

class ShowsController extends Controller
{
    public function index()
    {
        return Inertia::render('Shows/Index', [
            'shows' => Show::with(['performance', 'hall'])->paginate(),
            'filters' => Request::all('search', 'trashed'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Shows/Create', [
            'performances' => Performance::all(),
            'halls' => Hall::all(),
        ]);
    }

    public function store()
    {
        Show::create(
            Request::validate([
                'performance_id' => [
                    'required',
                    'exists:performances,id',
                ],
                'datetime' => [
                    'required',
                    'date',
                ],
                'price' => [
                    'required',
                    'numeric',
                    'min:0',
                ],
                'hall_id' => [
                    'required',
                    'exists:halls,id',
                ],
            ])
        );

        return Redirect::route('shows')->with('success', 'Show created.');
    }

    public function edit(Show $show)
    {
        return Inertia::render('Shows/Edit', [
            'show' => [
                'id' => $show->id,
                'performance_id' => $show->performance_id,
                'datetime' => $show->datetime,
                'price' => $show->price,
                'hall_id' => $show->hall_id,
            ],
            'performances' => Performance::all(),
            'halls' => Hall::all(),
        ]);
    }

    public function update(Show $show)
    {
        $show->update(
            Request::validate([
                'performance_id' => ['required', 'exists:performances,id'],
                'datetime' => ['required', 'date'],
                'price' => ['required', 'numeric', 'min:0'],
                'hall_id' => ['required', 'exists:halls,id'],
            ])
        );

        return Redirect::back()->with('success', 'Show updated.');
    }

    public function destroy(Show $show)
    {
        $show->delete();

        return Redirect::back()->with('success', 'Show deleted.');
    }

    public function restore(Show $show)
    {
        $show->restore();

        return Redirect::back()->with('success', 'Show restored.');
    }
}
