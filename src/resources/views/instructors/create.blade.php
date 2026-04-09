<x-app-layout>
<div class="workout-form-wrap">

    <div class="workout-form-header">
        <div>
            <p class="workout-form-kicker">Gerenciamento</p>
            <h1 class="workout-form-title">Novo Instrutor</h1>
        </div>
        <a href="{{ route('dashboard') }}" class="workout-form-back">← Voltar</a>
    </div>

    @if($errors->any())
        <div class="enrollment-errors" style="margin-bottom: 20px;">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="workout-form-card">
        <form action="{{ route('instructors.store') }}" method="POST">
            @csrf

            {{-- Seletor de usuário em cards --}}
            <div class="profile-field">
                <label>Selecione o Usuário</label>

                @if($users->isEmpty())
                    <div class="inst-empty" style="margin-top:8px;">
                        Nenhum usuário disponível para ser instrutor.
                    </div>
                @else
                    <div style="display:flex; flex-direction:column; gap:8px; margin-top:6px;">
                        @foreach($users as $user)
                        <div class="user-option" id="wrap-{{ $user->id }}">
                            <input
                                type="radio"
                                name="user_id"
                                id="user_{{ $user->id }}"
                                value="{{ $user->id }}"
                                {{ old('user_id') == $user->id ? 'checked' : '' }}
                                style="display:none;"
                                onchange="selectUser({{ $user->id }})"
                            >
                            <label
                                for="user_{{ $user->id }}"class="user-option__label" style="display:flex; align-items:center; gap:14px; padding:14px 16px; cursor:pointer;">
                                <div class="radio-indicator" class="radio-indicator" style="width:18px; height:18px; flex-shrink:0; transition:all .2s; display:flex; align-items:center; justify-content:center;"></div>
                                <div class="student-avatar" style="width:36px; height:36px; font-size:12px; flex-shrink:0;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div style="flex:1; min-width:0;">
                                    <p style="font-size:14px; font-weight:700; margin:0 0 2px;" class="user-option__name">{{ $user->name }}</p>
                                    <p style="font-size:12px; color:var(--text-muted); margin:0;">{{ $user->email }}</p>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                @endif

                @error('user_id')
                    <span class="profile-field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="profile-field">
                <label>Especialidade</label>
                <input
                    type="text"
                    name="specialty"
                    value="{{ old('specialty') }}"
                    placeholder="Ex: Musculação, Crossfit..."
                >
                @error('specialty')
                    <span class="profile-field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="profile-form-row" style="margin-top: 8px;">
                <button type="submit" class="btn-save">Salvar</button>
                <a href="{{ route('dashboard') }}" class="btn-cancel">Cancelar</a>
            </div>

        </form>
    </div>

</div>

<script>
function selectUser(id) {
    document.querySelectorAll('.user-option__label').forEach(label => {
        label.style.borderColor = '';
        label.style.background = '';
        label.querySelector('.radio-indicator').style.borderColor = '';
        label.querySelector('.radio-indicator').style.background = '';
    });
    const selected = document.querySelector('#wrap-' + id + ' .user-option__label');
    selected.style.borderColor = 'var(--red)';
    selected.style.background = 'var(--red-dim)';
    selected.querySelector('.radio-indicator').style.borderColor = 'var(--red)';
    selected.querySelector('.radio-indicator').style.background = 'var(--red)';
}
// Inicializa estado se vier de old()
document.addEventListener('DOMContentLoaded', function() {
    const checked = document.querySelector('input[name="user_id"]:checked');
    if (checked) selectUser(checked.value);
});
</script>
</x-app-layout>