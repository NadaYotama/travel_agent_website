<?php

use App\Http\Controllers\User;
use Illuminate\Support\Facades\Route;

Route::get('/beranda', [User\HomeController::class, 'index'])->name('home');

