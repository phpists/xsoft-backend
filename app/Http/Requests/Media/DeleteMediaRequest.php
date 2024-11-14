<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class DeleteMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:media,id',
        ];
    }
}
