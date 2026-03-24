<x-app-layout>
    @push('styles')
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endpush
    <x-slot name="header">
        <h2 style="font-size:18px; font-weight:700; color:#fff;">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="profile-wrapper">

        {{-- Atualizar informações --}}
        <div class="profile-card">
            <div class="profile-card__title">Informações do perfil</div>
            <div class="profile-card__desc">Atualize seu nome e endereço de e-mail.</div>

            <form method="post" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="profile-field">
                    <label for="name">Nome</label>
                    <input id="name" type="text" name="name"
                           value="{{ old('name', $user->name) }}"
                           required autofocus autocomplete="name"
                           placeholder="Seu nome">
                    @error('name')
                        <p class="profile-field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="email">E-mail</label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email', $user->email) }}"
                           required autocomplete="username"
                           placeholder="seu@email.com">
                    @error('email')
                        <p class="profile-field-error">{{ $message }}</p>
                    @enderror
                </div>

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div style="margin-bottom:16px;">
                        <p style="font-size:13px; color:var(--text-muted);">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification" style="background:none; border:none; color:var(--red-light); cursor:pointer; font-size:13px; padding:0;">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>
                        @if (session('status') === 'verification-link-sent')
                            <p style="font-size:13px; color:var(--green-light); margin-top:6px;">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif

                <div class="profile-form-row">
                    <button type="submit" class="btn-save">Salvar</button>
                    @if (session('status') === 'profile-updated')
                        <p class="profile-saved">Salvo!</p>
                    @endif
                </div>
            </form>
        </div>

        {{-- Alterar senha --}}
        <div class="profile-card">
            <div class="profile-card__title">Alterar senha</div>
            <div class="profile-card__desc">Use uma senha longa e aleatória para manter sua conta segura.</div>

            <form method="post" action="{{ route('password.update') }}">
                @csrf
                @method('put')

                <div class="profile-field">
                    <label for="current_password">Senha atual</label>
                    <input id="current_password" type="password" name="current_password"
                           autocomplete="current-password" placeholder="••••••••">
                    @error('current_password', 'updatePassword')
                        <p class="profile-field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="password">Nova senha</label>
                    <input id="password" type="password" name="password"
                           autocomplete="new-password" placeholder="••••••••">
                    @error('password', 'updatePassword')
                        <p class="profile-field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="password_confirmation">Confirmar nova senha</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           autocomplete="new-password" placeholder="••••••••">
                    @error('password_confirmation', 'updatePassword')
                        <p class="profile-field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="profile-form-row">
                    <button type="submit" class="btn-save">Atualizar senha</button>
                    @if (session('status') === 'password-updated')
                        <p class="profile-saved">Atualizado!</p>
                    @endif
                </div>
            </form>
        </div>

        {{-- Deletar conta --}}
        <div class="profile-card profile-card--danger" x-data="{ confirming: false }">
            <div class="profile-card__title profile-card__title--danger">Deletar conta</div>
            <div class="profile-card__desc">Uma vez deletada, todos os dados serão permanentemente removidos.</div>

            <button class="btn-delete" @click="confirming = true">Deletar conta</button>

            {{-- Modal de confirmação --}}
             <div x-show="confirming" x-cloak
     style="position:fixed; inset:0; z-index:50;
            background:rgba(0,0,0,0.7);
            backdrop-filter:blur(4px);
            -webkit-backdrop-filter:blur(4px);">

    <div style="display:flex; align-items:center; justify-content:center;
                width:100%; height:100%; padding:1rem;">

        <div style="background:#1a1a1a; border:1px solid rgba(255,255,255,0.10);
                    border-radius:16px; padding:28px; max-width:420px; width:100%;">
                        <h2 style="font-size:16px; font-weight:700; color:#fff; margin-bottom:8px;">Tem certeza?</h2>
                        <p style="font-size:13px; color:rgba(255,255,255,0.5); margin-bottom:20px;">
                            Esta ação é irreversível. Digite sua senha para confirmar.
                        </p>

                        <form method="post" action="{{ route('profile.destroy') }}">
                            @csrf
                            @method('delete')

                            <div class="profile-field">
                                <label for="delete_password">Senha</label>
                                <input id="delete_password" type="password" name="password"
                                       placeholder="••••••••" autofocus>
                                @error('password', 'userDeletion')
                                    <p class="profile-field-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div style="display:flex; gap:10px; margin-top:8px;">
                                <button type="submit" class="btn-delete">Confirmar exclusão</button>
                                <button type="button" class="btn-cancel" @click="confirming = false">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>