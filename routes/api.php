<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthSocialController;
use App\Http\Controllers\Api\BaseRoomController;
use App\Http\Controllers\Api\BaseRoomTypeController;
use App\Http\Controllers\Api\BaseServiceController;
use App\Http\Controllers\Api\BaseUserController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

require __DIR__  . '/api/user.php';
require __DIR__  . '/api/admin.php';
require __DIR__  . '/api/super_admin.php';

Route::middleware('lang')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::get('email/verify/{id}', 'verifyEmail')->name('verify.email');
        Route::post('email/verify/resend', 'resendVerificationEmail');
        Route::post('login', 'login')->name('login');
        Route::post('logout', 'logout')->middleware('auth');
        Route::post('refresh', 'refresh')->middleware('auth');
    });

    Route::controller(AuthSocialController::class)->prefix('auth')->group(function () {
        Route::get('google', 'redirectToGoogle');
        Route::get('google/callback', 'googleCallback');
        Route::get('github', 'redirectToGithub');
        Route::get('github/callback', 'githubCallback');
    });
    //All roles
    Route::middleware(['auth', 'checkRole'])->prefix('{role}')->whereIn('role', ['user', 'admin', 'super_admin'])->group(function () {
        Route::controller(NotificationController::class)->prefix('notifications')->group(function () {
            Route::get('', 'getAllNotifications');
            Route::get('unread', 'getUnreadNotifications');
            Route::patch('unread/{id}/mark_as_read', 'markAsRead');
            Route::patch('unread/mark_as_read', 'markAllAsRead');
        });

        Route::controller(BaseUserController::class)->prefix('settings')->group(function () {
            Route::get('profile', 'showProfile');
            Route::patch('profile', 'updateProfile');
            Route::post('password', 'setPassword');
            Route::patch('password', 'updatePassword');
        });
        Route::controller(BaseRoomTypeController::class)->prefix('room_types')->group(function () {
            Route::get('', 'index');
            Route::get('/{id}', 'show');
            Route::get('/{id}/rooms', 'rooms');
            Route::get('/{id}/services', 'services');
        });

        Route::controller(BaseRoomController::class)->prefix('rooms')->group(function () {
            Route::get('', 'index');
            Route::get('/{id}', 'show');
            Route::get('/{id}/unavailable_dates', 'unavailableDates');
        });    

        Route::controller(BaseServiceController::class)->prefix('services')->group(function () {
            Route::get('', 'index');
            Route::get('/{id}', 'show');
            Route::get('/{id}/room_types', 'roomTypes');
            Route::get('/{id}/available_units', 'limitedUnits');
        });    
    });
});
