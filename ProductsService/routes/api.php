<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductStatusController;
use App\Http\Middleware\Authentication;
use App\Http\Middleware\VerifyUserMiddleware;

//Endpoint para ver todos os produtos
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{product}', [ProductController::class, 'show']);

Route::patch('products/{product}', [ProductController::class, 'update']);

Route::prefix('administrator')
    ->middleware(Authentication::class, VerifyUserMiddleware::class)
    ->group(function () {
        //Routes para CRUD de produtos
        //Endpoint para ver todos os produtos
        Route::get('products', [ProductController::class, 'index']);
        //Endpoint para ver um produto específico
        Route::get('products/{product}', [ProductController::class, 'show']);
        //Endpoint para criar um novo produto
        Route::post('products', [ProductController::class, 'store']);
        //Endpoint para atualizar um produto
        Route::patch('products/{product}', [ProductController::class, 'update']);
        //Endpoint para deletar um produto
        Route::delete('products/{product}', [ProductController::class, 'destroy']);
        Route::post('products/{product}/restore', [ProductController::class, 'restore']);
    });

//Endpoint para ver todas as categorias
Route::get('categories', [ProductCategoryController::class, 'index']);
Route::get('categories/{productCategory}', [ProductCategoryController::class, 'show']);

Route::prefix('administrator')
    ->middleware(Authentication::class, VerifyUserMiddleware::class)
    ->group(function () {
        //Routes para CRUD de produtos
        //Endpoint para ver todos os produtos
        Route::get('categories', [ProductCategoryController::class, 'index']);
        //Endpoint para ver um produto específico
        Route::get('categories/{productCategory}', [ProductCategoryController::class, 'show']);
        //Endpoint para criar um novo produto
        Route::post('categories', [ProductCategoryController::class, 'store']);
        //Endpoint para atualizar um produto
        Route::patch('categories/{productCategory}', [ProductCategoryController::class, 'update']);
        //Endpoint para deletar um produto
        Route::delete('categories/{productCategory}', [ProductCategoryController::class, 'destroy']);
        //Endpoint para deletar um produto
        Route::delete('categories/{productCategory}', [ProductCategoryController::class, 'destroy']);
        Route::post('/categories/{productCategory}/restore', [ProductCategoryController::class, 'restore']);
    });

//Endpoint para ver todas os estados
Route::get('administrador/status', [ProductStatusController::class, 'index'])->middleware(Authentication::class, VerifyUserMiddleware::class);
