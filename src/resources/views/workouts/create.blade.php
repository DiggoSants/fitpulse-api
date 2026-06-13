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
                    <h1 class="workout-form-title">Criar Treino</h1>
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

                <form action="{{ route('workouts.store') }}" method="POST" id="workout-form">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">

                    <div class="profile-field">
                        <label>Nome do treino</label>
                        <input type="text" name="name" placeholder="Ex: Treino A" value="{{ old('name') }}">
                        @error('name')
                            <span style="color:#ff4d6a; font-size:12px;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="profile-field">
                        <label>Dia da agenda</label>
                        @if(count($scheduleDays) < $minScheduleDays)
                            <div class="weekly-validation weekly-validation--error" style="margin-bottom:10px;">
                                Defina a agenda semanal do aluno antes de criar o treino.
                            </div>
                        @endif
                        <div class="workout-day-options">
                            @forelse($scheduleDays as $dayKey)
                                <label class="weekly-day-option {{ old('week_day') === $dayKey ? 'is-selected' : '' }}">
                                    <input type="radio" name="week_day" value="{{ $dayKey }}" @checked(old('week_day') === $dayKey)>
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
                            {{-- Chips carregados via JS --}}
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

                    <div class="workout-form-tools" style="margin-top:1rem;">
                        @if(Auth::user()->isInstructor() || Auth::user()->isManager())
                            <a href="{{ route('exercises.create', ['student_id' => $student->id]) }}">
                                + Adicionar novo exercício
                            </a>
                        @endif
                        <a href="{{ route('exercises.index', ['student_id' => $student->id]) }}">
                            Biblioteca de exercícios <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>

                    <div style="margin-top:1.5rem; margin-bottom:12px;">
                        <p class="section-label">EXERCÍCIOS</p>
                    </div>

                    @error('exercise_id')
                        <div style="color:#ff4d6a; font-size:12px; margin-bottom:10px;">{{ $message }}</div>
                    @enderror

                    {{-- Estado inicial: instrução para aplicar filtro --}}
                    <div id="exercise-list-placeholder" class="muscle-filter-placeholder">
                        <i class="fa-solid fa-dumbbell" style="font-size:22px; color:rgba(255,255,255,.18); margin-bottom:10px;"></i>
                        <p>Selecione uma ou mais partes do corpo acima e clique em <strong>Aplicar filtro</strong> para ver os exercícios disponíveis.</p>
                    </div>

                    {{-- Estado de carregamento --}}
                    <div id="exercise-list-loading" style="display:none;">
                        <div class="sk sk-table-row" style="margin-bottom:8px;"></div>
                        <div class="sk sk-table-row" style="margin-bottom:8px;"></div>
                        <div class="sk sk-table-row"></div>
                    </div>

                    {{-- Lista de exercícios filtrados --}}
                    <ul class="exercise-list" id="exercise-list" style="display:none;">
                        {{-- Preenchido via JS --}}
                    </ul>

                    {{-- Estado vazio (nenhum exercício encontrado) --}}
                    <div id="exercise-list-empty" class="muscle-filter-placeholder" style="display:none;">
                        <i class="fa-solid fa-circle-xmark" style="font-size:22px; color:rgba(255,255,255,.18); margin-bottom:10px;"></i>
                        <p>Nenhum exercício encontrado para as partes do corpo selecionadas.</p>
                    </div>

                    <div class="profile-form-row" style="margin-top:1.5rem;">
                        <button type="submit" class="btn-save" id="btn-submit">Salvar treino</button>
                        @if(Auth::user()->isInstructor() || Auth::user()->isManager())
                            <a href="{{ route('dashboard') }}" class="btn-cancel" style="text-decoration:none;">Cancelar</a>
                        @else
                            <a href="{{ route('workouts.index') }}" class="btn-cancel" style="text-decoration:none;">Cancelar</a>
                        @endif
                    </div>

                </form>

                @if(Auth::user()->isInstructor() || Auth::user()->isManager())
                    <div id="exercise-delete-forms" style="display:none;">
                        {{-- Forms de delete injetados via JS --}}
                    </div>
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

        /* ── Placeholder / vazio ── */
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

        /* ── Badge do grupo muscular dentro do card ── */
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
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        const isStaff = {{ (Auth::user()->isInstructor() || Auth::user()->isManager()) ? 'true' : 'false' }};

        // ── Elementos ──
        const chipsContainer   = document.getElementById('muscle-filter-chips');
        const filterLoading    = document.getElementById('muscle-filter-loading');
        const filterActions    = document.getElementById('muscle-filter-actions');
        const filterError      = document.getElementById('muscle-filter-error');
        const btnApply         = document.getElementById('btn-apply-filter');
        const btnClear         = document.getElementById('btn-clear-filter');
        const exerciseList     = document.getElementById('exercise-list');
        const exerciseLoading  = document.getElementById('exercise-list-loading');
        const exercisePlaceholder = document.getElementById('exercise-list-placeholder');
        const exerciseEmpty    = document.getElementById('exercise-list-empty');
        const form             = document.getElementById('workout-form');

        // Nomes em PT dos grupos musculares (espelha o backend)
        const GROUP_NAMES = {
            chest: 'Peito', back: 'Costas', legs: 'Pernas',
            shoulders: 'Ombros', biceps: 'Bíceps', triceps: 'Tríceps',
            abs: 'Abdômen', glutes: 'Glúteos', calves: 'Panturrilha',
            traps: 'Trapézio', forearms: 'Antebraço', cardio: 'Cardio',
        };

        let selectedGroups = new Set();
        let filteredExerciseIds = new Set(); // IDs visíveis após filtro

        // ── 1. Carregar grupos musculares disponíveis ──
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

                // Pré-selecionar grupos dos exercícios salvos via old() se houver
                @if(old('exercise_id'))
                    // Se tiver old input (erro de validação), recarregar todos os exercícios
                    selectAllChips();
                    applyFilter(true);
                @endif

            } catch (e) {
                filterLoading.innerHTML = '<span style="color:#ff4d6a;font-size:12px;">Erro ao carregar grupos. Recarregue a página.</span>';
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

        async function applyFilter(restoreOld) {
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

                // Achatar exercícios agrupados
                const exercises = [];
                Object.values(data.exercises ?? {}).forEach(group => group.forEach(ex => exercises.push(ex)));

                if (!exercises.length) {
                    exerciseEmpty.style.display = 'flex';
                    btnClear.style.display = 'inline-flex';
                    return;
                }

                renderExercises(exercises, restoreOld);
                exerciseList.style.display = 'flex';
                btnClear.style.display = 'inline-flex';

            } catch (e) {
                exerciseLoading.style.display = 'none';
                exercisePlaceholder.style.display = 'flex';
                exercisePlaceholder.innerHTML = '<i class="fa-solid fa-circle-exclamation" style="font-size:22px;color:#ff4d6a;margin-bottom:10px;"></i><p>Erro ao carregar exercícios. Tente novamente.</p>';
            }
        }

        // ── 3. Renderizar exercícios ──
        function renderExercises(exercises, restoreOld) {
            exerciseList.innerHTML = '';

            // IDs selecionados via old() (após erro de validação)
            const oldIds = new Set(@json(old('exercise_id', [])).map(String));
            const oldSets     = @json(old('sets', []));
            const oldReps     = @json(old('reps', []));
            const oldRestTime = @json(old('rest_time', []));

            exercises.forEach(ex => {
                filteredExerciseIds.add(String(ex.id));

                const li = document.createElement('li');
                li.className = 'exercise-card';
                li.style.cssText = 'flex-direction:column; align-items:flex-start; gap:10px;';

                const isChecked  = restoreOld && oldIds.has(String(ex.id));
                const setVal     = oldSets[ex.id]     ?? '';
                const repVal     = oldReps[ex.id]     ?? '';
                const restVal    = oldRestTime[ex.id] ?? '';
                const groupLabel = GROUP_NAMES[ex.muscle_group] ?? ex.muscle_group_pt ?? ex.muscle_group ?? '';

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
                            <span class="exercise-name" style="margin-bottom:0;">${escHtml(ex.name)}</span>
                            ${groupLabel ? `<span class="exercise-group-badge">${escHtml(groupLabel)}</span>` : ''}
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
                        <input type="number" name="sets[${ex.id}]"     value="${escHtml(String(setVal))}"  placeholder="Séries"       class="workout-input-sm" min="1">
                        <input type="number" name="reps[${ex.id}]"     value="${escHtml(String(repVal))}"  placeholder="Reps"         class="workout-input-sm" min="1">
                        <input type="number" name="rest_time[${ex.id}]" value="${escHtml(String(restVal))}" placeholder="Descanso (s)" class="workout-input-sm" min="1">
                    </div>
                `;

                exerciseList.appendChild(li);

                // Injetar form de delete para staff
                if (isStaff) {
                    const deleteFormsWrap = document.getElementById('exercise-delete-forms');
                    if (deleteFormsWrap && !document.getElementById(`delete-exercise-${ex.id}`)) {
                        const f = document.createElement('form');
                        f.id     = `delete-exercise-${ex.id}`;
                        f.action = `/exercises/${ex.id}`;
                        f.method = 'POST';
                        f.innerHTML = `<input type="hidden" name="_token" value="${CSRF}"><input type="hidden" name="_method" value="DELETE">`;
                        deleteFormsWrap.appendChild(f);
                    }
                }
            });

            // Re-bind delete buttons
            if (isStaff) bindDeleteButtons();
        }

        // ── 4. Validação no submit ──
        form.addEventListener('submit', (e) => {
            if (filteredExerciseIds.size === 0) {
                e.preventDefault();
                filterError.textContent = 'Selecione pelo menos uma parte do corpo e aplique o filtro antes de salvar.';
                filterError.style.display = 'block';
                filterError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            // Garantir que apenas checkboxes de exercícios visíveis sejam enviados
            // (os não-visíveis não existem no DOM — sem necessidade de remoção extra)
        });

        // ── 5. Modal de delete ──
        @if(Auth::user()->isInstructor() || Auth::user()->isManager())
        const modal     = document.getElementById('exercise-delete-modal');
        const nameEl    = document.getElementById('exercise-delete-name');
        const cancelBtn = document.getElementById('exercise-delete-cancel');
        const confirmBtn= document.getElementById('exercise-delete-confirm');
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