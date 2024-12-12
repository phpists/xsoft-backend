<?php

namespace App\Http\Requests\ProductMovement;

use Illuminate\Foundation\Http\FormRequest;

class DeleteProductMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idx.*' => 'required|integer|exists:products_movement,id',
        ];
    }
}
