<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePerformanceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'producer' => 'required|exists:producers,id',
            'image' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Назва вистави обов\'язкова',
            'duration.required' => 'Тривалість обов\'язкова',
            'producer.required' => 'Продюсер обов\'язковий',
            'producer.exists' => 'Вказаний продюсер не існує',
            'image.required' => 'Зображення обов\'язкове'
        ];
    }
}
