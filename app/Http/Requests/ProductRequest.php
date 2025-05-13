<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules =  [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku',
            'product_categories_id' => 'required|exists:product_categories,id',
            'unit_id' => 'required|exists:units,id',
            'price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
        ];

        if ($this->isMethod('post')) {
            // Aturan validasi untuk create
            $rules['image'] = 'nullable|array|max:3';
            $rules['image.*'] = 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:1024';
            $rules['qty'] = 'required|numeric|min:1';
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            // Aturan validasi untuk update
            $rules['image'] = 'nullable|array|max:3';
            $rules['image.*'] = 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:1024';
            $rules['delete_image.*'] = 'nullable|exists:stock_items,id';
        }

        return  $rules;
    }
}
