<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Import Constants
use App\Constants\RouteConstants;

// Import Controllers
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebhookController;

// =============================================================================
// PUBLIC STOREFRONT ROUTES
// =============================================================================
Route::get(RouteConstants::PATH_HOME, [StorefrontController::class, 'index'])
    ->name(RouteConstants::NAME_HOME);

Route::get(RouteConstants::PATH_SHOP, [StorefrontController::class, 'shop'])
    ->name(RouteConstants::NAME_SHOP_INDEX);

Route::get(RouteConstants::PATH_SHOP_SHOW, [StorefrontController::class, 'show'])
    ->name(RouteConstants::NAME_SHOP_SHOW);

// =============================================================================
// CART ROUTES (Khách vãng lai & Thành viên)
// =============================================================================
Route::prefix(RouteConstants::PREFIX_CART)->name(RouteConstants::NAME_CART_GROUP)->group(function () {
    Route::get(RouteConstants::PATH_CART_INDEX, [CartController::class, 'index'])
        ->name('index');
    Route::post(RouteConstants::PATH_CART_ADD, [CartController::class, 'add'])
        ->name('add');
    Route::put(RouteConstants::PATH_CART_UPDATE, [CartController::class, 'update'])
        ->name('update');
    Route::delete(RouteConstants::PATH_CART_REMOVE, [CartController::class, 'remove'])
        ->name('remove');
    Route::post(RouteConstants::PATH_CART_BULK_REMOVE, [CartController::class, 'bulkRemove'])
        ->name('bulk_remove');
    Route::get(RouteConstants::PATH_CART_COUNT, [CartController::class, 'count'])
        ->name('count');
});

// =============================================================================
// CHECKOUT ROUTES
// =============================================================================
Route::prefix(RouteConstants::PREFIX_CHECKOUT)->name(RouteConstants::NAME_CHECKOUT_GROUP)->group(function () {
    Route::get(RouteConstants::PATH_CHECKOUT_INDEX, [CheckoutController::class, 'index'])
        ->name('index');
    Route::post(RouteConstants::PATH_CHECKOUT_PROCESS, [CheckoutController::class, 'process'])
        ->name('process');
    Route::get(RouteConstants::PATH_CHECKOUT_SUCCESS, [CheckoutController::class, 'success'])
        ->name('success');
});

// =============================================================================
// SHIPPING & GHN API ROUTES (Public for Checkout & Address Book)
// =============================================================================
Route::prefix('api/shipping')->name('shipping.')->group(function () {
    Route::get('/provinces', [ShippingController::class, 'getProvinces'])->name('provinces');
    Route::get('/districts', [ShippingController::class, 'getDistricts'])->name('districts');
    Route::get('/wards', [ShippingController::class, 'getWards'])->name('wards');
    Route::post('/calculate-fee', [ShippingController::class, 'calculateFee'])->name('calculate_fee');
});

// =============================================================================
// AUTH ROUTES — Chỉ dành cho Guest
// =============================================================================
Route::middleware('guest')->group(function () {
    Route::get(RouteConstants::PATH_LOGIN, [AuthController::class, 'showLoginForm'])
        ->name(RouteConstants::NAME_LOGIN);
    Route::post(RouteConstants::PATH_LOGIN, [AuthController::class, 'login']);

    Route::get(RouteConstants::PATH_REGISTER, [AuthController::class, 'showRegisterForm'])
        ->name(RouteConstants::NAME_REGISTER);
    Route::post(RouteConstants::PATH_REGISTER, [AuthController::class, 'register']);
});

// =============================================================================
// EMAIL VERIFICATION ROUTES
// =============================================================================
Route::get(RouteConstants::PATH_VERIFICATION_NOTICE, function () {
    return view('auth.verify-email');
})->middleware('auth')->name(RouteConstants::NAME_VERIFICATION_NOTICE);

Route::get(RouteConstants::PATH_VERIFICATION_VERIFY, function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route(RouteConstants::NAME_HOME)->with('success', 'Email đã được xác thực thành công!');
})->middleware(['auth', 'signed'])->name(RouteConstants::NAME_VERIFICATION_VERIFY);

