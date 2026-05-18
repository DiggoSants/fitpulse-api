<x-app-layout>
<div class="form-page">

    <div class="form-watermark" aria-hidden="true">
        <span>FIT</span>
        <span>PULSE</span>
    </div>

    <div class="form-content">
        <div class="workout-form-wrap">

            <div class="workout-form-header">
                <div>
                    <p class="workout-form-kicker">Gerenciamento</p>
                    <h1 class="workout-form-title">Editar Instrutor</h1>
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
                <form action="{{ route('instructors.update', $instructor->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="profile-field">
                        <label>Usuário</label>
                        <div style="display:flex; align-items:center; gap:12px; background:var(--surface-2); border:1px solid var(--border); border-radius:var(--radius-md); padding:11px 14px;">
                            <div class="student-avatar" style="width:32px; height:32px; font-size:12px; flex-shrink:0;">
                                {{ strtoupper(substr($instructor->user->name, 0, 2)) }}
                            </div>
                            <div>
                                <p style="font-size:14px; font-weight:700; margin:0 0 2px;">{{ $instructor->user->name }}</p>
                                <p style="font-size:12px; color:var(--text-muted); margin:0;">{{ $instructor->user->email }}</p>
                            </div>
                            <span style="margin-left:auto; font-size:11px; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.06em;">Não editável</span>
                        </div>
                    </div>

                    <div class="profile-field">
                        <label>Especialidade</label>
                        <input
                            type="text"
                            name="specialty"
                            value="{{ old('specialty', $instructor->specialty) }}"
                            placeholder="Ex: Musculação, Crossfit..."
                        >
                        @error('specialty')
                            <span class="profile-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="profile-form-row" style="margin-top: 8px;">
                        <button type="submit" class="btn-save">Atualizar</button>
                        <a href="{{ route('dashboard') }}" class="btn-cancel">Cancelar</a>
                    </div>

                </form>
            </div>

        </div>
    </div>

</div>
</x-app-layout>
