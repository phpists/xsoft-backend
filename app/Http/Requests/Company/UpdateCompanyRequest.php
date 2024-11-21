<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:companies,id',
            'title' => 'required',
            'category_id' => 'required|integer|exists:categories,id',
            'locations.*.id' => 'required',
            'phones' => 'sometimes'
        ];
    }
}
