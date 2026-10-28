<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
Route::resource('users', Admin\UserController::class);
Route::resource('destinations', Admin\DestinationController::class);
Route::resource('packages', Admin\TourPackageController::class);
Route::resource('promos', Admin\PromoController::class);
Route::resource('bookings', Admin\BookingController::class)->only(['index', 'show', 'update']);
Route::get('payments', [Admin\PaymentController::class, 'index'])->name('payments.index');
Route::patch('payments/{payment}/verify', [Admin\PaymentController::class, 'verify'])->name('payments.verify');
Route::patch('payments/{payment}/refund', [Admin\PaymentController::class, 'updateRefund'])->name('payments.refund');
Route::get('reports', [Admin\ReportController::class, 'index'])->name('reports.index');