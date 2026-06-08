<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'days' => ['required', 'array', 'min:2'],
            'days.*' => ['string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
        ];
    }

    public function messages(): array
    {
        return [
            'days.required' => 'Selecione os dias que você vai treinar.',
            'days.min' => 'Você deve treinar pelo menos 2 dias por semana.',
            'days.*.in' => 'Dia da semana inválido.',
        ];
    }
}