<x-app-layout>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endpush

    {{-- Watermark --}}
    <div aria-hidden="true" class="form-watermark">
        <span>FIT</span>
        <span>PULSE</span>
    </div>

    <div class="form-content">
        <div class="workout-form-wrap">

            {{-- Cabeçalho --}}
            <div class="workout-form-header">
                <div>
                    <p class="workout-form-kicker">TREINOS</p>
                    <h1 class="workout-form-title">Criar Treino</h1>
                </div>
                <a href="{{ route('dashboard') }}" class="workout-form-back">← Voltar</a>
            </div>

            {{-- Card do formulário --}}
            <div class="workout-form-card">

                @if(session('error'))
                <div style="color:red; margin-bottom:10px;">
                    {{ session('error') }}
                </div>
                @endif


                <form action="/workout/store" method="POST">
                    @csrf

            {{-- Nome do treino --}}
            <div class="profile-field">
                <label>Nome do treino</label>
                <input type="text" name="name" placeholder="Ex: Supino">
            </div>


                    {{-- Exercícios --}}
                    <a href="{{ route('exercises.create') }}"
                        style="display:inline-block; margin-bottom:15px; color:#4ade80;">
                        + Adicionar novo exercício
                    </a>
                    <div style="margin-top:1.5rem; margin-bottom:12px;">
                        <p class="section-label">EXERCÍCIOS</p>
                    </div>

                    <ul class="exercise-list">
                        @foreach($exercises as $exercise)
                        <li class="exercise-card" style="flex-direction:column; align-items:flex-start; gap:10px;">

                            <label style="display:flex; align-items:center; gap:10px; cursor:pointer; width:100%;">
                                <input type="checkbox" name="exercise_id[]" value="{{ $exercise->id }}"
                                    style="accent-color:var(--red); width:16px; height:16px; cursor:pointer;">
                                <div class="exercise-thumb" style="width:40px; height:40px; flex-shrink:0;">
                                    <svg viewBox="0 0 24 24">
                                        <rect x="2" y="9" width="4" height="6" rx="1" />
                                        <rect x="18" y="9" width="4" height="6" rx="1" />
                                        <rect x="7" y="11" width="10" height="2" rx="1" />
                                    </svg>
                                </div>
                                <span class="exercise-name" style="margin-bottom:0;">{{ $exercise->name }}</span>
                            </label>

                            <div class="workout-inputs">
                                <input type="number" name="sets[{{ $exercise->id }}]" placeholder="Séries" class="workout-input-sm">
                                <input type="number" name="reps[{{ $exercise->id }}]" placeholder="Reps" class="workout-input-sm">
                                <input type="number" name="rest_time[{{ $exercise->id }}]" placeholder="Descanso (s)" class="workout-input-sm">
                            </div>

                        </li>
                        @endforeach
                    </ul>

                    <div class="profile-form-row" style="margin-top:1.5rem;">
                        <button type="submit" class="btn-save">Salvar treino</button>
                        <a href="{{ route('dashboard') }}" class="btn-cancel" style="text-decoration:none;">Cancelar</a>
                    </div>

                </form>
            </div>

        </div>
    </div>

</x-app-layout>