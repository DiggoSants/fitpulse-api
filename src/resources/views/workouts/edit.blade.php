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

                <form action="{{ route('workouts.update', $workout->id) }}" method="POST" id="workout-form">
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

                    <div class="profile-field">
                        <label>Dia da agenda</label>
                        @if(count($scheduleDays) < $minScheduleDays)
                            <div class="weekly-validation weekly-validation--error" style="margin-bottom:10px;">
                                Defina a agenda semanal do aluno antes de atualizar o treino.
                            </div>
                        @endif
                        <div class="workout-day-options">
                            @forelse($scheduleDays as $dayKey)
                                @php $selectedDay = old('week_day', $workout->week_day); @endphp
                                <label class="weekly-day-option {{ $selectedDay === $dayKey ? 'is-selected' : '' }}">
                                    <input type="radio" name="week_day" value="{{ $dayKey }}" @checked($selectedDay === $dayKey)>
                                    <span class="weekly-day-option__short">{{ ['monday'=>'SEG','tuesday'=>'TER','wednesday'=>'QUA','thursday'=>'QUI','friday'=>'SEX','saturday'=>'SAB','sunday'=>'DOM'][$dayKey] ?? '' }}</span>
                                    <span class="weekly-day-option__label">{{ $weekDays[$dayKey] ?? $dayKey }}</span>
                                </label>
                            @empty
                                <div class="weekly-day-empty weekly-day-empty--wide">Nenhum dia cadastrado na agenda.</div>
                            @endforelse
                        </div>
                        @error('week_day')
                            <span style="color:#ff4d6a; font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- FILTRO POR PARTE DO CORPO --}}
                    <div style="margin-top:1.5rem; margin-bottom:12px;">
                        <p class="section-label">FILTRAR POR PARTE DO CORPO</p>
                    </div>

                    <div class="muscle-filter-wrap" id="muscle-filter-wrap">
                        <div class="muscle-filter-chips" id="muscle-filter-chips">
                            <div class="muscle-filter-loading" id="muscle-filter-loading">
                                <span class="sk sk-badge" style="width:72px;"></span>
                                <span class="sk sk-badge" style="width:60px;"></span>
                                <span class="sk sk-badge" style="width:80px;"></span>
                                <span class="sk sk-badge" style="width:68px;"></span>
                            </div>
                        </div>

                        <div class="muscle-filter-actions" id="muscle-filter-actions" style="display:none;">
                            <button type="button" class="btn-muscle-apply" id="btn-apply-filter">
                                <i class="fa-solid fa-filter" style="font-size:11px;"></i>
                                Aplicar filtro
                            </button>
                            <button type="button" class="btn-muscle-clear" id="btn-clear-filter" style="display:none;">
                                Limpar filtro
                            </button>
                        </div>

                        <div id="muscle-filter-error" style="display:none; color:#ff4d6a; font-size:12px; margin-top:6px;">
                            Selecione pelo menos uma parte do corpo para ver os exercícios.
                        </div>
                    </div>

                    <div style="margin-top:1.5rem; margin-bottom:12px;">
                        <p class="section-label">EXERCÍCIOS</p>
                    </div>

                    @error('exercise_id')
                        <div style="color:#ff4d6a; font-size:12px; margin-bottom:10px;">{{ $message }}</div>
                    @enderror

                    {{-- Placeholder inicial --}}
                    <div id="exercise-list-placeholder" class="muscle-filter-placeholder" style="display:none;">
                        <i class="fa-solid fa-dumbbell" style="font-size:22px; color:rgba(255,255,255,.18); margin-bottom:10px;"></i>
                        <p>Selecione uma ou mais partes do corpo acima e clique em <strong>Aplicar filtro</strong> para ver os exercícios disponíveis.</p>
                    </div>

                    {{-- Loading --}}
                    <div id="exercise-list-loading" style="display:none;">
                        <div class="sk sk-table-row" style="margin-bottom:8px;"></div>
                        <div class="sk sk-table-row" style="margin-bottom:8px;"></div>
                        <div class="sk sk-table-row"></div>
                    </div>

                    {{-- Lista de exercícios filtrados --}}
                    <ul class="exercise-list" id="exercise-list">
                        {{-- Preenchido via JS no init --}}
                    </ul>

                    {{-- Estado vazio --}}
                    <div id="exercise-list-empty" class="muscle-filter-placeholder" style="display:none;">
                        <i class="fa-solid fa-circle-xmark" style="font-size:22px; color:rgba(255,255,255,.18); margin-bottom:10px;"></i>
                        <p>Nenhum exercício encontrado para as partes do corpo selecionadas.</p>
                    </div>

                    <div class="profile-form-row" style="margin-top:1.5rem;">
                        <button type="submit" class="btn-save" id="btn-submit">Atualizar treino</button>
                        @if(Auth::user()->isInstructor() || Auth::user()->isManager())
                            <a href="{{ route('dashboard') }}" class="btn-cancel" style="text-decoration:none;">Cancelar</a>
                        @else
                            <a href="{{ route('workouts.index') }}" class="btn-cancel" style="text-decoration:none;">Cancelar</a>
                        @endif
                    </div>

                </form>

                @if(Auth::user()->isInstructor() || Auth::user()->isManager())
                    <div id="exercise-delete-forms" style="display:none;"></div>
                @endif
            </div>
        </div>
    </div>

    @if(Auth::user()->isInstructor() || Auth::user()->isManager())
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
    @endif

    {{-- Dados do treino atual passados para o JS --}}
    <script>
        window.__workoutExercises = @json(
            $workout->workoutExercises->map(fn($we) => [
                'exercise_id' => $we->exercise_id,
                'sets'        => $we->sets,
                'reps'        => $we->reps,
                'rest_time'   => $we->rest_time,
            ])
        );
    </script>

    <style>
        /* ── Filtro por parte do corpo ── */
        .muscle-filter-wrap {
            margin-bottom: 4px;
        }

        .muscle-filter-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 12px;
        }

        .muscle-filter-loading {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .muscle-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.04);
            color: rgba(255,255,255,.58);
            font-family: 'Montserrat', sans-serif;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: background .18s, border-color .18s, color .18s, transform .12s;
            user-select: none;
        }

        .muscle-chip:hover {
            border-color: rgba(214,21,50,.38);
            background: rgba(214,21,50,.08);
            color: #fff;
            transform: translateY(-1px);
        }

        .muscle-chip.is-selected {
            border-color: rgba(214,21,50,.60);
            background: rgba(214,21,50,.18);
            color: #fff;
        }

        .muscle-chip.is-selected::before {
            content: '✓';
            font-size: 10px;
            color: #ff808b;
        }

        [data-theme="light"] .muscle-chip {
            border-color: rgba(0,0,0,.10);
            background: rgba(0,0,0,.03);
            color: rgba(0,0,0,.55);
        }

        [data-theme="light"] .muscle-chip:hover {
            border-color: rgba(214,21,50,.28);
            background: rgba(214,21,50,.06);
            color: #111;
        }

        [data-theme="light"] .muscle-chip.is-selected {
            border-color: rgba(214,21,50,.45);
            background: rgba(214,21,50,.10);
            color: #b91c1c;
        }

        .muscle-filter-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 4px;
        }

        .btn-muscle-apply {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 999px;
            border: 1px solid rgba(214,21,50,.50);
            background: rgba(214,21,50,.14);
            color: #ff808b;
            font-family: 'Montserrat', sans-serif;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: background .18s, border-color .18s, transform .12s;
        }

        .btn-muscle-apply:hover {
            background: rgba(214,21,50,.22);
            border-color: rgba(214,21,50,.70);
            transform: translateY(-1px);
        }

        .btn-muscle-clear {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.10);
            background: transparent;
            color: rgba(255,255,255,.38);
            font-family: 'Montserrat', sans-serif;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: background .18s, color .18s;
        }

        .btn-muscle-clear:hover {
            background: rgba(255,255,255,.06);
            color: rgba(255,255,255,.70);
        }

        [data-theme="light"] .btn-muscle-apply {
            background: rgba(214,21,50,.08);
            border-color: rgba(214,21,50,.30);
            color: #b91c1c;
        }

        [data-theme="light"] .btn-muscle-clear {
            border-color: rgba(0,0,0,.10);
            color: rgba(0,0,0,.40);
        }

        [data-theme="light"] .btn-muscle-clear:hover {
            background: rgba(0,0,0,.04);
            color: rgba(0,0,0,.65);
        }

        .muscle-filter-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 32px 16px;
            border: 1px dashed rgba(255,255,255,.10);
            border-radius: 14px;
            background: rgba(255,255,255,.02);
            color: rgba(255,255,255,.35);
            font-size: 13px;
            font-weight: 600;
            line-height: 1.55;
            gap: 4px;
        }

        .muscle-filter-placeholder strong {
            color: rgba(255,255,255,.55);
        }

        [data-theme="light"] .muscle-filter-placeholder {
            border-color: rgba(0,0,0,.10);
            background: rgba(0,0,0,.02);
            color: rgba(0,0,0,.38);
        }

        [data-theme="light"] .muscle-filter-placeholder strong {
            color: rgba(0,0,0,.55);
        }

        .exercise-group-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 9px;
            border-radius: 999px;
            background: rgba(214,21,50,.10);
            border: 1px solid rgba(214,21,50,.18);
            color: #ff808b;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            flex-shrink: 0;
        }

        [data-theme="light"] .exercise-group-badge {
            background: rgba(214,21,50,.07);
            border-color: rgba(214,21,50,.14);
            color: #b91c1c;
        }
    </style>

    <script>
    (() => {
        const CSRF    = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        const isStaff = {{ (Auth::user()->isInstructor() || Auth::user()->isManager()) ? 'true' : 'false' }};

        // Exercícios já salvos no treino
        const savedExercises = window.__workoutExercises ?? [];
        const savedMap = {};
        savedExercises.forEach(we => { savedMap[String(we.exercise_id)] = we; });

        // ── Elementos ──
        const chipsContainer      = document.getElementById('muscle-filter-chips');
        const filterLoading       = document.getElementById('muscle-filter-loading');
        const filterActions       = document.getElementById('muscle-filter-actions');
        const filterError         = document.getElementById('muscle-filter-error');
        const btnApply            = document.getElementById('btn-apply-filter');
        const btnClear            = document.getElementById('btn-clear-filter');
        const exerciseList        = document.getElementById('exercise-list');
        const exerciseLoading     = document.getElementById('exercise-list-loading');
        const exercisePlaceholder = document.getElementById('exercise-list-placeholder');
        const exerciseEmpty       = document.getElementById('exercise-list-empty');
        const form                = document.getElementById('workout-form');

        const GROUP_NAMES = {
            chest: 'Peito', back: 'Costas', legs: 'Pernas',
            shoulders: 'Ombros', biceps: 'Bíceps', triceps: 'Tríceps',
            abs: 'Abdômen', glutes: 'Glúteos', calves: 'Panturrilha',
            traps: 'Trapézio', forearms: 'Antebraço', cardio: 'Cardio',
        };

        let selectedGroups    = new Set();
        let filteredExerciseIds = new Set();

        // ── 1. Carregar grupos musculares ──
        async function loadMuscleGroups() {
            try {
                const res  = await fetch('/muscle-groups', {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                });
                const data = await res.json();

                filterLoading.style.display = 'none';
                filterActions.style.display = 'flex';

                if (!data.length) {
                    chipsContainer.innerHTML = '<span style="font-size:13px;color:rgba(255,255,255,.35);">Nenhum grupo muscular cadastrado.</span>';
                    // Mostrar os exercícios do treino mesmo assim
                    renderSavedExercises();
                    return;
                }

                data.forEach(({ value, label }) => {
                    const chip = document.createElement('button');
                    chip.type = 'button';
                    chip.className = 'muscle-chip';
                    chip.dataset.group = value;
                    chip.textContent = label || GROUP_NAMES[value] || value;
                    chip.addEventListener('click', () => toggleChip(chip, value));
                    chipsContainer.appendChild(chip);
                });

                // No edit: carregar todos os exercícios automaticamente para mostrar os já salvos
                selectAllChips();
                await applyFilter(true);

            } catch (e) {
                filterLoading.innerHTML = '<span style="color:#ff4d6a;font-size:12px;">Erro ao carregar grupos. Recarregue a página.</span>';
                // Fallback: renderizar ao menos os exercícios salvos sem filtro
                renderSavedExercises();
            }
        }

        function toggleChip(chip, value) {
            if (selectedGroups.has(value)) {
                selectedGroups.delete(value);
                chip.classList.remove('is-selected');
            } else {
                selectedGroups.add(value);
                chip.classList.add('is-selected');
            }
            filterError.style.display = 'none';
        }

        function selectAllChips() {
            chipsContainer.querySelectorAll('.muscle-chip').forEach(chip => {
                chip.classList.add('is-selected');
                selectedGroups.add(chip.dataset.group);
            });
        }

        function clearChips() {
            selectedGroups.clear();
            chipsContainer.querySelectorAll('.muscle-chip').forEach(c => c.classList.remove('is-selected'));
        }

        // ── 2. Aplicar filtro ──
        btnApply.addEventListener('click', () => {
            if (selectedGroups.size === 0) {
                filterError.style.display = 'block';
                return;
            }
            filterError.style.display = 'none';
            applyFilter(false);
        });

        btnClear.addEventListener('click', () => {
            clearChips();
            exerciseList.style.display = 'none';
            exerciseEmpty.style.display = 'none';
            exercisePlaceholder.style.display = 'flex';
            btnClear.style.display = 'none';
            filteredExerciseIds.clear();
        });

        async function applyFilter(isInit) {
            exercisePlaceholder.style.display = 'none';
            exerciseEmpty.style.display = 'none';
            exerciseList.style.display = 'none';
            exerciseLoading.style.display = 'block';

            try {
                const body = new URLSearchParams();
                body.append('_token', CSRF);
                selectedGroups.forEach(g => body.append('muscle_groups[]', g));

                const res  = await fetch('/exercises/filter', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body,
                });
                const data = await res.json();

                exerciseLoading.style.display = 'none';
                filteredExerciseIds.clear();

                const exercises = [];
                Object.values(data.exercises ?? {}).forEach(group => group.forEach(ex => exercises.push(ex)));

                if (!exercises.length) {
                    exerciseEmpty.style.display = 'flex';
                    btnClear.style.display = 'inline-flex';
                    return;
                }

                renderExercises(exercises, isInit);
                exerciseList.style.display = 'flex';
                btnClear.style.display = 'inline-flex';

            } catch (e) {
                exerciseLoading.style.display = 'none';
                exercisePlaceholder.style.display = 'flex';
                exercisePlaceholder.innerHTML = '<i class="fa-solid fa-circle-exclamation" style="font-size:22px;color:#ff4d6a;margin-bottom:10px;"></i><p>Erro ao carregar exercícios. Tente novamente.</p>';
                exercisePlaceholder.style.display = 'flex';
                // Fallback: renderizar apenas os salvos
                if (isInit) renderSavedExercises();
            }
        }

        // ── 3. Renderizar exercícios ──
        function renderExercises(exercises, isInit) {
            exerciseList.innerHTML = '';

            // old() via PHP para erro de validação
            const oldIds      = new Set(@json(old('exercise_id', [])).map(String));
            const oldSets     = @json(old('sets', []));
            const oldReps     = @json(old('reps', []));
            const oldRestTime = @json(old('rest_time', []));
            const hasOld      = oldIds.size > 0;

            exercises.forEach(ex => {
                filteredExerciseIds.add(String(ex.id));

                const saved      = savedMap[String(ex.id)];
                const isChecked  = hasOld ? oldIds.has(String(ex.id)) : !!saved;
                const setVal     = hasOld ? (oldSets[ex.id]     ?? '') : (saved?.sets      ?? '');
                const repVal     = hasOld ? (oldReps[ex.id]     ?? '') : (saved?.reps      ?? '');
                const restVal    = hasOld ? (oldRestTime[ex.id] ?? '') : (saved?.rest_time ?? '');
                const groupLabel = GROUP_NAMES[ex.muscle_group] ?? ex.muscle_group_pt ?? ex.muscle_group ?? '';
                const isIncluded = !!saved;

                const li = document.createElement('li');
                li.className = `exercise-card${isIncluded ? ' exercise-card--active' : ''}`;
                li.style.cssText = 'flex-direction:column; align-items:flex-start; gap:10px;';

                li.innerHTML = `
                    <div class="workout-exercise-head">
                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; min-width:0;">
                            <input type="checkbox"
                                   name="exercise_id[]"
                                   value="${ex.id}"
                                   ${isChecked ? 'checked' : ''}
                                   style="accent-color:var(--red); width:16px; height:16px; cursor:pointer;">
                            <div class="exercise-thumb" style="width:40px; height:40px; flex-shrink:0;">
                                <svg viewBox="0 0 24 24">
                                    <rect x="2" y="9" width="4" height="6" rx="1"/>
                                    <rect x="18" y="9" width="4" height="6" rx="1"/>
                                    <rect x="7" y="11" width="10" height="2" rx="1"/>
                                </svg>
                            </div>
                            <span class="exercise-name">${escHtml(ex.name)}</span>
                            ${groupLabel ? `<span class="exercise-group-badge">${escHtml(groupLabel)}</span>` : ''}
                            ${isIncluded ? '<span class="chip chip--series" style="margin-left:auto;">Incluído</span>' : ''}
                        </label>
                        ${isStaff ? `
                        <button type="button"
                                class="workout-exercise-delete"
                                data-exercise-id="${ex.id}"
                                data-exercise-name="${escAttr(ex.name)}"
                                title="Apagar exercício"
                                aria-label="Apagar exercício ${escAttr(ex.name)}">
                            <i class="fa-solid fa-trash"></i>
                        </button>` : ''}
                    </div>
                    <div class="workout-inputs">
                        <input type="number" name="sets[${ex.id}]"      value="${escHtml(String(setVal))}"  placeholder="Séries"       class="workout-input-sm" min="1">
                        <input type="number" name="reps[${ex.id}]"      value="${escHtml(String(repVal))}"  placeholder="Reps"         class="workout-input-sm" min="1">
                        <input type="number" name="rest_time[${ex.id}]" value="${escHtml(String(restVal))}" placeholder="Descanso (s)" class="workout-input-sm" min="1">
                    </div>
                `;

                exerciseList.appendChild(li);

                if (isStaff) {
                    const wrap = document.getElementById('exercise-delete-forms');
                    if (wrap && !document.getElementById(`delete-exercise-${ex.id}`)) {
                        const f = document.createElement('form');
                        f.id     = `delete-exercise-${ex.id}`;
                        f.action = `/exercises/${ex.id}`;
                        f.method = 'POST';
                        f.innerHTML = `<input type="hidden" name="_token" value="${CSRF}"><input type="hidden" name="_method" value="DELETE">`;
                        wrap.appendChild(f);
                    }
                }
            });

            if (isStaff) bindDeleteButtons();
        }

        // Fallback: renderizar só os exercícios salvos (sem acesso ao endpoint de filtro)
        function renderSavedExercises() {
            if (!savedExercises.length) return;
            exercisePlaceholder.style.display = 'none';
            exerciseList.style.display = 'flex';
            savedExercises.forEach(we => {
                filteredExerciseIds.add(String(we.exercise_id));
                const li = document.createElement('li');
                li.className = 'exercise-card exercise-card--active';
                li.style.cssText = 'flex-direction:column; align-items:flex-start; gap:10px;';
                li.innerHTML = `
                    <div class="workout-exercise-head">
                        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; min-width:0;">
                            <input type="checkbox" name="exercise_id[]" value="${we.exercise_id}" checked
                                   style="accent-color:var(--red); width:16px; height:16px; cursor:pointer;">
                            <div class="exercise-thumb" style="width:40px; height:40px; flex-shrink:0;">
                                <svg viewBox="0 0 24 24">
                                    <rect x="2" y="9" width="4" height="6" rx="1"/>
                                    <rect x="18" y="9" width="4" height="6" rx="1"/>
                                    <rect x="7" y="11" width="10" height="2" rx="1"/>
                                </svg>
                            </div>
                            <span class="exercise-name">Exercício #${we.exercise_id}</span>
                            <span class="chip chip--series" style="margin-left:auto;">Incluído</span>
                        </label>
                    </div>
                    <div class="workout-inputs">
                        <input type="number" name="sets[${we.exercise_id}]"      value="${we.sets      ?? ''}" placeholder="Séries"       class="workout-input-sm" min="1">
                        <input type="number" name="reps[${we.exercise_id}]"      value="${we.reps      ?? ''}" placeholder="Reps"         class="workout-input-sm" min="1">
                        <input type="number" name="rest_time[${we.exercise_id}]" value="${we.rest_time ?? ''}" placeholder="Descanso (s)" class="workout-input-sm" min="1">
                    </div>
                `;
                exerciseList.appendChild(li);
            });
        }

        // ── 4. Validação no submit ──
        form.addEventListener('submit', (e) => {
            if (filteredExerciseIds.size === 0) {
                e.preventDefault();
                filterError.textContent = 'Selecione pelo menos uma parte do corpo e aplique o filtro antes de salvar.';
                filterError.style.display = 'block';
                filterError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        // ── 5. Modal de delete ──
        @if(Auth::user()->isInstructor() || Auth::user()->isManager())
        const modal      = document.getElementById('exercise-delete-modal');
        const nameEl     = document.getElementById('exercise-delete-name');
        const cancelBtn  = document.getElementById('exercise-delete-cancel');
        const confirmBtn = document.getElementById('exercise-delete-confirm');
        let pendingDeleteForm = null;

        function bindDeleteButtons() {
            document.querySelectorAll('.workout-exercise-delete[data-exercise-id]').forEach(btn => {
                btn.removeEventListener('click', onDeleteClick);
                btn.addEventListener('click', onDeleteClick);
            });
        }

        function onDeleteClick() {
            const id   = this.dataset.exerciseId;
            const name = this.dataset.exerciseName || 'este exercício';
            pendingDeleteForm = document.getElementById(`delete-exercise-${id}`);
            nameEl.textContent = name;
            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            cancelBtn.focus();
        }

        function closeModal() {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            pendingDeleteForm = null;
        }

        cancelBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape' && modal.style.display === 'flex') closeModal(); });
        confirmBtn.addEventListener('click', () => { if (pendingDeleteForm) pendingDeleteForm.submit(); });
        @endif

        // ── Helpers ──
        function escHtml(str) {
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }
        function escAttr(str) {
            return String(str).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }

        // ── Init ──
        loadMuscleGroups();
    })();
    </script>

</x-app-layout>