<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PriceItemController;
use App\Http\Controllers\ClientController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::get('/create', [ClientController::class, 'create'])->name('create');
        Route::post('/', [ClientController::class, 'store'])->name('store');
        Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit');
        Route::put('/{client}', [ClientController::class, 'update'])->name('update');
        Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('price-items')->name('price-items.')->group(function () {
        Route::get('/', [PriceItemController::class, 'index'])->name('index');
        Route::get('/create', [PriceItemController::class, 'create'])->name('create');
        Route::post('/', [PriceItemController::class, 'store'])->name('store');
        Route::get('/{priceItem}/edit', [PriceItemController::class, 'edit'])->name('edit');
        Route::put('/{priceItem}', [PriceItemController::class, 'update'])->name('update');
        Route::delete('/{priceItem}', [PriceItemController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/settings.php';
