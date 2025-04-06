<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MerchantController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/verify-token', [AuthController::class, 'verifyToken']);


    Route::prefix('merchants')->group(function () {
        Route::get('/', [MerchantController::class, 'index']);
        Route::post('/', [MerchantController::class, 'store']);
        Route::get('/{merchant}', [MerchantController::class, 'show']);
        Route::put('/{merchant}', [MerchantController::class, 'update']);
        Route::delete('/{merchant}', [MerchantController::class, 'destroy']);
    });
});
