<?php

use App\Http\Controllers\AuthController;
use App\Models\Cut;
use App\Models\Lot;
use App\Models\CutDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/prueba', function() {
    // $lot = Lot::create([
    //     'date' => now(),
    //     'start_time' => now(),
    //     'end_time' => now(),
    //     'product_count' => 0,
    //     'total_amount' => 0.0,
    // ]);
    // $cut = $lot->cuts()->create([
    //     'type' => 'x',
    //     'date' => now(),
    //     'time' => now(),
    //     'product_count' => 0,
    // ]);
    // $cut->cutDetails()->create([
    //     'cash' => 1.0,
    //     'card' => 1.0,
    //     'cash_total' => 1.0,
    //     'card_total' => 1.0,
    //     'total' => 1.0,
    //     'cash_difference' => 1.0,
    //     'card_difference' => 1.0,
    //     'total_difference' => 1.0,
    // ]);
    // return response()->json([
    //     'cut' => $cut,
    //     ]);
    $cuts = Cut::with('cutDetails')->get();
    return response()->json($cuts);
});

Route::middleware('auth')->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/validate', [AuthController::class, 'isValidToken']);
});
