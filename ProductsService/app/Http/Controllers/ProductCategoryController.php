<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\ProductCategoryResource;

use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;

use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $data = QueryBuilder::for(ProductCategory::class)
                ->withTrashed()
                ->allowedIncludes([
                    'product'
                ])
                ->allowedFilters([
                    'name',
                    AllowedFilter::trashed(),
                ]);

            return ProductCategoryResource::collection($data->paginate((int) $request->paginate));
        } catch (QueryException $e) {
            return response()->json(['message' => 'Failed to show categories', 'error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductCategoryRequest $request)
    {
        try {
            DB::beginTransaction();

            ProductCategory::create($request->all());

            DB::commit();
            return response()->json(['message' => 'Category created successfully'], 201);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Failed to create category', 'error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory)
    {
        $data = QueryBuilder::for(ProductCategory::class)
            ->where('id', $productCategory->id)
            ->allowedIncludes([
                'product'
            ])
            ->allowedFilters([
                'name',
                AllowedFilter::trashed(),
            ])
            ->first();

        return new ProductCategoryResource($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory)
    {
        try {
            DB::beginTransaction();

            $productCategory->update($request->all());

            DB::commit();
            return response()->json(['message' => 'Category updated successfully'], 200);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update Category', 'error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory)
    {
        try {
            DB::beginTransaction();

            $productCategory->delete();

            DB::commit();
            return response()->json(['message' => 'Category deleted successfully'], 200);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to delete category', 'error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function restore($productCategory)
    {
        try {
            DB::beginTransaction();

            $data = ProductCategory::onlyTrashed()->findOrFail($productCategory);
            $data->restore();

            DB::commit();
            return response()->json(['message' => 'Category restored successfully'], 200);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to restore category', 'error' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
}
