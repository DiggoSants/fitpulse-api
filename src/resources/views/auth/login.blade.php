<x-guest-layout>

    {{-- Status da sessão --}}
    @if (session('status'))
        <div class="auth-status">{{ session('status') }}</div>
    @endif

    <h2 style="font-family: var(--font-primary); font-size: 28px; letter-spacing: 2px; margin-bottom: 24px; color: var(--text);">
        ENTRAR
    </h2>

    <form method="POST" action="{{ route('login') }}">
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
            <input id="password" type="password" name="password" required autocomplete="current-password" />
            @error('password') <span class="text-red-400">{{ $message }}</span> @enderror
        </div>

        {{-- Lembrar --}}
        <div class="auth-field">
            <label class="remember-label">
                <input type="checkbox" name="remember" />
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

</x-guest-layout>