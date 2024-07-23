<?php

namespace App\Http\Controllers;

use App\Models\Payment;

use Illuminate\Database\QueryException;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Http\Resources\PaymentResource;

use App\Jobs\ProcessPaymentJob;

use App\Traits\CookieTrait;
use App\Traits\RequestsTrait;

class PaymentController extends Controller
{
    use CookieTrait;
    use RequestsTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        //Decoding authenticated user
        try {
            $user = $this->getUser($this->getCookie());
        } catch (\Exception $e) {
            Log::error('An error ocurred when getting user... ' . $e);
            return response()->json(['message' => 'An error ocurred when getting user', 'error' => $e->getMessage()], 500);
        }

        $paymentData = [
            'order_id' => $request['order']['id'],
        ];

        if ($request['paymentMethod'] !== 0) {
            $paymentData['payment_type_id'] = 2;
            $paymentData['card_id'] = $request['paymentMethod'];
        }

        try {
            $payment = Payment::create($paymentData);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Failed to create payment...' . $e);
            return response()->json(['message' => 'Failed to create payment', 'error' => $e->getMessage()], 500);
        }

        ProcessPaymentJob::dispatch($payment->id, $this->getCookie());

        DB::commit();
        Log::info('Payment created successfully...');
        return response()->json(['message' => 'Payment created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($order)
    {
        //Decoding authenticated user
        try {
            $user = $this->getUser($this->getCookie());
        } catch (\Exception $e) {
            Log::error('An error ocurred when getting user... ' . $e);
            return response()->json(['message' => 'An error ocurred when getting user', 'error' => $e->getMessage()], 500);
        }

        $payment = Payment::where('order_id', $order)
            ->with('status', 'type')
            ->firstOrFail();

        return new PaymentResource($payment);
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
