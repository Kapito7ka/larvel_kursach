<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHallRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'hall_number' => 'required|numeric|min:1',
        ];
    }

    public function messages()
    {
        return [
            'hall_number.required' => 'Номер зали обов\'язковий',
        ];
    }
}
