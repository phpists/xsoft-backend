<?php

namespace App\Http\Requests\CashCategory;

use Illuminate\Foundation\Http\FormRequest;

class DeleteCashCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'idx.*' => 'required|integer|exists:cash_categories,id',
        ];
    }
}
