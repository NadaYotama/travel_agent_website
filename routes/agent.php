<?php

use App\Http\Controllers\Agent;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [Agent\DashboardController::class, 'index'])->name('dashboard');
Route::resource('bookings', Agent\BookingController::class)->only(['index', 'show', 'update']);
Route::post('bookings/{booking}/claim', [Agent\BookingController::class, 'claim'])->name('bookings.claim');
Route::post('payments/{payment}/verify', [Agent\BookingController::class, 'verifyPayment'])->name('payments.verify');
Route::post('bookings/{booking}/complete', [Agent\BookingController::class, 'complete'])->name('bookings.complete');
Route::post('bookings/{booking}/cancel', [Agent\BookingController::class, 'cancel'])->name('bookings.cancel');