Route::post(RouteConstants::PATH_VERIFICATION_SEND, function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Link xác thực đã được gửi lại!');
})->middleware(['auth', 'throttle:6,1'])->name(RouteConstants::NAME_VERIFICATION_SEND);

// =============================================================================
// PROTECTED USER ACCOUNT ROUTES — Phải đăng nhập
// =============================================================================
Route::middleware('auth')->group(function () {
    Route::post(RouteConstants::PATH_LOGOUT, [AuthController::class, 'logout'])
        ->name(RouteConstants::NAME_LOGOUT);

    // Sổ địa chỉ (Address Book)
    Route::prefix(RouteConstants::PREFIX_ADDRESSES)->name(RouteConstants::NAME_ADDRESSES_GROUP)->group(function () {
        Route::get(RouteConstants::PATH_ADDRESSES_INDEX, [AddressController::class, 'index'])
            ->name('index');
        Route::post(RouteConstants::PATH_ADDRESSES_STORE, [AddressController::class, 'store'])
            ->name('store');
        Route::put(RouteConstants::PATH_ADDRESSES_UPDATE, [AddressController::class, 'update'])
            ->name('update');
        Route::delete(RouteConstants::PATH_ADDRESSES_DESTROY, [AddressController::class, 'destroy'])
            ->name('destroy');
        Route::post(RouteConstants::PATH_ADDRESSES_SET_DEFAULT, [AddressController::class, 'setDefault'])
            ->name('set_default');
    });

    // Tài khoản & Lịch sử đơn hàng
    Route::prefix(RouteConstants::PREFIX_ACCOUNT)->name(RouteConstants::NAME_ACCOUNT_GROUP)->group(function () {
        Route::get(RouteConstants::PATH_ACCOUNT_ORDERS, [AccountController::class, 'orders'])
            ->name('orders');
        Route::get(RouteConstants::PATH_ACCOUNT_ORDER_SHOW, [AccountController::class, 'orderDetail'])
            ->name('orders.show');
        Route::post('/orders/{order}/cancel', [AccountController::class, 'cancelOrder'])
            ->name('orders.cancel');
        Route::post('/orders/{order}/reorder', [AccountController::class, 'reorder'])
            ->name('orders.reorder');
        Route::post('/orders/{order}/confirm-delivery', [AccountController::class, 'confirmDelivery'])
            ->name('orders.confirm_delivery');
        Route::get(RouteConstants::PATH_ACCOUNT_ADDRESSES, [AccountController::class, 'addresses'])
            ->name('addresses');
    });
});

// =============================================================================
// ADMIN ROUTES — Phải đăng nhập + có role admin
// =============================================================================
Route::middleware(['auth', 'admin'])->prefix(RouteConstants::PREFIX_ADMIN)->name(RouteConstants::NAME_ADMIN_GROUP)->group(function () {
    Route::get(RouteConstants::PATH_ADMIN_DASHBOARD, [AdminController::class, 'dashboard'])
        ->name('dashboard');

    Route::resource(RouteConstants::RESOURCE_PRODUCTS, ProductController::class);
    Route::resource(RouteConstants::RESOURCE_CATEGORIES, CategoryController::class);
    Route::resource(RouteConstants::RESOURCE_BRANDS, BrandController::class);
    Route::resource(RouteConstants::RESOURCE_USERS, UserController::class);

    // Admin Order Management
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
        Route::patch('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('update_status');
        Route::post('/{order}/send-ghn', [AdminOrderController::class, 'sendToGhn'])->name('send_ghn');
        Route::post('/{order}/cancel', [AdminOrderController::class, 'cancelOrder'])->name('cancel');
        Route::get('/{order}/print-label', [AdminOrderController::class, 'printLabel'])->name('print_label');
    });
});

// =============================================================================
// WEBHOOK ROUTES — No CSRF, public endpoint for GHN callbacks
// =============================================================================
Route::post('/webhook/ghn', [WebhookController::class, 'ghnCallback'])
    ->name('webhook.ghn')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
