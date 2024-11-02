<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ActorsController;
use App\Http\Controllers\Api\TicketsController;
use App\Http\Controllers\Api\PerformancesController; // Додайте контролер для вистав

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('actors')->group(function () {
    Route::get('/', [ActorsController::class, 'index'])->name('api.actors.index');
    Route::get('/{id}', [ActorsController::class, 'show'])->name('api.actors.show');
});

Route::prefix('tickets')->group(function () {
    Route::get('/', [TicketsController::class, 'index'])->name('api.tickets.index');
    Route::get('/{id}', [TicketsController::class, 'show'])->name('api.tickets.show');
    Route::get('/user/{user_id}', [TicketsController::class, 'getUserTickets'])->name('api.tickets.user');
    Route::post('/', [TicketsController::class, 'store'])->name('api.tickets.store');
});

Route::prefix('performances')->group(function () {
    Route::get('/', [PerformancesController::class, 'index'])->name('api.performances.index');
    Route::get('/search', [PerformancesController::class, 'search'])->name('api.performances.search');
});
