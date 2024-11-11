<?php

namespace App\Http\Controllers\Api;

use App\Models\Ticket;
use App\Models\Seat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

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
    public function show(int $id): JsonResponse
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Квиток не знайдений'], 404);
        }

        return response()->json($ticket);
    }

    /**
     * Метод для бронювання квитків
     */
    public function bookTickets(Request $request): JsonResponse
    {
        try {
            \DB::beginTransaction();

            \Log::info('Отримано запит на бронювання квитків', ['request' => $request->all()]);

            $validated = $request->validate([
                'tickets' => 'required|array',
                'tickets.*.show_id' => 'required|integer|exists:shows,id',
                'tickets.*.seat_id' => 'required|integer|exists:seats,id',
                'discount_id' => 'nullable|integer|exists:discounts,id'
            ]);

            \Log::info('Валідація успішна', ['validated' => $validated]);

            $bookedTickets = [];
            $userId = auth()->id();

            \Log::info('ID користувача після авторизації', ['userId' => $userId]);

            if (!$userId) {
                \DB::rollBack();
                \Log::warning('Користувач не аутентифікований');
                return response()->json([
                    'message' => 'Користувач не аутентифікований'
                ], 401);
            }

            foreach ($validated['tickets'] as $ticketData) {
                \Log::info('Обробка квитка', ['ticketData' => $ticketData]);

                $show = \App\Models\Show::findOrFail($ticketData['show_id']);
                \Log::info('Знайдений показ', ['show' => $show]);

                // Перевірка, чи належить місце до залу показу
                $seat = Seat::findOrFail($ticketData['seat_id']);
                $seatExists = ($seat->hall_id === $show->hall_id);

                \Log::info('Перевірка належності місця до залу', ['seatExists' => $seatExists]);

                if (!$seatExists) {
                    \DB::rollBack();
                    \Log::warning('Вибране місце не належить до залу показу', [
                        'seat_id' => $ticketData['seat_id'],
                        'show_id' => $ticketData['show_id']
                    ]);
                    return response()->json([
                        'message' => 'Вибране місце не належить до залу показу',
                        'seat_id' => $ticketData['seat_id'],
                        'show_id' => $ticketData['show_id']
                    ], 400);
                }

                // Використання блокування для запобігання одночасного бронювання
                $existingTicket = Ticket::where('show_id', $ticketData['show_id'])
                                        ->where('seat_id', $ticketData['seat_id'])
                                        ->whereNull('user_id') // Шукаємо незаброньований квиток
                                        ->lockForUpdate()
                                        ->first();

                \Log::info('Перевірка існуючого бронювання', ['existingTicket' => $existingTicket]);

                if (!$existingTicket) {
                    \DB::rollBack();
                    \Log::warning('Місце вже заброньоване', [
                        'seat_id' => $ticketData['seat_id'],
                        'show_id' => $ticketData['show_id']
                    ]);
                    return response()->json([
                        'message' => 'Місце вже заброньоване',
                        'seat_id' => $ticketData['seat_id'],
                        'show_id' => $ticketData['show_id']
                    ], 409);
                }

                // Розрахунок ціни з урахуванням знижки
                $finalPrice = $show->price;
                if (isset($validated['discount_id'])) {
                    $discount = \App\Models\Discount::find($validated['discount_id']);
                    \Log::info('Знайдений знижка', ['discount' => $discount]);
                    if ($discount && is_numeric($discount->percentage)) {
                        $finalPrice = $show->price * (1 - $discount->percentage / 100);
                    } else {
                        \Log::warning('Некоректний відсоток знижки або знижка не знайдена', ['discount' => $discount]);
                    }
                }

                \Log::info('Розрахована фінальна ціна', ['finalPrice' => $finalPrice]);

                // Генерація ticket_number з інформацією про ряд та місце
                $ticketNumber = 'TKT' . uniqid() . '-R' . $seat->row . 'S' . $seat->number;

                // Оновлюємо існуючий квиток замість створення нового
                $existingTicket->update([
                    'ticket_number' => $ticketNumber,
                    'date' => $show->datetime->toDateString(),
                    'time' => $show->datetime->format('H:i:s'),
                    'user_id' => $userId,
                    'price' => $finalPrice,
                    'discount_id' => $validated['discount_id'] ?? null
                ]);

                \Log::info('Оновлено квиток', ['ticket' => $existingTicket]);

                $bookedTickets[] = $existingTicket;
            }

            \DB::commit();
            \Log::info('Транзакція успішно завершена');

            return response()->json([
                'message' => 'Квитки успішно заброньовані',
                'tickets' => $bookedTickets
            ], 201);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Помилка при бронюванні квитків:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Помилка при бронюванні квитків',
                'error' => config('app.debug') ? $e->getMessage() : 'Помилка сервера'
            ], 500);
        }
    }
}
