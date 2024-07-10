<?php

namespace App\Http\Controllers;

use App\Models\ProductStatus;
use Illuminate\Database\QueryException;

use App\Http\Resources\ProductStatusResource;

class ProductStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = ProductStatus::get();

            return ProductStatusResource::collection($data);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Failed to show products', 'error' => $e->getMessage()], 500);
        }
    }
}
