<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\MerchantController;


Route::prefix('merchants')->group(function () {
    Route::get('/', [MerchantController::class, 'index']);
    Route::post('/', [MerchantController::class, 'store']);
    Route::get('/{merchant}', [MerchantController::class, 'show']);
    Route::put('/{merchant}', [MerchantController::class, 'update']);
    Route::delete('/{merchant}', [MerchantController::class, 'destroy']);
});
