<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CutController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/validate', [AuthController::class, 'isValidToken']);
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::apiResource('user', UserController::class);
        Route::apiResource('type', TypeController::class);
        Route::apiResource('product', ProductController::class)->except(['update']);
        Route::post('/product/{product}', [ProductController::class, 'update'])->name('product.update');
        Route::apiResource('sale', SaleController::class)->except(['destroy', 'update']);
        Route::apiResource('cut', CutController::class)->except(['destroy', 'update']);
        Route::get('/lot', [LotController::class, 'index'])->name('lot.index');
    });
});
