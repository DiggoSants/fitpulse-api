<x-guest-layout>

    @if (session('status'))
        <div class="auth-status">{{ session('status') }}</div>
    @endif

    <h2 style="font-family: var(--font-primary); font-size: 28px; letter-spacing: 2px; margin-bottom: 10px; color: var(--text);">
        RECUPERAR SENHA
    </h2>

    <p style="font-size: 13px; line-height: 1.6; color: var(--muted); margin: 0 0 24px;">
        Informe seu e-mail e enviaremos um link para criar uma nova senha.
    </p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="auth-field">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
            @error('email') <span class="text-red-400">{{ $message }}</span> @enderror
        </div>

        <div class="auth-actions">
            <a class="auth-link" href="{{ route('login') }}">Voltar ao login</a>
            <button type="submit" class="auth-btn-primary">
                <i class="fa-solid fa-paper-plane"></i> ENVIAR LINK
            </button>
        </div>
    </form>

</x-guest-layout>
