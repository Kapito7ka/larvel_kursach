<?php

namespace App\Http\Controllers\Api;

use App\Models\Performance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class PerformancesController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/performances",
     *     summary="Get a list of all performances",
     *     @OA\Response(
     *         response=200,
     *         description="List of performances"
     *     )
     * )
     */
    public function index(): JsonResponse {
        $performances = Performance::all();
        return response()->json($performances);
    }

    /**
     * @OA\Get(
     *     path="/performances/search",
     *     summary="Search performances",
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         required=false,
     *         description="Title of the performance",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="duration",
     *         in="query",
     *         required=false,
     *         description="Duration of the performance",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of matching performances"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No performances found"
     *     )
     * )
     */
    public function search(Request $request): JsonResponse {
        $query = Performance::query();

        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }

        if ($request->has('duration')) {
            $query->where('duration', '=', $request->input('duration'));
        }

        $performances = $query->get();

        if ($performances->isEmpty()) {
            return response()->json(['message' => 'No performances found'], 404);
        }

        return response()->json($performances);
    }
}
