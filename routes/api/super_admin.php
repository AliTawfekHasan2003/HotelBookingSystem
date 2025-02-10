<?php

use App\Http\Controllers\Api\RoomTypeServiceController;
use App\Http\Controllers\Api\SuperAdmin\InvoiceController;
use App\Http\Controllers\Api\SuperAdmin\RoomController;
use App\Http\Controllers\Api\SuperAdmin\RoomTypeController;
use App\Http\Controllers\Api\SuperAdmin\ServiceController;
use App\Http\Controllers\Api\SuperAdmin\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware(['lang', 'auth', 'role.super_admin'])->prefix('super_admin/dashboard')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::patch('users/{id}/assign_role', 'assignRole');
    });

    Route::controller(RoomTypeController::class)->prefix('room_types/deleted')->group(function () {
        Route::get('', 'trashedIndex');
        Route::get('/{id}', 'trashedShow');
        Route::post('/{id}/restore', 'trashedRestore');
        Route::delete('/{id}/force', 'trashedForceDelete');
    });

    Route::controller(RoomController::class)->prefix('rooms')->group(function () {
        Route::get('/deleted', 'trashedIndex');
        Route::get('/deleted/{id}', 'trashedShow');
        Route::post('/deleted/{id}/restore', 'trashedRestore');
        Route::delete('/deleted/{id}/force', 'trashedForceDelete');
        Route::get('/{id}/bookings', 'bookings');
    });

    Route::controller(ServiceController::class)->prefix('services')->group(function () {
        Route::get('/deleted', 'trashedIndex');
        Route::get('/deleted/{id}', 'trashedShow');
        Route::post('/deleted/{id}/restore', 'trashedRestore');
        Route::delete('/deleted/{id}/force', 'trashedForceDelete');
        Route::get('/{id}/bookings', 'bookings');
    });

    Route::controller(InvoiceController::class)->prefix('invoices')->group(function () {
        Route::get('', 'index');
        Route::get('/{id}', 'show');
        Route::get('/{id}/bookings', 'bookings');
    });
});
