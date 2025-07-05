<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $perPage = is_numeric($request->perPage) ? $request->perPage :  10;

        $units = Unit::filter()
            ->with(['products'])
            ->orderBy('name', 'asc')
            ->paginate($perPage);
        $units->appends(['search' => $request->search]);


        if (!empty($request->search) && $request->search != '')
            $units->appends(['search' => $request->search]);

        $units->appends(['perPage' => $perPage]);

        $units->getCollection()->transform(function ($unit) {
            return [
                'id' => $unit->id,
                'name' => $unit->name,
                'short_name' => $unit->short_name,
                'status' => $unit->status,
                'items' =>  $unit->products->count(),
                'created_at' => $unit->created_at,
            ];
        });

        return inertia('inventory/pos-units', [
            'units' =>  $units,
            'filters' => $request->only('search', 'page', 'per_page'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate =  $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        $unit = Unit::create([
            'name' => $validate['name'],
            'short_name' =>  $validate['short_name'],
            'status' => $validate['status'] ?? 1,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => "Satuan '{$unit->name}' berhasil ditambahkan.",
                'data' => $unit,
            ], 200);
        }

        return redirect()->back()->with('success', "Satuan '{$unit->name}' berhasil ditambahkan.");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $unit = Unit::findOrFail($id);

        $validate =  $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        $unit->update([
            'name' => $validate['name'],
            'short_name' =>  $validate['short_name'],
            'status' => $validate['status'] ?? 1,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => "Satuan '{$unit->name}' berhasil diperbarui.",
                'data' => $unit,
            ], 200);
        }

        return redirect()->back()->with('success', "Satuan '{$unit->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $unit = Unit::findOrFail($id);

        if ($unit) {
            $unit->delete();
            return redirect()->back()->with('success', "Satuan '{$unit->name}' berhasil dihapus.");
        }

        return redirect()->back()->withErrors([
            'error' => "Satuan tidak ditemukan.",
        ]);
    }
}
