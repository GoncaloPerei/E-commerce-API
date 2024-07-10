<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OrderController;
use App\Http\Middleware\Authentication;

Route::get('orders', [OrderController::class, 'index'])->middleware(Authentication::class);

Route::get('orders/{order}', [OrderController::class, 'show'])->middleware(Authentication::class);

Route::post('orders', [OrderController::class, 'store'])->middleware(Authentication::class);
