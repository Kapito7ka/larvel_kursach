<?php
use App\Http\Controllers\ShowsController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActorsController;
use App\Http\Controllers\ProducerController;
use Illuminate\Foundation\Application;
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

    Route::prefix('producers')->group(function () {
        Route::get('', [ProducerController::class, 'index'])->name('producers');
        Route::get('create', [ProducerController::class, 'create'])->name('producers.create');
        Route::post('', [ProducerController::class, 'store'])->name('producers.store');
        Route::get('{producer}/edit', [ProducerController::class, 'edit'])->name('producers.edit');
        Route::put('{producer}', [ProducerController::class, 'update'])->name('producers.update');
        Route::delete('{producer}', [ProducerController::class, 'destroy'])->name('producers.destroy');
        Route::put('{producer}/restore', [ProducerController::class, 'restore'])->name('producers.restore');
    });

    Route::prefix('performances')->group(function () {
        Route::get('', [PerformanceController::class, 'index'])->name('performances');
        Route::get('create', [PerformanceController::class, 'create'])->name('performances.create');
        Route::post('', [PerformanceController::class, 'store'])->name('performances.store');
        Route::get('{performance}/edit', [PerformanceController::class, 'edit'])->name('performances.edit');
        Route::put('{performance}', [PerformanceController::class, 'update'])->name('performances.update');
        Route::delete('{performance}', [PerformanceController::class, 'destroy'])->name('performances.destroy');
        Route::put('{performance}/restore', [PerformanceController::class, 'restore'])->name('performances.restore');
    });

    Route::prefix('shows')->group(function () {
        Route::get('', [ShowsController::class, 'index'])->name('shows');
        Route::get('create', [ShowsController::class, 'create'])->name('shows.create');
        Route::post('', [ShowsController::class, 'store'])->name('shows.store');
        Route::get('{show}/edit', [ShowsController::class, 'edit'])->name('shows.edit');
        Route::put('{show}', [ShowsController::class, 'update'])->name('shows.update');
        Route::delete('{show}', [ShowsController::class, 'destroy'])->name('shows.destroy');
        Route::put('{show}/restore', [ShowsController::class, 'restore'])->name('shows.restore');
    });
});

require __DIR__.'/auth.php';
