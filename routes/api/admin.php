<?php

use App\Http\Controllers\Api\Admin\RoomController;
use App\Http\Controllers\Api\Admin\RoomTypeController;
use App\Http\Controllers\Api\Admin\RoomTypeServiceController;
use App\Http\Controllers\Api\Admin\ServiceController;
use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware(['lang', 'auth', 'dashboard', 'checkRole'])->prefix('{role}/dashboard')->whereIn('role', ['admin', 'super_admin'])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('users', 'index');
        Route::get('users/{id}', 'showUser');
    });

    Route::apiResource('room_types', RoomTypeController::class)->except(['index', 'show']);

    Route::apiResource('rooms', RoomController::class)->except(['index', 'show']);

    Route::get('services/{id}/unavailable_dates', [ServiceController::class, 'unavailableDates']);
    Route::apiResource('services', ServiceController::class)->except(['index', 'show']);

    Route::controller(RoomTypeServiceController::class)->prefix('room_type_services')->group(function () {
        Route::post('', 'store');
        Route::delete('', 'destroy');
    });
});
