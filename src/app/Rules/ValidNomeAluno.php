<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidNomeAluno implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Remove espaços extras do início e fim
        $value = trim($value);
        
        // Verifica se está vazio
        if (empty($value)) {
            $fail('O nome do aluno não pode estar vazio.');
            return;
        }
        
        // 1. Bloquear caracteres especiais (exceto espaços, acentos e cedilha)
        if (preg_match('/[^a-zA-ZÀ-ÿ\s]/u', $value)) {
            $fail('O nome não pode conter caracteres especiais como @, #, $, %, &, *, etc.');
            return;
        }
        
        // 2. Impedir formato de e-mail
        if (filter_var($value, FILTER_VALIDATE_EMAIL) || str_contains($value, '@')) {
            $fail('O nome não pode ser um endereço de e-mail.');
            return;
        }
        
        // 3. Bloquear números
        if (preg_match('/[0-9]/', $value)) {
            $fail('O nome não pode conter números.');
            return;
        }
        
        // 4. Bloquear símbolos específicos
        $simbolosProibidos = ['!', '?', ';', ':', '|', '\\', '/', '*', '+', '=', '<', '>', '[', ']', '{', '}', '^', '~', '`', '"', "'", ','];
        foreach ($simbolosProibidos as $simbolo) {
            if (str_contains($value, $simbolo)) {
                $fail('O nome contém símbolos não permitidos. Use apenas letras, espaços e acentos.');
                return;
            }
        }
        
        // 5. Verificar se tem pelo menos duas letras
        if (preg_match_all('/[a-zA-ZÀ-ÿ]/u', $value) < 2) {
            $fail('O nome deve conter pelo menos 2 letras.');
            return;
        }
        
        // 6. Impedir espaços no início ou fim
        if ($value !== trim($value)) {
            $fail('O nome não pode começar ou terminar com espaços.');
            return;
        }
        
        // 7. Impedir múltiplos espaços seguidos
        if (str_contains($value, '  ')) {
            $fail('O nome não pode ter espaços duplos.');
            return;
        }
    }
    
    public function message()
    {
        return 'Nome inválido. Use apenas letras, espaços e acentos.';
    }
}