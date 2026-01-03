<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ExpenseController;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/test-produk', [ProductController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {


    Route::get('/user', function (Request $request) {
        return $request->user();
    });


    Route::apiResource('products', ProductController::class);
    Route::get('/products/{id}/history', [ProductController::class, 'history']);

    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::apiResource('users', UserController::class);
    Route::apiResource('expenses', ExpenseController::class);

    Route::apiResource('categories', App\Http\Controllers\Api\CategoryController::class);
    Route::apiResource('brands', App\Http\Controllers\Api\BrandController::class);
});
