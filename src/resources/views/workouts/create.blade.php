<x-app-layout>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endpush

    <div aria-hidden="true" class="form-watermark">
        <span>FIT</span>
        <span>PULSE</span>
    </div>

    <div class="form-content">
        <div class="workout-form-wrap">

            <div class="workout-form-header">
                <div>
                    <p class="workout-form-kicker">TREINOS</p>
                    <h1 class="workout-form-title">Criar Treino</h1>
                    @if(Auth::user()->isInstructor() || Auth::user()->isManager())
                    <p style="font-size:13px; opacity:.6;">Para: {{ $student->user->name }}</p>
                    @endif
                </div>
                @if(Auth::user()->isInstructor() || Auth::user()->isManager())
                <a href="{{ route('dashboard') }}" class="workout-form-back">← Voltar</a>
                @else
                <a href="{{ route('dashboard') }}" class="workout-form-back">← Voltar</a>
                @endif
            </div>

            <div class="workout-form-card">

                @if(session('error'))
                <div style="color:red; margin-bottom:10px;">{{ session('error') }}</div>
                @endif
                <form action="{{ route('workouts.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">

                    <div class="profile-field">
                        <label>Nome do treino</label>
                        <input type="text" name="name" placeholder="Ex: Treino A" value="{{ old('name') }}">
                        @error('name')
                        <span style="color:#ff4d6a; font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <a href="{{ route('exercises.create') }}"
                        style="display:inline-block; margin-bottom:15px; color:var(--red-light);">
                        + Adicionar novo exercício
                    </a>

                    <div style="margin-top:1.5rem; margin-bottom:12px;">
                        <p class="section-label">EXERCÍCIOS</p>
                    </div>

                    @error('exercise_id')
                    <div style="color:#ff4d6a; font-size:12px; margin-bottom:10px;">{{ $message }}</div>
                    @enderror

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
                                <input type="number" name="sets[{{ $exercise->id }}]" placeholder="Séries" class="workout-input-sm" min="1">
                                <input type="number" name="reps[{{ $exercise->id }}]" placeholder="Reps" class="workout-input-sm" min="1">
                                <input type="number" name="rest_time[{{ $exercise->id }}]" placeholder="Descanso (s)" class="workout-input-sm" min="1">
                            </div>
                        </li>
                        @endforeach
                    </ul>

                    <div class="profile-form-row" style="margin-top:1.5rem;">
                        <button type="submit" class="btn-save">Salvar treino</button>
                        @if(Auth::user()->isInstructor() || Auth::user()->isManager())
                        <a href="{{ route('dashboard') }}" class="btn-cancel" style="text-decoration:none;">Cancelar</a>
                        @else
                        <a href="{{ route('dashboard') }}" class="btn-cancel" style="text-decoration:none;">Cancelar</a>
                        @endif
                    </div>

                </form>
            </div>
        </div>
    </div>

</x-app-layout>