<?php

use App\Http\Controllers\AuthController;
use App\Models\Cut;
use App\Models\Lot;
use App\Models\CutDetail;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth')->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/validate', [AuthController::class, 'isValidToken']);
    Route::middleware('admin')->group(function () {
        Route::get('/prueba', function() {
            return response()->json([
                'message' => 'Admin'
            ]);
        });
    });
});
