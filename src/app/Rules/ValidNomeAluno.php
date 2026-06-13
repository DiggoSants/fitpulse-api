<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidNomeAluno implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = trim((string) $value);

        if ($value === '') {
            $fail('O nome do aluno não pode estar vazio.');
            return;
        }

        if (str_contains($value, '@')) {
            $fail('O nome não pode conter @. Use apenas letras, espaços e acentos.');
            return;
        }

        if (preg_match('/[^a-zA-ZÀ-ÿ\s]/u', $value)) {
            $fail('Nome inválido. Use apenas letras, espaços e acentos.');
            return;
        }

        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $fail('O nome não pode ser um endereço de e-mail.');
            return;
        }

        if (preg_match('/[0-9]/', $value)) {
            $fail('O nome não pode conter números.');
            return;
        }

        if (preg_match_all('/[a-zA-ZÀ-ÿ]/u', $value) < 2) {
            $fail('O nome deve conter pelo menos 2 letras.');
            return;
        }

        if (str_contains($value, '  ')) {
            $fail('O nome não pode ter espaços duplos.');
        }
    }

    public function message(): string
    {
        return 'Nome inválido. Use apenas letras, espaços e acentos.';
    }
}
