<section>
    <header>
        <p class="profile-card__title">{{ __('Informações do Perfil') }}</p>
        <p class="profile-card__desc">{{ __("Atualize seu nome e endereço de e-mail.") }}</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6">
        @csrf
        @method('patch')

        <div class="profile-field">
            <label for="name">{{ __('Nome') }}</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" placeholder="Seu nome" />
            @error('name')<p class="profile-field-error">{{ $message }}</p>@enderror
        </div>

        <div class="profile-field">
            <label for="email">{{ __('E-mail') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" placeholder="seu@email.com" />
            @error('email')<p class="profile-field-error">{{ $message }}</p>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="profile-field-error" style="margin-top:6px">
                        {{ __('E-mail não verificado.') }}
                        <button form="send-verification" style="color:var(--red-light);text-decoration:underline;background:none;border:none;cursor:pointer;font-size:12px;">
                            {{ __('Reenviar verificação') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="profile-saved" style="margin-top:4px">{{ __('Link enviado!') }}</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="profile-form-row">
            <button type="submit" class="btn-save">{{ __('Salvar') }}</button>
            @if (session('status') === 'profile-updated')
                <span class="profile-saved" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)">{{ __('Salvo!') }}</span>
            @endif
        </div>
    </form>
</section>