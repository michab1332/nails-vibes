<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PriceItemController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::prefix('price-items')->name('price-items.')->group(function () {
        Route::get('/', [PriceItemController::class, 'index'])->name('index');
    });
});

require __DIR__.'/settings.php';
