<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules()
    {
        return [
            'id' => 'required|integer|exists:products,id',
            'brand_id' => 'sometimes',
            'category_id' => 'required|integer|exists:categories,id',
            'article' => 'required',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_measure_id' => 'sometimes',
            'color' => 'sometimes',
            'balance' => 'sometimes',
            'cost_price' => 'sometimes',
            'retail_price' => 'sometimes',
            'tags' => 'sometimes',
            'vendors' => 'sometimes',
            'materials_used_quantity' => 'sometimes',
            'materials_used_measure_id' => 'sometimes',
        ];
    }
}
