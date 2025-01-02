<?php

namespace App\Http\Requests\Cashes;

use Illuminate\Foundation\Http\FormRequest;

class GetCasheById extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:cashes,id',
            'debt_status' => 'something',
        ];
    }
}
