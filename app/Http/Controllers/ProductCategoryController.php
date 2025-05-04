<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $perPage = is_numeric($request->perPage) ? $request->perPage :  10;

        $productCategories = ProductCategory::filter()
            ->with(['products'])
            ->orderBy('name', 'asc')
            ->paginate($perPage);
        $productCategories->appends(['search' => $request->search]);


        if (!empty($request->search) && $request->search != '')
            $productCategories->appends(['search' => $request->search]);

        $productCategories->appends(['perPage' => $perPage]);

        $productCategories->getCollection()->transform(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'status' => $category->status,
                'items' => $category->products->count(),
                'created_at' => $category->created_at,

            ];
        });

        return inertia('inventory/category-list', [
            'productCategories' => $productCategories,
            'filters' => $request->only('search', 'page', 'per_page'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $category = ProductCategory::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'category' => $category,
            'message' => 'Category created successfully',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
