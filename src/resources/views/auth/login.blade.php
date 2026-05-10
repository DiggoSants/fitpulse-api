<x-guest-layout>

    {{-- Status da sessão --}}
    @if (session('status'))
        <div class="auth-status">{{ session('status') }}</div>
    @endif

    <h2 style="font-family: var(--font-primary); font-size: 28px; letter-spacing: 2px; margin-bottom: 24px; color: var(--text);">
        ENTRAR
    </h2>

    <form method="POST" action="{{ route('login') }}" id="login-form">
        @csrf

        {{-- Email --}}
        <div class="auth-field">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
            @error('email') <span class="text-red-400">{{ $message }}</span> @enderror
        </div>

        {{-- Senha --}}
        <div class="auth-field">
            <label for="password">Senha</label>
            <div class="password-field">
                <input id="password" type="password" name="password" required autocomplete="current-password" />
                <button type="button" class="password-toggle" id="password-toggle" aria-label="Mostrar senha" aria-pressed="false">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            @error('password') <span class="text-red-400">{{ $message }}</span> @enderror
        </div>

        {{-- Lembrar --}}
        <div class="auth-field">
            <label class="remember-label" for="remember">
                <input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }} />
                Lembrar de mim
            </label>
        </div>

        <div class="auth-actions">
            @if (Route::has('password.request'))
                <a class="auth-link" href="{{ route('password.request') }}">Esqueceu a senha?</a>
            @endif
            <button type="submit" class="auth-btn-primary">
                <i class="fa-solid fa-right-to-bracket"></i> ENTRAR
            </button>
        </div>

        @if (Route::has('register'))
            <p style="margin-top: 20px; text-align: center; font-size: 13px; color: var(--muted);">
                Não tem conta?
                <a href="{{ route('register') }}" class="auth-link" style="color: var(--red); font-weight: 700;">Registre-se</a>
            </p>
        @endif
    </form>

    <script>
        (() => {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('password-toggle');
            const rememberInput = document.getElementById('remember');
            const form = document.getElementById('login-form');
            const storageKey = 'fitpulse.remembered_email';
            const rememberedEmail = localStorage.getItem(storageKey);

            if (rememberedEmail && !emailInput.value) {
                emailInput.value = rememberedEmail;
                rememberInput.checked = true;
            }

            form.addEventListener('submit', () => {
                if (rememberInput.checked) {
                    localStorage.setItem(storageKey, emailInput.value.trim());
                    return;
                }

                localStorage.removeItem(storageKey);
            });

            passwordToggle.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                passwordToggle.setAttribute('aria-label', isPassword ? 'Esconder senha' : 'Mostrar senha');
                passwordToggle.setAttribute('aria-pressed', String(isPassword));
                passwordToggle.innerHTML = `<i class="fa-solid ${isPassword ? 'fa-eye-slash' : 'fa-eye'}"></i>`;
                passwordInput.focus();
            });
        })();
    </script>

</x-guest-layout>
