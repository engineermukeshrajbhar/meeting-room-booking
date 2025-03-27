<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Container\Attributes\Auth;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SubscriptionController;

Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return response()->json(['message' => 'Logged out successfully']);

    Route::get('/meeting-rooms', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/subscription/plans', [SubscriptionController::class, 'plans']);
    Route::post('/subscription/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);

    Route::post('/available-rooms', [BookingController::class, 'availableRooms'])->name('available-rooms');
    Route::get('/dashboard', [BookingController::class, 'index'])->name('dashboard');
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
});