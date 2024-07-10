<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Middleware\Authentication;
use Illuminate\Support\Facades\Route;

Route::get('cart', [CartController::class, 'show'])->middleware(Authentication::class);

Route::post('/cart', [CartController::class, 'store']);

Route::delete('/cart/{cart}', [CartController::class, 'cleanCart'])->middleware(Authentication::class);

Route::patch('/cart/{itemId}', [CartController::class, 'update'])->middleware(Authentication::class);

Route::delete('/item/{itemId}', [CartController::class, 'destroy'])->middleware(Authentication::class);

Route::patch('item/{itemId}', [CartItemController::class, 'update'])->middleware(Authentication::class);
