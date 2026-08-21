<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('products.index');
});

// Resource Routes for Categories & Products
Route::resource('categories', CategoryController::class);
Route::resource('products', ProductController::class);
