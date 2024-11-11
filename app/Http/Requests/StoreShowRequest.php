<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShowRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'performance_id' => 'required|exists:performances,id',
            'datetime' => 'required|date',
            'price' => 'required|numeric|min:0',
            'hall_id' => 'required|exists:halls,id',
        ];
    }

    public function messages()
    {
        return [
            'performance_id.required' => 'ID вистави обов\'язкове',
            'performance_id.exists' => 'Вистава не існує',
            'datetime.required' => 'Дата та час обов\'язкові',
            'datetime.date' => 'Некоректний формат дати',
            'price.required' => 'Ціна обов\'язкова',
            'price.numeric' => 'Ціна повинна бути числом',
            'price.min' => 'Ціна не може бути від\'ємною',
            'hall_id.required' => 'ID залу обов\'язкове',
            'hall_id.exists' => 'Зал не існує',
        ];
    }
}
