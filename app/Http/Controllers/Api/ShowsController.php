<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Show;
use App\Models\Performance;
use App\Models\Hall;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\StoreShowRequest;
use App\Http\Requests\StoreHallRequest;
use Illuminate\Http\JsonResponse;

class ShowsController extends Controller
{
    public function index()
    {
        $shows = Show::with(['performance', 'hall'])->paginate();
        return response()->json($shows);
    }

    public function show(Show $show)
    {
        return response()->json($show);
    }

    public function store(StoreShowRequest $request)
    {
        try {   
            \Log::info('Отримані дані:', $request->all());
            
            $validated = $request->validated();

            // Перевіряємо наявність місць
            $seatsCount = \DB::table('seats')
                ->where('hall_id', $validated['hall_id'])
                ->count();

            if ($seatsCount === 0) {
                return response()->json([
                    'message' => 'Для цього залу не створені місця',
                    'error' => 'No seats available'
                ], 400);
            }

            \DB::beginTransaction();

            try {
                // Створюємо виставу
                $show = Show::create([
                    'performance_id' => $validated['performance_id'],
                    'datetime' => $validated['datetime'],
                    'price' => $validated['price'],
                    'hall_id' => $validated['hall_id']
                ]);

                // Отримуємо місця для конкретного залу
                $seats = \DB::table('seats')
                    ->where('hall_id', $validated['hall_id'])
                    ->orderBy('row')
                    ->orderBy('seat_number')
                    ->get();

                // Масове створення квитків
                $tickets = [];
                foreach ($seats as $seat) {
                    $tickets[] = [
                        'ticket_number' => uniqid('TKT') . '-R' . $seat->row . 'S' . str_pad($seat->seat_number, 2, '0', STR_PAD_LEFT),
                        'date' => date('Y-m-d', strtotime($validated['datetime'])),
                        'time' => date('H:i:s', strtotime($validated['datetime'])),
                        'show_id' => $show->id,
                        'seat_id' => $seat->id,
                        'price' => $validated['price'],
                        'user_id' => null,
                        'discount_id' => null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                \App\Models\Ticket::insert($tickets);

                \DB::commit();

                return response()->json([
                    'message' => 'Вистава створена і квитки згенеровані',
                    'show' => $show,
                    'tickets_count' => count($tickets)
                ], 201);

            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Помилка при створенні квитків:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        }
        catch (\Exception $e) {
            \Log::error('Помилка при створенні вистави:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Помилка при створенні вистави',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

    public function storeHall(StoreHallRequest $request)
    {
        try {
            \DB::beginTransaction();

            try {
                $validated = $request->validated();
                
                // Створюємо зал
                $hall = Hall::create([
                    'hall_number' => $validated['hall_number']
                ]);

                // Створюємо місця для залу (10 рядів по 10 місць)
                $seats = [];
                for ($row = 1; $row <= 10; $row++) {
                    for ($seatNumber = 1; $seatNumber <= 10; $seatNumber++) {
                        $seats[] = [
                            'hall_id' => $hall->id,
                            'row' => $row,
                            'seat_number' => $seatNumber,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }

                // Масово вставляємо місця
                \DB::table('seats')->insert($seats);

                \DB::commit();

                // Створюємо місця для всіх існуючих залів, які не мають місць
                $hallsWithoutSeats = Hall::whereNotIn('id', function($query) {
                    $query->select('hall_id')
                        ->from('seats')
                        ->distinct();
                })->get();

                foreach ($hallsWithoutSeats as $existingHall) {
                    $existingSeats = [];
                    for ($row = 1; $row <= 10; $row++) {
                        for ($seatNumber = 1; $seatNumber <= 10; $seatNumber++) {
                            $existingSeats[] = [
                                'hall_id' => $existingHall->id,
                                'row' => $row,
                                'seat_number' => $seatNumber,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                        }
                    }
                    \DB::table('seats')->insert($existingSeats);
                }

                return response()->json([
                    'message' => 'Зал створений з місцями',
                    'hall' => $hall,
                    'seats_count' => count($seats)
                ], 201);
            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Помилка при створенні зали:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        }
        catch (\Exception $e) {
            return response()->json([
                'message' => 'Помилка при створенні зали',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);    
        }
    }

    public function getHalls(){
        $halls = Hall::all();
        return response()->json($halls);
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

    public function createSeatsForExistingHalls()
    {
        try {
            \DB::beginTransaction();

            $hallsWithoutSeats = Hall::whereNotIn('id', function($query) {
                $query->select('hall_id')
                    ->from('seats')
                    ->distinct();
            })->get();

            $createdSeatsCount = 0;

            foreach ($hallsWithoutSeats as $hall) {
                $seats = [];
                for ($row = 1; $row <= 10; $row++) {
                    for ($seatNumber = 1; $seatNumber <= 10; $seatNumber++) {
                        $seats[] = [
                            'hall_id' => $hall->id,
                            'row' => $row,
                            'seat_number' => $seatNumber,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
                \DB::table('seats')->insert($seats);
                $createdSeatsCount += count($seats);
            }

            \DB::commit();

            return response()->json([
                'message' => 'Місця створені для всіх залів',
                'halls_processed' => count($hallsWithoutSeats),
                'seats_created' => $createdSeatsCount
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'message' => 'Помилка при створенні місць',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

    public function getShowSeats(Show $show)
    {
        try {
            // Отримуємо всі місця для залу
            $allSeats = \DB::table('seats')
                ->where('hall_id', $show->hall_id)
                ->orderBy('row')
                ->orderBy('seat_number')
                ->get();

            // Отримуємо заброньовані місця
            $bookedSeats = \DB::table('tickets')
                ->where('show_id', $show->id)
                ->whereNotNull('user_id')
                ->pluck('seat_id')
                ->toArray();

            // Розділяємо місця на доступні та заброньовані
            $availableSeats = [];
            $bookedSeatsDetails = [];

            foreach ($allSeats as $seat) {
                if (in_array($seat->id, $bookedSeats)) {
                    $bookedSeatsDetails[] = $seat;
                } else {
                    $availableSeats[] = $seat;
                }
            }

            return response()->json([
                'id' => $show->id,
                'datetime' => $show->datetime,
                'available_seats' => $availableSeats,
                'booked_seats' => $bookedSeatsDetails,
                'price' => $show->price
            ]);

        } catch (\Exception $e) {
            \Log::error('Помилка при отриманні місць:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Помилка при отриманні місць',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

    public function bookTickets(Request $request): JsonResponse 
    {
        try {
            \DB::beginTransaction();

            \Log::info('Отримано запит на бронювання квитків', ['request' => $request->all()]);

            // Логування до валідації
            \Log::info('Початок валідації');

            $validated = $request->validate([
                'tickets' => 'required|array',
                'tickets.*.show_id' => 'required|integer|exists:shows,id',
                'tickets.*.seat_id' => 'required|integer|exists:seats,id',
                'discount_id' => 'nullable|integer|exists:discounts,id'
            ]);

            // Логування після успішної валідації
            \Log::info('Валідація успішна', ['validated' => $validated]);

            // ... решта коду
        } catch (\Exception $e) {
            // Обробка помилок
        }
    }
}
