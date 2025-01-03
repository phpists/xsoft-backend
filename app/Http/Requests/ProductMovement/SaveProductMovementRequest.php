<?php

namespace App\Http\Requests\ProductMovement;

use Illuminate\Foundation\Http\FormRequest;

class SaveProductMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'staff_id' => 'required|integer|exists:users,id',
            'warehouse_id' => 'required',
            'supplier_id' => 'required',
            'total_price' => 'required',
            'date_create' => 'required',
            'time_create' => 'required',
            'debt_status' => 'sometimes',
            'debt' => 'sometimes',
            'debt_data' => 'sometimes',
            'installment_payment' => 'sometimes',
            'box_office_date' => 'sometimes',
            'items.*' => 'required',
            'cashes.*.cashes_id' => 'required',
        ];

    }
}
