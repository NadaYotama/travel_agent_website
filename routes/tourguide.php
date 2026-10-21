<?php

use App\Http\Controllers\TourGuide;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [TourGuide\DashboardController::class, 'index'])->name('dashboard');
Route::post('bookings/payment-statuses', [TourGuide\BookingController::class, 'paymentStatuses'])->name('bookings.payment-statuses');
Route::resource('bookings', TourGuide\BookingController::class)->only(['index', 'show', 'update']);
Route::post('bookings/{booking}/claim', [TourGuide\BookingController::class, 'claim'])->name('bookings.claim');
Route::get('bookings/{booking}/payment-status', [TourGuide\BookingController::class, 'paymentStatus'])->name('bookings.payment-status');
Route::post('bookings/{booking}/complete', [TourGuide\BookingController::class, 'complete'])->name('bookings.complete');
Route::post('bookings/{booking}/cancel', [TourGuide\BookingController::class, 'cancel'])->name('bookings.cancel');
