<?php

use App\Http\Controllers\Api\User\BookingController;
use App\Http\Controllers\Api\User\InvoiceController;
use App\Http\Controllers\Api\User\RoomController;
use App\Http\Controllers\Api\User\RoomTypeController;
use App\Http\Controllers\Api\User\ServiceController;
use Illuminate\Support\Facades\Route;


Route::middleware(['lang', 'auth', 'role.user'])->prefix('user')->group(function () {
    Route::controller(RoomTypeController::class)->prefix('room_types')->group(function () {
        Route::get('/favorite', 'getFavorite');
        Route::post('/{id}/favorite/mark_as_favorite', 'markAsFavorite');
        Route::delete('/{id}/favorite/unmark_as_favorite', 'unmarkAsFavorite');
    });

    Route::controller(RoomController::class)->prefix('rooms')->group(function () {
        Route::get('/favorite', 'getFavorite');
        Route::post('/{id}/favorite/mark_as_favorite', 'markAsFavorite');
        Route::delete('/{id}/favorite/unmark_as_favorite', 'unmarkAsFavorite');
    });

    Route::controller(ServiceController::class)->prefix('services')->group(function () {
        Route::get('/favorite', 'getFavorite');
        Route::post('/{id}/favorite/mark_as_favorite', 'markAsFavorite');
        Route::delete('/{id}/favorite/unmark_as_favorite', 'unmarkAsFavorite');
    });

    Route::controller(BookingController::class)->prefix('bookings')->group(function () {
        Route::post('/calculate_cost', 'calculateCost');
        Route::post('/payment_intent', 'paymentIntent')->name('payment_intent');
        Route::post('/confirm_payment', 'confirmPayment');
    });

    Route::controller(InvoiceController::class)->prefix('invoices')->group(function () {
        Route::get('', 'index');
        Route::get('/{id}', 'show');
        Route::get('/{id}/bookings', 'bookings');
    });
});
