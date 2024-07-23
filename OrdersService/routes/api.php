<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;

use App\Http\Controllers\OrderController;
use App\Http\Middleware\Authentication;

use App\Mail\ConfirmOrderMail;
use Illuminate\Support\Facades\Mail;

use App\Models\Order;

Route::get('orders', [OrderController::class, 'index'])->middleware(Authentication::class);

Route::get('orders/{order}', [OrderController::class, 'show'])->middleware(Authentication::class);

Route::post('test', [OrderController::class, 'testDetails'])->middleware(Authentication::class);

Route::post('orders', [OrderController::class, 'store'])->middleware(Authentication::class);

Route::get('test/mail', function () {
    $order = Order::where('id', 1)->firstOrFail();

    Mail::to('goncalosilva2008@gmail.com')->send(new ConfirmOrderMail($order));
});
