<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class SaveProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'brand_id' => 'sometimes',
            'category_id' => 'required|integer|exists:products_categories,id',
            'article' => 'required|string|max:255|unique:products,article',
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
