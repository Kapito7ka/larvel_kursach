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
use App\Http\Controllers\Api\UsersController;

Route::group(['middleware' => ['api']], function () {
    Route::options('/{any}', function () {
        return response()->json([], 200);
    })->where('any', '.*');

    Route::get('/ping', function () {
        return response()->json(['message' => 'pong']);
    });
    Route::get('/user', function (Request $request) {
            return $request->user();
    })->middleware('auth:sanctum');
    Route::put('/', [UsersController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/', [UsersController::class, 'destroy'])->middleware('auth:sanctum');
    

    Route::prefix('actors')->group(function () {
        Route::get('/', [ActorsController::class, 'index']);
        Route::post('/', [ActorsController::class, 'store'])->middleware(['auth:sanctum', 'is_admin']);
        Route::post('/{actor}', [ActorsController::class, 'create'])->middleware(['auth:sanctum', 'is_admin']);
        Route::get('/{actor}', [ActorsController::class, 'show']);
        Route::put('/{actor}', [ActorsController::class, 'update'])->middleware(['auth:sanctum', 'is_admin']);
        Route::delete('/{actor}', [ActorsController::class, 'destroy'])->middleware(['auth:sanctum', 'is_admin']);
    });

    Route::prefix('shows')->group(function () {
        Route::get('/', [ShowsController::class, 'index']);
        Route::get('/hall', [ShowsController::class, 'getHalls']);
        Route::get('/{show}/seats', [ShowsController::class, 'getShowSeats']);
        Route::post('/hall', [ShowsController::class, 'storeHall'])->middleware(['auth:sanctum', 'is_admin']);
        Route::get('/{show}', [ShowsController::class, 'show']);
        Route::post('/', [ShowsController::class, 'store'])->middleware(['auth:sanctum', 'is_admin']);
        Route::put('/{show}', [ShowsController::class, 'update'])->middleware(['auth:sanctum', 'is_admin']);
        Route::delete('/{show}', [ShowsController::class, 'destroy'])->middleware(['auth:sanctum', 'is_admin']);
        Route::post('/create-seats', [ShowsController::class, 'createSeatsForExistingHalls'])
            ->middleware(['auth:sanctum', 'is_admin']);
    });

    Route::prefix('performances')->group(function () {
        Route::get('/genres', [PerformancesController::class, 'showGenres']);
        Route::get('/', [PerformancesController::class, 'index']);
        Route::get('/{performance}', [PerformancesController::class, 'show']);
        Route::get('/{performance}/shows', [PerformancesController::class, 'shows']);

        Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
            Route::post('/', [PerformancesController::class, 'store']);
            Route::put('/{performance}', [PerformancesController::class, 'update']);
            Route::delete('/{performance}', [PerformancesController::class, 'destroy']);
        });
    });

    Route::prefix('producers')->group(function () {
        Route::get('/', [ProducersController::class, 'index']);
        Route::post('/', [ProducersController::class, 'store'])->middleware(['auth:sanctum', 'is_admin']);
        Route::get('/{producer}', [ProducersController::class, 'show']);
        Route::put('/{producer}', [ProducersController::class, 'update'])->middleware(['auth:sanctum', 'is_admin']);
        Route::delete('/{producer}', [ProducersController::class, 'destroy'])->middleware(['auth:sanctum', 'is_admin']);
    });

    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketsController::class, 'index']);
        Route::get('/{id}', [TicketsController::class, 'show']);
        Route::get('/user/{user_id}', [TicketsController::class, 'getUserTickets']);
        
        Route::get('/available-seats/{show_id}', [TicketsController::class, 'getAvailableSeats']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/user', [TicketsController::class, 'getCurrentUserTickets']);
            Route::post('/purchase', [TicketsController::class, 'purchase']);
            Route::post('/book', [TicketsController::class, 'bookTickets']);
            Route::post('/{id}/cancel', [TicketsController::class, 'cancelBooking']);
            Route::get('/{id}', [TicketsController::class, 'show'])->where('id', '[0-9]+');
        });

        Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
            Route::put('/{id}', [TicketsController::class, 'updateTicket'])->where('id', '[0-9]+');
            Route::delete('/{id}', [TicketsController::class, 'destroy'])->where('id', '[0-9]+');
        });
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->middleware(['auth:sanctum', 'is_admin']);
        Route::get('/{id}', [UsersController::class, 'edit'])->middleware('auth:sanctum');
        Route::put('/{id}', [UsersController::class, 'update'])->middleware('auth:sanctum');
    });

});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register/admin', [AuthController::class, 'registerAdmin']);
    Route::post('/login/admin', [AuthController::class, 'loginAdmin']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::prefix('actors')->group(function () {
            Route::post('/', [ActorsController::class, 'store']);
            Route::put('/{actor}', [ActorsController::class, 'update']);
            Route::delete('/{actor}', [ActorsController::class, 'destroy']);
        });

        Route::prefix('performances')->group(function () {
            Route::post('/', [PerformancesController::class, 'store']);
            Route::put('/{performance}', [PerformancesController::class, 'update']);
            Route::delete('/{performance}', [PerformancesController::class, 'destroy']);
        });

        Route::prefix('producers')->group(function () {
            Route::post('/', [ProducersController::class, 'store']);
            Route::put('/{producer}', [ProducersController::class, 'update']);
            Route::delete('/{producer}', [ProducersController::class, 'destroy']);
        });
        Route::prefix('users')->group(function () {
            Route::get('/', [UsersController::class, 'index']);
        });

        Route::prefix('shows')->group(function () {
            Route::post('/', [ShowsController::class, 'store']);
            Route::put('/{show}', [ShowsController::class, 'update']);
            Route::delete('/{show}', [ShowsController::class, 'destroy']);
            Route::post('/hall', [ShowsController::class, 'storeHall']);
            Route::post('/create-seats', [ShowsController::class, 'createSeatsForExistingHalls']);
        });
    });
});

