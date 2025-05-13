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
        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        $category = ProductCategory::create(
            [
                'name' => $validate['name'],
                'description' => $validate['description'],
                'status' => $validate['status'] ?? 1,
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => "Kategori '{$category->name}' berhasil ditambahkan.",
                'data' => $category,
            ], 200);
        }

        return redirect()->back()->with('success', "Kategori '{$category->name}' berhasil ditambahkan.");
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
        $category = ProductCategory::find($id);

        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        $category->update(
            [
                'name' => $validate['name'],
                'description' => $validate['description'],
                'status' => $validate['status'] ?? 1,
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => "Kategori '{$category->name}' berhasil diperbarui.",
                'data' => $category,
            ], 200);
        }

        return redirect()->back()->with('success', "Kategori '{$category->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category =  ProductCategory::find($id);
        if ($category) {
            $category->delete();
            return redirect()->back()->with('success', "Kategori {$category->name} berhasil dihapus");
        }
        return back()->withErrors([
            'error' => 'Product not found'
        ]);
    }
}
