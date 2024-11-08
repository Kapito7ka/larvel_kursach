<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ActorsController;
use App\Http\Controllers\Api\ShowsController;
use App\Http\Controllers\Api\PerformancesController;
use App\Http\Controllers\Api\ProducerController;
use App\Http\Controllers\Api\ProducersController;
use App\Http\Controllers\Api\TicketsController;
use App\Http\Controllers\Api\AuthController;

Route::group(['middleware' => ['api']], function () {
    Route::options('/{any}', function () {
        return response()->json([], 200);
    })->where('any', '.*');

    Route::get('/ping', function () {
        return response()->json(['message' => 'pong']);
    });

    Route::get('/test', function () {
        return response()->json(['message' => 'API works!']);
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::prefix('actors')->group(function () {
        Route::get('/', [ActorsController::class, 'index']);
        Route::post('/', [ActorsController::class, 'store']);
        Route::post('/{actor}', [ActorsController::class, 'create']);
        Route::get('/{actor}', [ActorsController::class, 'show']);
        Route::put('/{actor}', [ActorsController::class, 'update']);
        Route::delete('/{actor}', [ActorsController::class, 'destroy']);
    });

    Route::prefix('shows')->group(function () {
        Route::get('/', [ShowsController::class, 'index']);
        Route::post('/', [ShowsController::class, 'store']);
        Route::get('/{show}', [ShowsController::class, 'show']);
        Route::put('/{show}', [ShowsController::class, 'update']);
        Route::delete('/{show}', [ShowsController::class, 'destroy']);
    });

    Route::prefix('performances')->group(function () {
        Route::get('/', [PerformancesController::class, 'index']);
        Route::post('/', [PerformancesController::class, 'store']);
        Route::get('/{performance}', [PerformancesController::class, 'show']);
        Route::put('/{performance}', [PerformancesController::class, 'update']);
        Route::delete('/{performance}', [PerformancesController::class, 'destroy']);
    });

    Route::prefix('producers')->group(function () {
        Route::get('/', [ProducersController::class, 'index']);
        Route::post('/', [ProducersController::class, 'store']);
        Route::get('/{producer}', [ProducersController::class, 'show']);
        Route::put('/{producer}', [ProducersController::class, 'update']);
        Route::delete('/{producer}', [ProducersController::class, 'destroy']);
    });

    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketsController::class, 'index']);
        Route::get('/{id}', [TicketsController::class, 'show']);
        Route::get('/user/{user_id}', [TicketsController::class, 'getUserTickets']);
        Route::post('/', [TicketsController::class, 'store']);
    });

    Route::post('/auth/register', [AuthController::class, 'register']);
});