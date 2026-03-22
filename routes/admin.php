<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PriceItemController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

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
