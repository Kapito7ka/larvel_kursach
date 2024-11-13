<?php

namespace App\Http\Controllers\Api;

use App\Events\TicketBooked;
use App\Models\Ticket;
use App\Models\Seat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketsController extends ApiController
{
    /**
     * Метод для отримання списку всіх квитків
     */
    public function index(): JsonResponse
    {
        $tickets = Ticket::all();
        return response()->json($tickets);
    }

    /**
     * Метод для отримання окремого квитка за його ID
     */
    public function show($id): JsonResponse
    {
        try {
            $ticket = Ticket::with(['show.performance', 'show.hall', 'seat', 'discount'])
                ->find((int) $id);

            if (!$ticket) {
                return response()->json(['message' => 'Квиток не знайдено'], 404);
            }

            return response()->json($ticket);
        } catch (\Exception $e) {
            Log::error('Помилка при отриманні квитка:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Помилка при отриманні квитка',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }


    public function bookTickets(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            Log::info('Отримано запит на бронювання квитків', ['request' => $request->all()]);

            $validated = $request->validate([
                'tickets' => 'required|array',
                'tickets.*.show_id' => 'required|integer|exists:shows,id',
                'tickets.*.seat_id' => 'required|integer|exists:seats,id',
                'discount_id' => 'nullable|integer|exists:discounts,id'
            ]);

            Log::info('Валідація успішна', ['validated' => $validated]);

            $bookedTickets = [];
            $userId = auth()->id();

            Log::info('ID користувача після авторизації', ['userId' => $userId]);

            if (!$userId) {
                DB::rollBack();
                Log::warning('Користувач не аутентифікований');
                return response()->json([
                    'message' => 'Користувач не аутентифікований'
                ], 401);
            }

            foreach ($validated['tickets'] as $ticketData) {
                Log::info('Обробка квитка', ['ticketData' => $ticketData]);

                $show = \App\Models\Show::findOrFail($ticketData['show_id']);
                Log::info('Знайдений показ', ['show' => $show]);

                $seat = Seat::findOrFail($ticketData['seat_id']);
                $seatExists = ($seat->hall_id === $show->hall_id);

                Log::info('Перевірка належності місця до залу', ['seatExists' => $seatExists]);

                if (!$seatExists) {
                    DB::rollBack();
                    Log::warning('Вибране місце не належить до залу показу', [
                        'seat_id' => $ticketData['seat_id'],
                        'show_id' => $ticketData['show_id']
                    ]);
                    return response()->json([
                        'message' => 'Вибране місце не належить до залу показу',
                        'seat_id' => $ticketData['seat_id'],
                        'show_id' => $ticketData['show_id']
                    ], 400);
                }

                $existingTicket = Ticket::where('show_id', $ticketData['show_id'])
                                        ->where('seat_id', $ticketData['seat_id'])
                                        ->whereNull('user_id')
                                        ->lockForUpdate()
                                        ->first();

                Log::info('Перевірка існуючого бронювання', ['existingTicket' => $existingTicket]);

                if (!$existingTicket) {
                    DB::rollBack();
                    Log::warning('Місце вже заброньоване', [
                        'seat_id' => $ticketData['seat_id'],
                        'show_id' => $ticketData['show_id']
                    ]);
                    return response()->json([
                        'message' => 'Місце вже заброньоване',
                        'seat_id' => $ticketData['seat_id'],
                        'show_id' => $ticketData['show_id']
                    ], 409);
                }

                $finalPrice = $show->price;
                if (isset($validated['discount_id'])) {
                    $discount = \App\Models\Discount::find($validated['discount_id']);
                    Log::info('Знайдений знижка', ['discount' => $discount]);
                    if ($discount && is_numeric($discount->percentage)) {
                        $finalPrice = $show->price * (1 - $discount->percentage / 100);
                    } else {
                        Log::warning('Некоректний відсоток знижки або знижка не знайдена', ['discount' => $discount]);
                    }
                }

                Log::info('Розрахована фінальна ціна', ['finalPrice' => $finalPrice]);

                $ticketNumber = 'TKT' . uniqid() . '-R' . $seat->row . 'S' . $seat->number;

                $existingTicket->update([
                    'ticket_number' => $ticketNumber,
                    'date' => $show->datetime->toDateString(),
                    'time' => $show->datetime->format('H:i:s'),
                    'user_id' => $userId,
                    'price' => $finalPrice,
                    'discount_id' => $validated['discount_id'] ?? null
                ]);

                Log::info('Оновлено квиток', ['ticket' => $existingTicket]);

                $bookedTickets[] = $existingTicket;

                if (!$request->input('silent', false)) {
                    event(new TicketBooked($existingTicket));
                }
            }

            DB::commit();
            Log::info('Транзакція успішно завершена');

            return response()->json([
                'message' => 'Квитки успішно заброньовані',
                'tickets' => $bookedTickets
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Помилка при бронюванні квитків:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Помилка при бронюванні квитків',
                'error' => config('app.debug') ? $e->getMessage() : 'Помилка сервера'
            ], 500);
        }
    }

    public function getCurrentUserTickets(): JsonResponse
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                Log::warning('Неавторизований доступ до getCurrentUserTickets');
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $tickets = Ticket::with([
                'show.performance',
                'show.hall',
                'seat',
                'discount'
            ])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

            Log::info('Успішно отримано квитки користувача', [
                'user_id' => $user->id,
                'tickets_count' => $tickets->count()
            ]);

            return response()->json($tickets);

        } catch (\Exception $e) {
            Log::error('Помилка в getCurrentUserTickets', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Помилка при отриманні квитків',
                'error' => config('app.debug') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

    public function cancelBooking($id): JsonResponse
    {
        try {
            Log::info('Початок відміни бронювання', [
                'ticket_id' => $id,
                'user_id' => auth()->id()
            ]);
            
            if (!auth()->check()) {
                return response()->json([
                    'message' => 'Необхідна авторизація'
                ], 401);
            }

            DB::beginTransaction();

            $ticket = Ticket::find($id);
            
            if (!$ticket) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Квиток не знайдено'
                ], 404);
            }

            if ($ticket->user_id !== auth()->id()) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Ви не маєте прав для відміни цього бронювання'
                ], 403);
            }

            try {
                $updateResult = $ticket->update([
                    'user_id' => null
                ]);

                if (!$updateResult) {
                    throw new \Exception('Не вдалося оновити дані квитка');
                }

                DB::commit();
                
                return response()->json([
                    'message' => 'Бронювання успішно відмінено',
                    'ticket' => $ticket
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Помилка при відміні бронювання:', [
                    'ticket_id' => $id,
                    'error' => $e->getMessage()
                ]);

                return response()->json([
                    'message' => 'Помилка при відміні бронювання',
                    'error' => config('app.debug') ? $e->getMessage() : 'Помилка сервера'
                ], 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Помилка при відміні бронювання:', [
                'ticket_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Помилка при відміні бронювання',
                'error' => config('app.debug') ? $e->getMessage() : 'Помилка сервера'
            ], 500);
        }
    }
}
