<?php

use App\Http\Controllers\User;
use Illuminate\Support\Facades\Route;

Route::get('/beranda', [User\HomeController::class, 'index'])->name('home');

Route::get('/paket/{package:slug}/pesan', [User\BookingController::class, 'create'])->name('bookings.create');
Route::post('/paket/{package:slug}/pesan', [User\BookingController::class, 'store'])->name('bookings.store');
Route::get('/pesanan/{booking:code}', [User\BookingController::class, 'show'])->name('bookings.show');
Route::post('/pesanan/{booking:code}/batal', [User\BookingController::class, 'cancel'])->name('bookings.cancel');

Route::get('/pesanan/{booking:code}/pembayaran', [User\PaymentController::class, 'create'])->name('payments.create');
Route::post('/pesanan/{booking:code}/pembayaran/midtrans', [User\PaymentController::class, 'createMidtransPayment'])->name('payments.midtrans');
