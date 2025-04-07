<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\OfferController;
use App\Http\Controllers\API\MerchantController;
use App\Http\Controllers\API\VoucherCodeController;

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

    Route::prefix('offers')->group(function () {
        Route::get('/', [OfferController::class, 'index']);
        Route::post('/', [OfferController::class, 'store']);
        Route::get('/{offer}', [OfferController::class, 'show']);
        Route::put('/{offer}', [OfferController::class, 'update']);
        Route::delete('/{offer}', [OfferController::class, 'destroy']);
    });

    Route::prefix('voucher-codes')->group(function () {
        Route::get('/', [VoucherCodeController::class, 'index']);
        Route::post('/', [VoucherCodeController::class, 'store']);
        Route::get('/{voucher_code}', [VoucherCodeController::class, 'show']);
        Route::put('/{voucher_code}', [VoucherCodeController::class, 'update']);
        Route::delete('/{voucher_code}', [VoucherCodeController::class, 'destroy']);
        Route::get('/offer/{offer}', [VoucherCodeController::class, 'getByOffer']);
    });
});

Route::prefix('merchants')->group(function () {
    Route::get('/', [MerchantController::class, 'index']);
    Route::post('/', [MerchantController::class, 'store']);
    Route::get('/{merchant}', [MerchantController::class, 'show']);
    Route::put('/{merchant}', [MerchantController::class, 'update']);
    Route::delete('/{merchant}', [MerchantController::class, 'destroy']);
});
