<?php

use App\Http\Controllers\PaymentController;
use App\Http\Middleware\Authentication;
use Illuminate\Support\Facades\Route;


Route::get('payments', [PaymentController::class, 'index'])->middleware(Authentication::class);

Route::post('payments', [PaymentController::class, 'store'])->middleware(Authentication::class);
