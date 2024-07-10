<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

use App\Http\Resources\ProductResource;
use App\Traits\FakeStoreApiTrait;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class ProductController extends Controller
{
    use FakeStoreApiTrait;

    public function test()
    {
        return $this->fetchProductsFromApi();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = QueryBuilder::for(Product::class)
                ->withTrashed()
                ->with(['category', 'status'])
                ->allowedFilters([
                    'title',
                    'product_category_id',
                    'product_status_id',
                    AllowedFilter::trashed(),
                    AllowedFilter::scope('price_range'),
                ])
                ->allowedSorts([
                    'price',
                    '-price',
                    'title',
                    '-title',
                ])
                ->latest('id');

            return ProductResource::collection($data->paginate((int) $request->paginate));
        } catch (QueryException $e) {
            return response()->json(['message' => 'Failed to show products', 'error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            DB::beginTransaction();

            Product::create($request->all());

            DB::commit();
            return response()->json(['message' => 'Product created successfully'], 201);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create product', 'error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json(['data' => new ProductResource($product)], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            DB::beginTransaction();

            $product->update($request->all());

            DB::commit();
            return response()->json(['message' => 'Product updated successfully'], 201);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update product', 'error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            $product->delete();

            DB::commit();
            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update product', 'error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function restore($product)
    {
        try {
            DB::beginTransaction();

            $data = Product::onlyTrashed()->findOrFail($product);
            $data->restore();

            DB::commit();
            return response()->json(['message' => 'Product restored successfully'], 200);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to restore product', 'error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
}
