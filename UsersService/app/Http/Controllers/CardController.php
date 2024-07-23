<?php

namespace App\Http\Controllers;

use App\Models\Card;

use App\Http\Requests\Card\StoreCardRequest;
use App\Http\Requests\Card\UpdateCardRequest;
use App\Http\Resources\CardResource;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCardRequest $request)
    {
        DB::beginTransaction();

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response()->json(['message' => 'An error ocurred when getting auth user', 'error' => $e->getMessage()]);
        }

        try {
            $data = $request->all();

            if (strpos($request['card_number'], 5) === 0) {
                $data['card_type_id'] = 1;
            } else if (strpos($request['card_number'], 4) === 0) {
                $data['card_type_id'] = 2;
            } else {
                return response()->json(['message' => 'Card invalid or not supported']);
            }

            $data['user_id'] = $user->id;
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error ocurred when merging data', 'error' => $e->getMessage()]);
        }

        try {
            Card::create($data);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create debit or credit card', 'error' => $e->getMessage()], 500);
        }

        DB::commit();
        return response()->json(['message' => 'Debit or credit card created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Card $userCard)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response()->json(['message' => 'An error ocurred when getting auth user', 'error' => $e->getMessage()]);
        }

        if ($userCard->user_id !== $user['id']) {
            return response()->json(['message' => 'An error ocurred when getting card', 'error' => 'Request unauthorized'], 500);
        }

        return new CardResource($userCard);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCardRequest $request, Card $userCard)
    {
        DB::beginTransaction();

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response()->json(['message' => 'An error ocurred when getting auth user', 'error' => $e->getMessage()]);
        }

        if ($userCard->user_id !== $user['id']) {
            return response()->json(['message' => 'An error ocurred when getting card', 'error' => 'Request unauthorized'], 500);
        }

        try {
            $userCard->update($request->all());
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create debit or credit card', 'error' => $e->getMessage()], 500);
        }

        DB::commit();
        return response()->json(['message' => 'Debit or credit card updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Card $userCard)
    {
        DB::beginTransaction();

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response()->json(['message' => 'An error ocurred when getting auth user', 'error' => $e->getMessage()]);
        }

        if ($userCard->user_id !== $user['id']) {
            return response()->json(['message' => 'An error ocurred when getting card', 'error' => 'Request unauthorized'], 500);
        }

        try {
            $userCard->delete();
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create debit or credit card', 'error' => $e->getMessage()], 500);
        }

        DB::commit();
        return response()->json(['message' => 'Debit or credit card deleted successfully'], 200);
    }
}
