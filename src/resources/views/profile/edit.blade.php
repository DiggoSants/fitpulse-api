<x-app-layout>
    @push('styles')
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endpush
    <x-slot name="header">
        <h2 style="font-size:18px; font-weight:700; color:#fff;">
            {{ __('Perfil') }}
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
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 style="position:fixed; inset:0; z-index:50;
                        backdrop-filter:blur(8px);
                        -webkit-backdrop-filter:blur(8px);
                        background:rgba(0,0,0,0.15);
                        display:flex !important; align-items:center; justify-content:center; padding:1rem;">

                <div x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     style="background:rgba(255,255,255,0.06);
                            border:1px solid rgba(255,255,255,0.12);
                            border-radius:14px; padding:24px 28px;
                            max-width:380px; width:100%;
                            margin:auto;
                            box-shadow: 0 0 0 1px rgba(220,38,38,0.15),
                                        0 0 40px rgba(220,38,38,0.12),
                                        0 8px 32px rgba(0,0,0,0.3);">

                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                        <span style="font-size:14px; font-weight:700; color:#fff;">Deletar conta</span>
                        <span style="font-size:11px; color:rgba(239,68,68,0.8); margin-left:2px;">— irreversível</span>
                    </div>

                    <p style="font-size:12px; color:rgba(255,255,255,0.45); margin-bottom:16px; line-height:1.6;">
                        Todos os dados serão permanentemente removidos. Digite sua senha para confirmar.
                    </p>

                    <form method="post" action="{{ route('profile.destroy') }}" x-data="{ pwd: '' }">
                        @csrf
                        @method('delete')

                        <div class="profile-field" style="margin-bottom:12px;">
                            <input id="delete_password" type="password" name="password"
                                   x-model="pwd"
                                   placeholder="Sua senha"
                                   autofocus
                                   style="background:rgba(255,255,255,0.05);
                                          border:1px solid rgba(220,38,38,0.25);
                                          border-radius:8px; padding:9px 12px;
                                          font-size:13px; color:#fff; width:100%;
                                          outline:none;">
                            @error('password', 'userDeletion')
                                <p class="profile-field-error" style="margin-top:6px;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div style="display:flex; gap:8px; justify-content:flex-end;">
                            <button type="button" class="btn-cancel"
                                    style="font-size:12px; padding:7px 14px;"
                                    @click="confirming = false; pwd = ''">
                                Cancelar
                            </button>
                            <button type="submit" class="btn-delete"
                                    style="font-size:12px; padding:7px 14px;"
                                    :disabled="pwd.length === 0"
                                    :style="pwd.length === 0 ? 'opacity:0.4; cursor:not-allowed;' : ''">
                                Confirmar exclusão
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>