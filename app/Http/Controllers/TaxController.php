<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = is_numeric($request->perPage) ? $request->perPage : 10;

        $taxes = Tax::filter()
            ->orderBy('name', 'asc')
            ->paginate($perPage);

        if (!empty($request->search) && $request->search != '') {
            $taxes->appends(['search' => $request->search]);
        }

        $taxes->appends(['perPage' => $perPage]);

        $taxes->getCollection()->transform(function ($tax) {
            return [
                'id' => $tax->id,
                'name' => $tax->name,
                'percent' => $tax->percent,
                'fixed_amount' => $tax->fixed_amount,
                'status' => $tax->status,
                'created_at' => $tax->created_at,
            ];
        });

        return inertia('tax/tax-list', [
            'taxes' => $taxes,
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
            'percent' => 'nullable|numeric|min:0|max:100|required_without:fixed_amount',
            'fixed_amount' => 'nullable|numeric|min:0|required_without:percent',
            'status' => 'boolean',
        ]);

        $tax = Tax::create($validate);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => "Tarif pajak '{$tax->name}' berhasil ditambahkan.",
                'data' => $tax,
            ], 200);
        }

        return redirect()->back()->with('success', "Tarif pajak '{$tax->name}' berhasil ditambahkan.");
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
        $tax = Tax::find($id);

        $validate = $request->validate([
            'name' => 'required|string|max:255',
            'percent' => 'nullable|numeric|min:0|max:100|required_without:fixed_amount',
            'fixed_amount' => 'nullable|numeric|min:0|required_without:percent',
            'status' => 'required|boolean',
        ]);

        $tax->update($validate);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => "Tarif pajak '{$tax->name}' berhasil diperbarui.",
                'data' => $tax,
            ], 200);
        }

        return redirect()->back()->with('success', "Tarif Pajak '{$tax->name}' berhasil perbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tax = Tax::find($id);

        if ($tax) {
            $tax->delete();
            return redirect()->back()->with('success', "Tarif Pajak '{$tax->name}' berhasil dihapus.");
        }

        return back()->withErrors([
            'error' => 'Tarif Pajak tidak ditemukan.',
        ]);
    }
}
