<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PesananController;
use Illuminate\Support\Facades\Route;

// Redirect root to shop
Route::get('/', function () {
    return redirect('/shop');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Routes
Route::get('/shop', [ProdukController::class, 'index'])->name('shop');
Route::get('/guide', function () {
    return view('guide');
})->name('guide');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/orders', [PesananController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [PesananController::class, 'show'])->name('orders.show');
    Route::post('/api/orders', [PesananController::class, 'store'])->name('orders.store');
});
