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
Route::get('/prueba', function() {
    // Product::find(5)->delete();
    // $products = Product::all();
    // $products = Product::withTrashed()->get();
    $ventas = Lot::find(1)->with(['sales.products', 'sales.payment'])->get();
    return response()->json([
        'ventas' => $ventas,
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/validate', [AuthController::class, 'isValidToken']);
});
