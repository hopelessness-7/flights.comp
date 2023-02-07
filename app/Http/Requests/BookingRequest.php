<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class   BookingRequest extends FormRequest
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
            'flight_from' => 'required',
            'flight_from.id' => 'required|exists:flights,id',
            'flight_from.date' => 'required|date_format:Y-m-d',

            'flight_back.id' => 'required_with:flight_back|exists:flights,id',
            'flight_back.date' => 'required_with:flight_back|date_format:Y-m-d',

            'passengers' => 'required|array',
            'passengers.*.first_name' => 'required|string',
            'passengers.*.last_name' => 'required|string',
            'passengers.*.birth_date' => 'required|date_format:Y-m-d',
            'passengers.*.document_number' => 'required|string|min:10|max:10'
        ];
    }

    public function messages()
    {
        return [
            'flight_from.required' => 'Рейс не указан',
            'passengers.required' => 'Пассажир(ы) не добавлен(ы)',
            'passengers.*.first_name' => 'Имя пассажира не указано',
            'passengers.*.last_name' => 'Фамилия пассажира не указано',
            'passengers.*.birth_date' => 'Дата рождения пассажира не указано',
            'passengers.*.document_number' => 'Серия и номер паспорта пассажира не указано',
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
