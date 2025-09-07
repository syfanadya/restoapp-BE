<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ApiFoodController;
use App\Http\Controllers\ApiOrderController;
use App\Http\Controllers\ApiTableController;
use App\Http\Controllers\ApiOrderItemController;
use App\Http\Controllers\ApiPaymentController;

Route::post('login', [ApiAuthController::class, 'login']);
Route::post('logout', [ApiAuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('user', [ApiAuthController::class, 'fetch'])->middleware('auth:sanctum');

Route::get('food', [ApiFoodController::class, 'fetch'])->middleware('auth:sanctum');
Route::post('food', [ApiFoodController::class, 'create'])->middleware('auth:sanctum');
Route::put('food/{id}', [ApiFoodController::class, 'update'])->middleware('auth:sanctum');
Route::delete('food/{id}', [ApiFoodController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('table', [ApiTableController::class, 'fetch'])->middleware('auth:sanctum');
Route::put('table/{id}', [ApiTableController::class, 'update'])->middleware('auth:sanctum');

Route::get('order', [ApiOrderController::class, 'fetch'])->middleware('auth:sanctum');
Route::post('order', [ApiOrderController::class, 'create'])->middleware('auth:sanctum');
Route::put('order/{id}', [ApiOrderController::class, 'update'])->middleware('auth:sanctum');
Route::delete('order/{id}', [ApiOrderController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('orderitem', [ApiOrderItemController::class, 'fetch'])->middleware('auth:sanctum');
Route::post('orderitem', [ApiOrderItemController::class, 'create'])->middleware('auth:sanctum');
Route::put('orderitem/{id}', [ApiOrderItemController::class, 'update'])->middleware('auth:sanctum');
Route::delete('orderitem/{id}', [ApiOrderItemController::class, 'destroy'])->middleware('auth:sanctum');

Route::post('payment', [ApiPaymentController::class, 'create'])->middleware('auth:sanctum');
