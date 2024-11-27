<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:users,id',
            'category_id' => 'required|integer|exists:users_categories,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'color' => 'required|string',
            'email' => 'required|string|email',
            'comment' => 'nullable|string',
            'phones' => 'required',
            'phones.*' => 'required',
        ];
    }
}
