<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PriceItemController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InspirationController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/inspirations', [InspirationController::class, 'index'])->name('inspirations.index');

    Route::prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [AppointmentController::class, 'index'])->name('index');
        Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('calendar');
        Route::get('/create', [AppointmentController::class, 'create'])->name('create');
        Route::post('/', [AppointmentController::class, 'store'])->name('store');
        Route::get('/{appointment}/edit', [AppointmentController::class, 'edit'])->name('edit');
        Route::put('/{appointment}', [AppointmentController::class, 'update'])->name('update');
        Route::delete('/{appointment}', [AppointmentController::class, 'destroy'])->name('destroy');
    });

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
