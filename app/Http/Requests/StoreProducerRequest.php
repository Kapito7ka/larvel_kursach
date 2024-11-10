<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProducerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:producers,email',
            'phone_number' => 'required|string|max:20',
            'date_of_birth' => 'required|date_format:d/m/Y',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => 'Ім\'я обов\'язкове',
            'last_name.required' => 'Прізвище обов\'язкове',
            'email.required' => 'Email обов\'язковий',
            'email.email' => 'Невірний формат email',
            'email.unique' => 'Такий email вже існує',
            'phone_number.required' => 'Номер телефону обов\'язковий',
            'date_of_birth.required' => 'Дата народження обов\'язкова',
            'date_of_birth.date_format' => 'Невірний формат дати. Використовуйте формат ДД/ММ/РРРР',
        ];
    }
}
