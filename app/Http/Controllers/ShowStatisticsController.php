<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

class ShowStatisticsController extends Controller
{
    public function index()
    {
        $statistics = DB::table('performance_ticket_sales')
            ->select('performance_title', 'sale_date', 'tickets_sold')
            ->orderBy('performance_title')
            ->orderBy('sale_date')
            ->get();

        return Inertia::render('Statistics/Index', [
            'statistics' => $statistics,
            'filters' => Request::all('start_date', 'end_date', 'performance_id'),
        ]);
    }

    public function fetchStatistics()
    {
        $filters = Request::validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'performance_id' => ['nullable', 'integer'],
        ]);

        $query = DB::table('performance_ticket_sales')
            ->select('performance_title', 'sale_date', 'tickets_sold')
            ->orderBy('performance_title')
            ->orderBy('sale_date');

        if ($filters['start_date']) {
            $query->whereDate('sale_date', '>=', $filters['start_date']);
        }

        if ($filters['end_date']) {
            $query->whereDate('sale_date', '<=', $filters['end_date']);
        }

        if ($filters['performance_id']) {
            $query->where('performance_id', $filters['performance_id']);
        }

        $statistics = $query->get();

        return Redirect::route('statistics.index')->with([
            'statistics' => $statistics,
            'filters' => $filters,
        ]);
    }
}
