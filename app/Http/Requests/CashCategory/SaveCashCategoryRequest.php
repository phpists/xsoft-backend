<?php

namespace App\Http\Requests\CashCategory;

use Illuminate\Foundation\Http\FormRequest;

class SaveCashCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'type_id' => 'required'
        ];
    }
}
