<?php

use Illuminate\Support\Facades\Route;

// Import controllers
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StorefrontController;

// =============================================================================
// PUBLIC ROUTES — Ai cũng truy cập được (không cần đăng nhập)
// =============================================================================
Route::get('/', [StorefrontController::class, 'index'])->name('home');
Route::get('/shop', [StorefrontController::class, 'shop'])->name('shop.index');
Route::get('/shop/{product}', [StorefrontController::class, 'show'])->name('shop.show');

// =============================================================================
// AUTH ROUTES — Chỉ dành cho Guest (đã đăng nhập thì không vào được)
// =============================================================================
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

// =============================================================================
// PROTECTED ROUTES — Phải đăng nhập
// =============================================================================
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

// =============================================================================
// ADMIN ROUTES — Phải đăng nhập + có role admin
// =============================================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('users', \App\Http\Controllers\UserController::class);
});
