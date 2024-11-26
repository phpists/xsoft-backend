<?php

namespace App\Http\Requests\Brand;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:brands,id',
            'title' => 'required|string|max:255',
            'description' => 'sometimes',
            'color' => 'sometimes'
        ];
    }
}
