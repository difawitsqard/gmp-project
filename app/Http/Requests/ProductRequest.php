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
            'product_categories_id' => 'required|exists:product_categories,id',
            'unit_id' => 'required|exists:units,id',
            'price' => 'required|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
        ];

        $rules['images'] = 'nullable|array|max:3';
        $rules['images.*'] = ['nullable', function ($attribute, $value, $fail) {
            // Validasi file upload atau string base64
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                $validator = \Illuminate\Support\Facades\Validator::make(
                    [$attribute => $value],
                    [$attribute => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048']
                );
                if ($validator->fails()) {
                    $fail($validator->errors()->first($attribute));
                }
            } elseif (is_string($value)) {
                // Jika ini adalah string base64, validasi
                if (!preg_match('/^data:image\/(\w+);base64,/', $value)) {
                    $fail('Gambar harus dalam format base64 yang valid.');
                }
            } else {
                $fail('Format gambar tidak valid.');
            }
        }];

        if ($this->isMethod('post')) {
            // Aturan validasi untuk create
            $rules['sku'] = 'required|string|max:100|unique:products,sku';
            $rules['qty'] = 'required|numeric|min:1';
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            // Aturan validasi untuk update
            $rules['delete_image'] = 'nullable|array';
            $rules['delete_image.*'] = 'nullable|integer|exists:product_images,id';
        }

        return  $rules;
    }
}
