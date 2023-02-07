<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\PassengerCount;

class FlightRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'from' => 'required|exists:airports,iata',
            'to' => 'required|exists:airports,iata',
            'date1' => 'required|date_format:Y-m-d|exists:flights,date',
            'date2' => 'date_format:Y-m-d|exists:flights,date',
            'passengers' => ['required','integer', 'min:1', 'max:8']
        ];
    }

    public function messages()
    {
        return [
            'from.required' => 'Аэропорт отправления не указан',
            'to.required' => 'Аэропорт прибытия не указан',
            'from.exists' => 'Такого аэропорта отправления не существует',
            'to.exists' => 'Такого аэропорта прибытия не существует',
            'date1.required' => 'Не указана дата отправления',
            'date1.date_format'=> 'Не верный формат даты - Г-м-д',
            'date1.exists'=> 'На эту дату нет рейсов',
            'date2.date_format'=> 'Не верный формат даты - Г-м-д',
            'date2.exists'=> 'На эту дату нет рейсов',
            'passengers.required' => 'Количество пассажиров не указано',
            'passengers.min' => 'Минимальное количество пассажиров на рейс - 1',
            'passengers' => 'Максимальное количество пассажиров на рейс - 8',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'data' => $validator->errors(),
            'code' => 422,
        ]));
    }
}
