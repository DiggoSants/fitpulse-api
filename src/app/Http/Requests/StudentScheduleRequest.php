<?php

namespace App\Http\Requests;

use App\Models\StudentSchedule;
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
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'user_id'    => ['nullable', 'integer', 'exists:users,id'],
            'days'       => ['nullable', 'array'],
            'days.*'     => ['string', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'shifts'     => ['nullable', 'array'],
            'shifts.*'   => ['string', 'in:morning,afternoon,evening,full_day'],
        ];
    }

    public function messages(): array
    {
        return [
            'days.*.in' => 'Dia da semana inválido.',
            'shifts.*.in' => 'Turno de treino inválido.',
        ];
    }
}
