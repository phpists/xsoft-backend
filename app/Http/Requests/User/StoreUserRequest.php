<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'category_id' => 'required|integer|exists:users_categories,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string',
            'color' => 'sometimes|string',
            'bd_day' => 'sometimes|date|before:today',
            'comment' => 'sometimes',
            'phones' => 'sometimes',
            'phones.*' => 'sometimes',
            'tags' => 'sometimes',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => 'Поле категорії обов\'язкове.',
            'first_name.required' => 'Поле ім\'я обов\'язкове.',
            'last_name.required' => 'Поле прізвище обов\'язкове.',
            'phone.regex' => 'Невірний формат номера телефону.',
            'color.regex' => 'Невірний формат кольору.',
            'bd_day.before' => 'Дата народження повинна бути раніше сьогоднішньої дати.',
            'email.unique' => 'Пользователь с таким email уже зарегистрирован',
        ];
    }
}
