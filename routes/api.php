<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

// Route::post('/register', [AuthController::class, 'register']);
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
    });
});
