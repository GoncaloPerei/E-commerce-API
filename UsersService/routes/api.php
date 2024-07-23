j<?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\RoleController;
    use App\Http\Controllers\CardController;
    use App\Http\Controllers\UserController;
    use App\Http\Middleware\EnsurePermissionIsValid;

    Route::get('test', [AuthController::class, 'authenticate']);

    //Endpoint para iniciar sessão users
    Route::post('/login', [AuthController::class, 'login']);

    //Endpoint para registar users
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:api')
        ->group(function () {
            //Endpoint para finalizar sessão users
            Route::post('/logout', [AuthController::class, 'logout']);

            //Endpoint para ver perfil de user autenticado
            Route::get('/profile', [AuthController::class, 'profile']);

            //Endpoint para atualizar perfil de user autenticado
            Route::patch('/profile', [AuthController::class, 'updateProfile']);

            Route::get('/user/card/{userCard}', [CardController::class, 'show']);

            Route::post('/user/card', [CardController::class, 'store']);

            Route::patch('/user/card/{userCard}', [CardController::class, 'update']);

            Route::delete('/user/card/{userCard}', [CardController::class, 'destroy']);
        });


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
