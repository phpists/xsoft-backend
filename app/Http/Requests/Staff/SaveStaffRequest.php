<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class SaveStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role_id' => 'required',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'color' => 'sometimes',
            'email' => 'required|string|email',
            'comment' => 'nullable|string',
            'password' => 'required',
            'phones' => 'sometimes',
            'branches' => 'sometimes',
            'position_id' => 'sometimes',
            'department_id' => 'sometimes'
        ];
    }
}
