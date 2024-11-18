<?php

namespace App\Http\Requests\ProductCategory;

use Illuminate\Foundation\Http\FormRequest;

class ProductCategoryDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:categories,id',
        ];
    }
}
