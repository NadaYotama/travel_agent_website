<?php

use App\Http\Controllers\Vendor;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [Vendor\DashboardController::class, 'index'])->name('dashboard');
Route::resource('services', Vendor\ServiceController::class);

