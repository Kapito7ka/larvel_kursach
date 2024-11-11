<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Carbon\Carbon;

class TimeCast implements CastsAttributes
{
    /**
     * Преобразует значение из базы данных в формат, який використовується у вашій моделі.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return \Carbon\Carbon
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return Carbon::createFromFormat('H:i:s', $value)->format('H:i:s');
    }

    /**
     * Преобразует значення з моделі для запису в базу даних.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return Carbon::createFromFormat('H:i:s', $value)->format('H:i:s');
    }
} 