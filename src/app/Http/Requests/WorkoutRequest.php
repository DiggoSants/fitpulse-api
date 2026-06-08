<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'student_id' => ['required', 'exists:users,id'],
            'muscle_groups' => ['required', 'array', 'min:1'],
            'muscle_groups.*' => ['string'], 
            'exercises' => ['required', 'array', 'min:1'],
            'exercises.*.id' => ['exists:exercises,id'],
            'exercises.*.sets' => ['integer', 'min:1', 'max:10'],
            'exercises.*.repetitions' => ['integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'muscle_groups.required' => 'Selecione pelo menos um grupo muscular.',
            'muscle_groups.min' => 'Você deve selecionar pelo menos um grupo muscular para o treino.',
            'exercises.required' => 'Adicione pelo menos um exercício ao treino.',
        ];
    }
}