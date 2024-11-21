<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required',
            'category_id' => 'required|integer|exists:categories,id',
            'locations' => 'required',
            'phones' => 'sometimes'
        ];
    }
}
