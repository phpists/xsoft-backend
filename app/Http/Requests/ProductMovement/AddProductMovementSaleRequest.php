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
            'type_id' => 'required',
            'items.*' => 'required',
            'items.*.id' => ['required', new ProductMovementQtyRule()],
            'items.*.qty' => ['required'],
            'cashes.*.cashes_id' => 'required',
            'debt_data' => 'sometimes',
        ];
    }

    public function messages()
    {
        return [
            'id.exists' => 'Такого товару на складі вже немає!'
        ];
    }


}


