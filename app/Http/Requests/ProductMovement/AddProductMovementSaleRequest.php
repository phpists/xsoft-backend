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
            'product_id' => 'required',
            'type_id' => 'required',
            'qty' => ['required', new ProductMovementQtyRule()],
            'measurement_id' => 'required',
            'cost_price' => 'required',
            'retail_price' => 'required',
            'description' => 'sometimes',
        ];
    }


}


