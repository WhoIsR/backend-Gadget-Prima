<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;

Route::apiResource('products', ProductController::class);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/products/{id}/history', [App\Http\Controllers\Api\ProductController::class, 'history']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
