<?php

namespace App\Http\Requests\CashCategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCashCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:cash_categories,id',
            'title' => 'required|string|max:255',
            'type_id' => 'required'
        ];
    }
}
