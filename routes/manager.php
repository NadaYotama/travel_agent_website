<?php

use App\Http\Controllers\Manager;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [Manager\DashboardController::class, 'index'])->name('dashboard');
Route::resource('services', Manager\ServiceController::class);
