<section>
    <header>
        <p class="profile-card__title">{{ __('Atualizar Senha') }}</p>
        <p class="profile-card__desc">{{ __('Use uma senha longa e aleatória para manter sua conta segura.') }}</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6">
        @csrf
        @method('put')

        <div class="profile-field">
            <label for="update_password_current_password">{{ __('Senha Atual') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" placeholder="••••••••" />
            @error('current_password', 'updatePassword')<p class="profile-field-error">{{ $message }}</p>@enderror
        </div>

        <div class="profile-field">
            <label for="update_password_password">{{ __('Nova Senha') }}</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password" placeholder="••••••••" />
            @error('password', 'updatePassword')<p class="profile-field-error">{{ $message }}</p>@enderror
        </div>

        <div class="profile-field">
            <label for="update_password_password_confirmation">{{ __('Confirmar Senha') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="••••••••" />
            @error('password_confirmation', 'updatePassword')<p class="profile-field-error">{{ $message }}</p>@enderror
        </div>

        <div class="profile-form-row">
            <button type="submit" class="btn-save">{{ __('Atualizar') }}</button>
            @if (session('status') === 'password-updated')
                <span class="profile-saved" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)">{{ __('Salvo!') }}</span>
            @endif
        </div>
    </form>
</section>