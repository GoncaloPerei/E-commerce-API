<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\PaymentResource;

use App\Jobs\ProcessPaymentJob;
use App\Traits\CookieTrait;
use App\Traits\DecodeAuthHeaderTrait;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    use DecodeAuthHeaderTrait;
    use CookieTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $this->decodeHeader($request);

        try {
            $data = Payment::where('user_id', $user['id'])
                ->latest('created_at')
                ->paginate(6);

            return PaymentResource::collection($data);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Error']);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = $this->decodeHeader($request);
        } catch (\Exception $e) {
            Log::error('An error ocurred when decoding header... ' . $e);
            return response()->json(['message' => 'An error ocurred when decoding header', 'error' => $e->getMessage()], 500);
        }

        try {
            $response = Http::withCookies(['token' => $this->getCookie()], 'localhost')->get('http://localhost:8002/api/cart')->json();
        } catch (\Exception $e) {
            Log::error('An error ocurred when sending a request... ' . $e);
            return response()->json(['message' => 'An error ocurred when sending a request', 'error' => $e->getMessage()], 500);
        }

        if (empty($response)) {
            Log::error('Cart was not found...');
            return response()->json(['message' => 'Cart was not found']);
        }

        $cart = $response['data'];

        if (empty($cart['cartItem'])) {
            Log::error("Cart doesn't have any items...");
            return response()->json(['message' => "Cart doesn't have any items"]);
        }

        try {
            DB::beginTransaction();

            $payment = Payment::create([
                'cart' => json_encode($cart),
                'user_id' => $user['id'],
            ]);

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to create payment...');
            return response()->json(['message' => 'Failed to create payment', 'error' => $e->getMessage()], 500);
        }

        ProcessPaymentJob::dispatch($payment, $cart, $user, $this->getCookie());

        Log::info('Payment created successfully...');
        return response()->json(['message' => 'Payment created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
}
