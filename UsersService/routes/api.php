<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsurePermissionIsValid;
use App\Jobs\CreateCartJob;

Route::get('test', [AuthController::class, 'authenticate']);

//Endpoint para ver perfil de user autenticado
Route::get('/profile', [AuthController::class, 'profile'])
    ->middleware('auth:api');

//Endpoint para atualizar perfil de user autenticado
Route::patch('/profile', [AuthController::class, 'updateProfile'])
    ->middleware('auth:api');

//Endpoint para iniciar sessão users
Route::post('/login', [AuthController::class, 'login'])
    ->name('login');

//Endpoint para finalizar sessão users
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

//Endpoint para registar users
Route::post('/register', [AuthController::class, 'register'])
    ->name('register');

//Routes de administrador
Route::prefix('administrator')
    ->middleware('auth:api', EnsurePermissionIsValid::class)
    ->group(function () {
        //Routes para CRUD de utilizadores
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::post('/users', [UserController::class, 'store']);
        Route::patch('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        Route::post('/users/{user}/restore', [UserController::class, 'restore']);
        //Routes para CRUD de roles
        Route::get('/roles', [RoleController::class, 'index']);
    });
