<?php

namespace App\Http\Controllers\Api;

use App\Models\Actor;
use Illuminate\Http\JsonResponse;
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
}
