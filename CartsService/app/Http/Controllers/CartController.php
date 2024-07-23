<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;

use Illuminate\Http\Request;

use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Http\Resources\CartResource;

use App\Http\Requests\StoreCartItemRequest;
use App\Traits\CookieTrait;
use App\Traits\RequestsTrait;

use Spatie\QueryBuilder\QueryBuilder;

class CartController extends Controller
{
    use RequestsTrait;
    use CookieTrait;

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
        try {
            $cart = Cart::where('user_id', $request->id)->first();
        } catch (QueryException $e) {
            Log::error('Failed to search cart... ' . $e->getMessage());
        }

        try {
            if (empty($cart)) {
                DB::beginTransaction();

                Cart::create([
                    'user_id' => $request->id,
                ]);

                DB::commit();

                return response()->json(['message' => 'Cart created successfully'], 201);
            }
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to create cart... ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create cart'], 500);
        }
        return response()->json(['message' => 'User already has a cart'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        try {
            $user = $this->getUser($this->getCookie());
        } catch (\Exception $e) {
            Log::error('An error ocurred when decoding header... ' . $e);
            return response()->json(['message' => 'An error ocurred when decoding header', 'error' => $e->getMessage()], 500);
        }

        try {
            $data = QueryBuilder::for(Cart::class)
                ->where('user_id', $user['id'])
                ->withCount('cartItem')
                ->first();
        } catch (QueryException $e) {
            Log::error('An error ocurred when trying to find cart... ' . $e);
            return response()->json(['message' => 'An error ocurred when trying to find cart']);
        }

        if (empty($data)) {
            return response()->json(['message' => 'Cart was not found']);
        }

        foreach ($data->cartItem as $item) {
            try {
                $productData = Http::get('http://localhost:8001/api/products/' . $item->product_id)->json();

                $item->product = $productData['data'];

                $item->productTotal = ($item->product['price'] * $item->quantity);

                $data->cartPrice += $item->productTotal;
            } catch (\Exception $e) {
                Log::info('Error when show cart items... ' . $e->getMessage());
            }
        }

        return new CartResource($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCartItemRequest $request, $itemId)
    {
        $data = $this->show($request);

        try {
            $product = $this->getProduct($itemId);
        } catch (\Exception $e) {
            Log::error('An error ocurred when getting product... ' . $e->getMessage());
            return response()->json(['message' => 'An error ocurred when getting product', 'error' => $e->getMessage()]);
        }

        if ($product['stock'] === 0) {
            Log::error('Product does not have stock...');
            return response()->json(['message' => 'An error ocurred when getting product', 'error' => 'Product does not have any stock']);
        }

        try {
            DB::beginTransaction();
            
            CartItem::updateOrCreate(
                ['product_id' => $itemId],
                [
                    'product_id' => $itemId,
                    'cart_id' => $data->id,
                    'quantity' => $request->quantity,
                ]
            );

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to add product... ' . $e->getMessage());
            return response()->json(['message' => 'Failed to add product', 'error' => $e->getMessage()]);
        }

        return response()->json(['message' => 'Product added to cart successfully'], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CartItem $itemId)
    {
        try {
            DB::beginTransaction();

            $itemId->delete();

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to remove cart item... ' . $e);
            return response()->json(['message' => 'Failed to remove cart item', 'error' => $e->getMessage()]);
        }

        return response()->json(['message' => 'Item removed from cart successfully'], 201);
    }

    public function cleanCart(Cart $cart)
    {
        try {
            $items = CartItem::where('cart_id', $cart->id)->get();
        } catch (QueryException $e) {
            Log::error('Failed to get cart items... ' . $e->getMessage());
            return response()->json(['message' => 'Failed to get cart items']);
        }

        try {
            foreach ($items as $item) {
                try {
                    DB::beginTransaction();

                    $item->delete();

                    DB::commit();
                } catch (QueryException $e) {
                    DB::rollBack();
                    Log::error('Failed to remove item... ' . $e->getMessage());
                    return response()->json(['message' => 'Failed to remove item']);
                }
            }
        } catch (QueryException $e) {
            Log::error('Failed to clean cart... ' . $e->getMessage());
            return response()->json(['message' => 'Failed to clean cart']);
        }

        return response()->json(['message' => 'Cart cleaned successfully'], 201);
    }
}
