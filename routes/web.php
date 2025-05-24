<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ForecastController;
use App\Http\Controllers\NixtlaTestController;
use App\Http\Controllers\ProductCategoryController;

Route::get('/', function () {
    if (auth()->check()) {
        return Inertia::render('dashboard/admin-dashboard', [
            'canLogin' => Route::has('login'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }

    return redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        // check user login
        $user = auth()->user();

        return Inertia::render('dashboard/admin-dashboard', [
            'user' => $user,
        ]);
    })->name('dashboard');

    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::resource('products', ProductController::class)
        ->only(['index', 'create', 'store', 'show', 'update', 'destroy']);

    Route::resource('product-categories', ProductCategoryController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::resource('taxes', TaxController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::resource('units', UnitController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::post('orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::resource('orders', OrderController::class)
        ->only(['index', 'create', 'store', 'show', 'update']);

    Route::post('forecasting/request', [ForecastController::class, 'requestForecast'])->name('forecasting.request');
    Route::resource('forecasting', ForecastController::class)
        ->only(['index', 'create', 'store', 'show', 'update', 'destroy']);

    Route::post('/payments/{order}/retry', [PaymentController::class, 'retryPayment'])->name('payments.retry');
});

// nixtla test
Route::get('/nixtla-test', [NixtlaTestController::class, 'forecast'])
    ->name('nixtla.test.forecast');

Route::post('/midtrans/webhook', [PaymentController::class, 'midtransCallback'])
    ->name('midtrans.webhook');
