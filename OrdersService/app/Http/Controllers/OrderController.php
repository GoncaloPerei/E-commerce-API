<?php

namespace App\Http\Controllers;

use App\Models\Order;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\OrderResource;

use App\Http\Requests\StoreOrderRequest;
use App\Jobs\ProcessOrderJob;
use App\Traits\RequestsTrait;
use App\Traits\CookieTrait;
use App\Traits\CRUDDetailsTrait;
use App\Traits\DecodeHeaderTrait;

class OrderController extends Controller
{
    use RequestsTrait;
    use CookieTrait;
    use CRUDDetailsTrait;
    use DecodeHeaderTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        //Decoding authenticated user
        try {
            $user = $this->decodeHeader($request);
        } catch (\Exception $e) {
            Log::error('An error ocurred when decoding header... ' . $e);
            return response()->json(['message' => 'An error ocurred when decoding header', 'error' => $e->getMessage()], 500);
        }

        try {
            $data = Order::latest('id')
                ->where('user_id', $user['id'])
                ->with(['details']);

            return OrderResource::collection($data->paginate((int) $request->paginate));
        } catch (QueryException $e) {
            Log::error('Error when getting orders... ' . $e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        DB::beginTransaction();

        //Decoding authenticated user
        try {
            $user = $this->decodeHeader($request);
        } catch (\Exception $e) {
            Log::error('An error ocurred when decoding header... ' . $e);
            return response()->json(['message' => 'An error ocurred when decoding header', 'error' => $e->getMessage()], 500);
        }

        //Getting cart from cart micro
        try {
            $cart = $this->getCart($this->getCookie());
        } catch (\Exception $e) {
            Log::error('An error ocurred when getting cart... ' . $e);
            return response()->json(['message' => 'An error ocurred when getting cart', 'error' => $e->getMessage()], 500);
        }

        //Create details of order
        try {
            $details = $this->storeDetails($request->all());
            Log::info('Order details created successfully... ' . $details);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('An error ocurred when creating details... ' . $e);
            return response()->json(['message' => 'An error ocurred when creating details', 'error' => $e->getMessage()], 500);
        }

        //Create order
        try {
            $order = Order::create([
                'cart' => json_encode($cart),
                'user_id' => $user['id'],
                'order_details_id' => $details['id'],
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error creating order... ' . $e);
            return response()->json(['message' => 'Error creating order', 'error' => $e->getMessage()], 500);
        }

        ProcessOrderJob::dispatch($order, $cart, $this->getCookie());

        DB::commit();
        Log::info('Order created successfully...');
        return response()->json(['message' => 'Order created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Order $order)
    {
        //Decoding authenticated user
        try {
            $user = $this->decodeHeader($request);
        } catch (\Exception $e) {
            Log::error('An error ocurred when decoding header... ' . $e);
            return response()->json(['message' => 'An error ocurred when decoding header', 'error' => $e->getMessage()], 500);
        }

        //Check if selected order belongs to authenticated user
        if ($order->user_id !== $user['id']) {
            return response()->json(['message' => 'An error ocurred when getting order', 'error' => 'Request unauthorized'], 500);
        }

        try {
            $data = Order::where('user_id', $order->id)
                ->firstOrFail();

            return new OrderResource($data);
        } catch (QueryException $e) {
            return response()->json(['message' => 'An error ocurred when getting order', 'error' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
