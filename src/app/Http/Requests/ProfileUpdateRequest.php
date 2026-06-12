<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ValidNomeAluno; // Adicione esta linha
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new ValidNomeAluno],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'goal' => ['nullable', 'string', 'in:hypertrophy,weight_loss,conditioning,health,rehabilitation,other'],
            'custom_goal' => ['nullable', 'string', 'required_if:goal,other', 'max:500'],
        ];
    }
}