<?php

namespace App\Http\Requests\ProductMovement;

use App\Rules\ProductMovementQtyRule;
use Illuminate\Foundation\Http\FormRequest;

class AddProductMovementSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_movement_id' => 'required|integer|exists:products_movement,id',
            'type_id' => 'required',
            'items.*' => 'required',
            'items.*.id' => ['required', new ProductMovementQtyRule()],
            'items.*.qty' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'id.exists' => 'Такого товару на складі вже немає!'
        ];
    }


}


