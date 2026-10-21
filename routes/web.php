<?php

use App\Http\Controllers\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route publik & route lintas-role
|--------------------------------------------------------------------------
| File ini dimuat oleh RouteServiceProvider tanpa middleware role.
| Route per role ada di admin.php, tourguide.php, manager.php, dan user.php.
*/

// Halaman utama (welcome.blade.php)
Route::get('/', [User\HomeController::class, 'welcome'])->name('home');

// Katalog paket (publik). Nama route dipakai juga oleh welcome.blade.php.
Route::get('/paket', [User\PackageController::class, 'index'])->name('user.packages.index');
Route::get('/paket/{package:slug}', [User\PackageController::class, 'show'])->name('user.packages.show');

// Tujuan redirect setelah login (Breeze memakai /dashboard): arahkan sesuai role.
Route::get('/dashboard', fn () => redirect()->route(auth()->user()->role->dashboardRoute()))
    ->middleware(['auth', 'cache.headers:no_store;private'])
    ->name('dashboard');

// Profil akun: tersedia untuk semua role yang sudah login.
Route::middleware(['auth', 'cache.headers:no_store;private'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [User\ProfileController::class, 'edit'])->name('edit');
    Route::patch('/', [User\ProfileController::class, 'update'])->name('update');
    Route::put('/password', [User\ProfileController::class, 'updatePassword'])->name('password');
    Route::delete('/', [User\ProfileController::class, 'destroy'])->name('destroy');
});

// Login, register, reset password, dst.
require __DIR__ . '/auth.php';