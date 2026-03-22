<section>
    <header>
        <p class="profile-card__title profile-card__title--danger">{{ __('Deletar Conta') }}</p>
        <p class="profile-card__desc">{{ __('Ao deletar sua conta, todos os dados serão permanentemente removidos. Esta ação não pode ser desfeita.') }}</p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="btn-delete"
    >{{ __('Deletar Conta') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" style="padding:28px">
            @csrf
            @method('delete')

            <p class="profile-card__title">{{ __('Tem certeza?') }}</p>
            <p class="profile-card__desc" style="margin-top:6px">{{ __('Todos os seus dados serão deletados permanentemente. Digite sua senha para confirmar.') }}</p>

            <div class="profile-field" style="margin-top:16px">
                <label for="password">{{ __('Senha') }}</label>
                <input id="password" name="password" type="password" placeholder="••••••••" />
                @error('password', 'userDeletion')<p class="profile-field-error">{{ $message }}</p>@enderror
            </div>

            <div class="profile-form-row" style="justify-content:flex-end">
                <button type="button" class="btn-cancel" x-on:click="$dispatch('close')">{{ __('Cancelar') }}</button>
                <button type="submit" class="btn-delete">{{ __('Deletar') }}</button>
            </div>
        </form>
    </x-modal>
</section>