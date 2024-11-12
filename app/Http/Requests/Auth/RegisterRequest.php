<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone' => 'required',
            'email' => 'required|string|email|unique:users,email',
            'first_name' => 'required',
            'last_name' => 'required',
            'password' => 'required|string|min:1|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'Пользователь с таким email уже зарегистрирован',
            'phone' => 'Номер телефона должен состоять из 11 цифр',
        ];
    }
}
