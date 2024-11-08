<?php

namespace App\Http\Controllers\Api;

use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class TicketsController extends ApiController
{
    // Метод для отримання списку всіх квитків
    public function index(): JsonResponse {
        $tickets = Ticket::all();
        return response()->json($tickets);
    }

    // Метод для отримання окремого квитка за його ID
    public function show(int $id): JsonResponse {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        return response()->json($ticket);
    }

    /**
     * @OA\Get(
     *     path="/tickets/user/{user_id}",
     *     summary="Get all tickets for a specific user",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User has no tickets"
     *     )
     * )
     */
    public function getUserTickets(int $user_id): JsonResponse {
        $tickets = Ticket::where('user_id', $user_id)->get();

        if ($tickets->isEmpty()) {
            return response()->json(['message' => 'User has no tickets'], 404);
        }

        return response()->json($tickets);
    }

    /**
     * @OA\Post(
     *     path="/tickets",
     *     summary="Add a new ticket",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ticket_number", "datetime", "show_id", "seat_id", "user_id", "price"},
     *             @OA\Property(property="ticket_number", type="string"),
     *             @OA\Property(property="datetime", type="string", format="date-time"),
     *             @OA\Property(property="show_id", type="integer"),
     *             @OA\Property(property="seat_id", type="integer"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="price", type="number", format="decimal"),
     *             @OA\Property(property="discount_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ticket created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse {
        $validated = $request->validate([
            'ticket_number' => 'required|string|max:255',
            'datetime' => 'required|date',
            'show_id' => 'required|integer',
            'seat_id' => 'required|integer',
            'user_id' => 'required|integer',
            'price' => 'required|numeric',
            'discount_id' => 'nullable|integer'
        ]);

        $ticket = Ticket::create($validated);

        return response()->json($ticket, 201);
    }
}
