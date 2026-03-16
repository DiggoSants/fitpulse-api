<x-guest-layout>

    <h2 style="font-family: var(--font-primary); font-size: 28px; letter-spacing: 2px; margin-bottom: 24px; color: var(--text);">
        CRIAR CONTA
    </h2>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Nome --}}
        <div class="auth-field">
            <label for="name">Nome</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
            @error('name') <span class="text-red-400">{{ $message }}</span> @enderror
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

</x-guest-layout>