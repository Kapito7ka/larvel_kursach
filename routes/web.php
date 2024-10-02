<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActorsController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('actors')->group(function () {
        Route::get('', [ActorsController::class, 'index'])->name('actors');
        Route::get('create', [ActorsController::class, 'create'])->name('actors.create');
        Route::post('', [ActorsController::class, 'store'])->name('actors.store');
        Route::get('{actor}/edit', [ActorsController::class, 'edit'])->name('actors.edit');
        Route::put('{actor}', [ActorsController::class, 'update'])->name('actors.update');
        Route::delete('{actor}', [ActorsController::class, 'destroy'])->name('actors.destroy');
        Route::put('{actor}/restore', [ActorsController::class, 'restore'])->name('actors.restore');
    });
});

require __DIR__.'/auth.php';
