<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;

use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\Log;

use App\Http\Requests\UpdateCartItemRequest;

class CartItemController extends Controller
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CartItem $cartItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCartItemRequest $request, CartItem $itemId)
    {
        try {
            $itemId->update($request->all());
        } catch (\Exception $e) {
            Log::error('An error ocurred when updating item... ' . $e);
            return response()->json(['message' => 'An error ocurred when updating item', 'error' => $e->getMessage()]);
        }

        return response()->json(['message' => 'Item updated successfully'], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CartItem $cartItem)
    {
        //
    }
}
