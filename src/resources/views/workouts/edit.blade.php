<x-app-layout>
    <div aria-hidden="true" class="form-watermark">
        <span>FIT</span>
        <span>PULSE</span>
    </div>

    <div class="form-content">
        <div class="workout-form-wrap">

            <div class="workout-form-header">
                <div>
                    <p class="workout-form-kicker">TREINOS</p>
                    <h1 class="workout-form-title">Editar Treino</h1>
                    @if(Auth::user()->isInstructor() || Auth::user()->isManager())
                        <p style="font-size:13px; opacity:.6;">Para: {{ $student->user->name }}</p>
                    @endif
                </div>
                @if(Auth::user()->isInstructor() || Auth::user()->isManager())
                    <a href="{{ route('dashboard') }}" class="workout-form-back">← Voltar</a>
                @else
                    <a href="{{ route('workouts.index') }}" class="workout-form-back">← Voltar</a>
                @endif
            </div>

            <div class="workout-form-card">

                @if(session('error'))
                    <div style="color:red; margin-bottom:10px;">{{ session('error') }}</div>
                @endif

                <form action="{{ route('workouts.update', $workout->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="student_id" value="{{ $student->id }}">

                    <div class="profile-field">
                        <label>Nome do treino</label>
                        <input type="text" name="name" value="{{ old('name', $workout->name) }}">
                        @error('name')
                            <span style="color:#ff4d6a; font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="margin-top:1.5rem; margin-bottom:12px;">
                        <p class="section-label">EXERCÍCIOS</p>
                    </div>

                    @error('exercise_id')
                        <div style="color:#ff4d6a; font-size:12px; margin-bottom:10px;">{{ $message }}</div>
                    @enderror

                    <ul class="exercise-list">
                        @foreach($exercises as $exercise)
                            @php
                                $we = $workout->workoutExercises->firstWhere('exercise_id', $exercise->id);
                            @endphp
                            <li class="exercise-card {{ $we ? 'exercise-card--active' : '' }}"
                                style="flex-direction:column; align-items:flex-start; gap:10px;">
                                <div class="workout-exercise-head">
                                <label style="display:flex; align-items:center; gap:10px; cursor:pointer; min-width:0;">
                                    <input type="checkbox" name="exercise_id[]" value="{{ $exercise->id }}"
                                           {{ $we ? 'checked' : '' }}
                                           style="accent-color:var(--red); width:16px; height:16px; cursor:pointer;">
                                    <div class="exercise-thumb" style="width:40px; height:40px; flex-shrink:0;">
                                        <svg viewBox="0 0 24 24">
                                            <rect x="2" y="9" width="4" height="6" rx="1"/>
                                            <rect x="18" y="9" width="4" height="6" rx="1"/>
                                            <rect x="7" y="11" width="10" height="2" rx="1"/>
                                        </svg>
                                    </div>
                                    <span class="exercise-name">{{ $exercise->name }}</span>
                                    @if($we)
                                        <span class="chip chip--series" style="margin-left:auto;">Incluído</span>
                                    @endif
                                </label>

                                <button type="button"
                                        class="workout-exercise-delete"
                                        data-delete-form="delete-exercise-{{ $exercise->id }}"
                                        data-exercise-name="{{ $exercise->name }}"
                                        title="Apagar exercício"
                                        aria-label="Apagar exercício {{ $exercise->name }}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                </div>
                                <div class="workout-inputs">
                                    <input type="number" name="sets[{{ $exercise->id }}]"
                                           value="{{ old('sets.'.$exercise->id, $we->sets ?? '') }}"
                                           placeholder="Séries" class="workout-input-sm" min="1">
                                    <input type="number" name="reps[{{ $exercise->id }}]"
                                           value="{{ old('reps.'.$exercise->id, $we->reps ?? '') }}"
                                           placeholder="Reps" class="workout-input-sm" min="1">
                                    <input type="number" name="rest_time[{{ $exercise->id }}]"
                                           value="{{ old('rest_time.'.$exercise->id, $we->rest_time ?? '') }}"
                                           placeholder="Descanso (s)" class="workout-input-sm" min="1">
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="profile-form-row" style="margin-top:1.5rem;">
                        <button type="submit" class="btn-save">Atualizar treino</button>
                        @if(Auth::user()->isInstructor() || Auth::user()->isManager())
                            <a href="{{ route('dashboard') }}" class="btn-cancel" style="text-decoration:none;">Cancelar</a>
                        @else
                            <a href="{{ route('workouts.index') }}" class="btn-cancel" style="text-decoration:none;">Cancelar</a>
                        @endif
                    </div>

                </form>

                @foreach($exercises as $exercise)
                    <form id="delete-exercise-{{ $exercise->id }}" action="{{ route('exercises.destroy', $exercise->id) }}" method="POST" style="display:none;">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach
            </div>
        </div>
    </div>

    <div id="exercise-delete-modal" class="fit-confirm-overlay" style="display:none;" aria-hidden="true">
        <div class="fit-confirm-modal" role="dialog" aria-modal="true" aria-labelledby="exercise-delete-title">
            <div class="fit-confirm-modal__icon">
                <i class="fa-solid fa-trash"></i>
            </div>
            <p class="fit-confirm-modal__eyebrow">Excluir exercício</p>
            <h2 id="exercise-delete-title" class="fit-confirm-modal__title">Apagar da biblioteca?</h2>
            <p class="fit-confirm-modal__text">
                O exercício <strong id="exercise-delete-name">—</strong> também será removido dos treinos que usam ele.
            </p>
            <div class="fit-confirm-modal__actions">
                <button type="button" class="fit-confirm-btn fit-confirm-btn--cancel" id="exercise-delete-cancel">Cancelar</button>
                <button type="button" class="fit-confirm-btn fit-confirm-btn--danger" id="exercise-delete-confirm">Apagar exercício</button>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.getElementById('exercise-delete-modal');
            const nameEl = document.getElementById('exercise-delete-name');
            const cancelBtn = document.getElementById('exercise-delete-cancel');
            const confirmBtn = document.getElementById('exercise-delete-confirm');
            let formId = null;

            document.querySelectorAll('[data-delete-form]').forEach(button => {
                button.addEventListener('click', () => {
                    formId = button.dataset.deleteForm;
                    nameEl.textContent = button.dataset.exerciseName || 'este exercício';
                    modal.style.display = 'flex';
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.style.overflow = 'hidden';
                    cancelBtn.focus();
                });
            });

            function closeDeleteModal() {
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                formId = null;
            }

            cancelBtn.addEventListener('click', closeDeleteModal);
            modal.addEventListener('click', event => {
                if (event.target === modal) closeDeleteModal();
            });
            document.addEventListener('keydown', event => {
                if (event.key === 'Escape' && modal.style.display === 'flex') closeDeleteModal();
            });
            confirmBtn.addEventListener('click', () => {
                if (formId) document.getElementById(formId)?.submit();
            });
        })();
    </script>

</x-app-layout>
