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
            'duration' => 'required|integer',
            'producer' => 'required|exists:producers,id',
            'image' => 'nullable|string',
            'genre_id' => 'required|exists:genres,id',
            'actors' => 'required|array',
            'actors.*' => 'required|integer|exists:actors,id'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Назва вистави обов\'язкова',
            'duration.required' => 'Тривалість обов\'язкова',
            'producer.required' => 'Продюсер обов\'язковий',
            'producer.exists' => 'Вказаний продюсер не існує',
            'image.required' => 'Зображення обов\'язкове',
            'genre_id.required' => 'Жанр обов\'язковий',
            'genre_id.exists' => 'Вказаний жанр не існує',
            'actors.required' => 'Потрібно вибрати хоча б одного актора',
            'actors.array' => 'Некоректний формат даних акторів',
            'actors.*.required' => 'ID актора обов\'язковий',
            'actors.*.integer' => 'ID актора повинен бути числом',
            'actors.*.exists' => 'Актор з вказаним ID не існує'
        ];
    }
}
