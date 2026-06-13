<x-guest-layout>

    <h2 style="font-family: var(--font-primary); font-size: 28px; letter-spacing: 2px; margin-bottom: 24px; color: var(--text);">
        CRIAR CONTA
    </h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Nome --}}
        <div class="auth-field">
            <label for="name">Nome</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" data-name-validation />
            @error('name') <span class="text-red-400">{{ $message }}</span> @enderror
            <span class="text-red-400" data-name-validation-message style="display:none;"></span>
        </div>

        {{-- Email --}}
        <div class="auth-field">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
            @error('email') <span class="text-red-400">{{ $message }}</span> @enderror
        </div>

        {{-- Senha --}}
        <div class="auth-field">
            <label for="password">Senha</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" />
            @error('password') <span class="text-red-400">{{ $message }}</span> @enderror
        </div>

        {{-- Confirmar Senha --}}
        <div class="auth-field">
            <label for="password_confirmation">Confirmar Senha</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            @error('password_confirmation') <span class="text-red-400">{{ $message }}</span> @enderror
        </div>

        <div class="auth-actions">
            <a class="auth-link" href="{{ route('login') }}">Já tem conta?</a>
            <button type="submit" class="auth-btn-primary">
                <i class="fa-solid fa-dumbbell"></i> REGISTRAR
            </button>
        </div>

    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.querySelector('[data-name-validation]');
        const message = document.querySelector('[data-name-validation-message]');
        const form = input?.closest('form');

        function nameError(value) {
            const trimmed = value.trim();

            if (!trimmed) return 'Informe o nome do aluno.';
            if (value.includes('@')) return 'O nome não pode conter @. Use apenas letras, espaços e acentos.';
            if (!/^[A-Za-zÀ-ÿ\s]+$/u.test(value)) return 'Nome inválido. Use apenas letras, espaços e acentos.';
            if ((value.match(/[A-Za-zÀ-ÿ]/gu) || []).length < 2) return 'O nome deve conter pelo menos 2 letras.';
            if (/\s{2,}/.test(value)) return 'O nome não pode ter espaços duplos.';

            return '';
        }

        function validateName() {
            const error = nameError(input.value);
            input.setCustomValidity(error);

            if (message) {
                message.textContent = error;
                message.style.display = error ? 'block' : 'none';
            }

            return !error;
        }

        if (input && form) {
            input.addEventListener('input', validateName);
            form.addEventListener('submit', function(event) {
                if (!validateName()) {
                    event.preventDefault();
                    input.reportValidity();
                    input.focus();
                }
            });
        }
    });
    </script>

</x-guest-layout>
