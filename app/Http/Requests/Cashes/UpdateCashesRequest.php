<?php

namespace App\Http\Requests\Cashes;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCashesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:cashes,id',
            'title' => 'required',
            'appointment' => 'required',
            'description' => 'sometimes',
            'is_cash_category' => 'required',
            'cash_categories' => 'sometimes'
        ];
    }
}
