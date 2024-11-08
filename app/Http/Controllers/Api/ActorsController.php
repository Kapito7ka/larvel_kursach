<?php

namespace App\Http\Controllers\Api;

use App\Models\Actor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ActorsController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/actors",
     *     summary="List of actors",
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function index(): JsonResponse {
        $actors = Actor::all();
        return response()->json($actors);
    }

    /**
     * @OA\Get(
     *     path="/actors/{id}",
     *     summary="Get a specific actor",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Actor ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Actor not found"
     *     )
     * )
     */
    public function show($id): JsonResponse {
        $actor = Actor::find($id);
        
        if (!$actor) {
            return response()->json(['message' => 'Actor not found'], 404);
        }

        return response()->json($actor);
    }
}
