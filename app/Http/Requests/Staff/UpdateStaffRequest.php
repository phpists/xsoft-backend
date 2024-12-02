<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:users,id',
            'role_id' => 'required',
            'first_name' => 'sometimes',
            'last_name' => 'sometimes',
            'color' => 'sometimes',
            'email' => 'required|string|email|exists:users,email',
            'comment' => 'nullable|string',
            'password' => 'sometimes',
            'position_id' => 'sometimes',
            'department_id' => 'sometimes'
        ];
    }
}
