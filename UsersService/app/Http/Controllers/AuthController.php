<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

use App\Http\Controllers\Controller;

use App\Http\Resources\UserResource;

use App\Http\Requests\Auth\StoreLoginRequest;
use App\Http\Requests\Auth\StoreRegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Jobs\CreateCartJob;
use App\Traits\CookieTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $user;

    use CookieTrait;

    public function __construct()
    {
        //Adicionar utilizador autenticado para uma variavel global no controlador.
    }

    //Função para mostrar dados de perfil de utilizador autenticado.
    public function profile()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is not valid'], 401);
        }

        return response()->json(['user' => new UserResource($user)], 200);
    }

    //Função para atualizar os dados do utilizador que está autenticado.
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $this->profile();
        $data = $user->getData(true);

        try {
            DB::beginTransaction();

            $user = User::where('id', $data['user']['id'])->firstOrFail();

            $user->update($request->all());

            DB::commit();
            return response()->json(['message' => 'Profile updated successfully'], 200);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update profile', 'error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    //Função para iniciar sessão de um utilizador.
    public function login(StoreLoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (Cookie::get('jwt')) {
                return response()->json(['message' => 'User is already authenticated'], 400);
            }

            $token = Auth::attempt($credentials);

            if (!$token) {
                return response()->json(['message' => 'Email or password are wrong'], 401);
            }

            $cookie = Cookie::make("token", $token, 60 * 2,  '/', null, false, true);

            return response()->json([
                'status' => 'success',
                "message" => "Logged in successfully",
            ])->withCookie($cookie);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Failed to login user', 'error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    //Função para registar um novo utilizador.
    public function register(StoreRegisterRequest $request)
    {
        try {
            $user = User::create($request->all());
        } catch (QueryException $e) {
            Log::info('Failed to register user' . $e);
            return response()->json(['message' => 'Failed to register user', 'error' => $e->getMessage()], 500);
        }

        CreateCartJob::dispatch($user, $this->getCookie());

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    //Função para finalizar sessão de utilizador.
    public function logout()
    {
        try {
            auth()->logout(true);
            $cookie = Cookie::forget("token");

            return response()->json([
                'message' => 'Logged out successfully'
            ], 200)->withCookie($cookie);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Failed to logout user', 'error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
}
