<x-app-layout>
  @vite(['resources/css/app.css','resources/js/app.js'])

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div style="margin-bottom:16px; padding:12px 16px; background:rgba(74,222,128,0.08); border:1px solid rgba(74,222,128,0.2); border-radius:10px; color:#4ade80; font-size:13px; font-weight:600;">
                {{ session('success') }}
            </div>
            @endif

            {{-- VISÃO DO GERENTE --}}
           @if(Auth::user()->isManager())

<div class="dash-hero">
    <div class="dash-hero__ring"></div>
    <div class="dash-hero__inner">
        <div>
            <div class="dash-hero__eyebrow">Gerenciamento</div>
            <h2 class="dash-hero__title">Painel Geral</h2>
            <p class="dash-hero__sub">Visão completa de instrutores e alunos</p>
        </div>
        <div class="dash-hero__right">
            <span class="dash-hero__pulse">
                <span class="dash-hero__pulse-dot"></span>
                GERENTE
            </span>
            <a href="{{ route('instructors.create') }}" class="btn-save"
                style="text-decoration:none; display:inline-flex; align-items:center; gap:7px;">
                + Novo Instrutor
            </a>
        </div>
    </div>
</div>

<div class="mgr-grid">
    @forelse($instructors as $instructor)
    <div class="mgr-card">

        {{-- esquerda--}}
        <div class="mgr-card__left">
            <div class="mgr-card__left-top">

                <div class="mgr-inst-big-avatar">
                    {{ mb_strtoupper(mb_substr($instructor->user->name, 0, 2)) }}
                </div>

                <div class="mgr-inst-label">Instrutor</div>
                <div class="mgr-inst-name">{{ $instructor->user->name }}</div>

                <span class="mgr-inst-specialty">
                    <svg viewBox="0 0 24 24">
                        <rect x="2"  y="10" width="3"  height="4" rx="1"/>
                        <rect x="19" y="10" width="3"  height="4" rx="1"/>
                        <rect x="5"  y="8"  width="3"  height="8" rx="1"/>
                        <rect x="16" y="8"  width="3"  height="8" rx="1"/>
                        <rect x="8"  y="11" width="8"  height="2" rx="1"/>
                    </svg>
                    {{ !empty($instructor->specialty) ? $instructor->specialty : 'Personal Trainer' }}
                </span>

                <div class="mgr-inst-badge">{{ $instructor->students->count() }} aluno(s)</div>
            </div>

            <div class="mgr-inst-divider"></div>

            <a href="{{ route('instructors.edit', $instructor->id) }}" class="mgr-inst-edit">
                <svg width="12" height="12" viewBox="0 0 14 14" fill="none"
                    style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;">
                    <path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/>
                </svg>
                Editar
            </a>
        </div>

        {{--DIREITA: alunos--}}
        <div class="mgr-card__right">
            <div class="mgr-right-header">
                <span class="mgr-right-title">Alunos</span>
                <span class="mgr-right-count">{{ $instructor->students->count() }} vinculado(s)</span>
            </div>

            @forelse($instructor->students as $student)
            <div class="mgr-student">

                <div class="mgr-student-row">
                    <div class="mgr-student-av">{{ mb_strtoupper(mb_substr($student->user->name, 0, 2)) }}</div>
                    <div class="mgr-student-info">
                        <div class="mgr-student-name">{{ $student->user->name }}</div>
                        <div class="mgr-student-email">{{ $student->user->email }}</div>
                    </div>
                    <div class="mgr-student-right">
                        @if($student->is_defaulter)
                            <span class="mgr-badge-bad">Devedor</span>
                        @else
                            <span class="mgr-badge-ok">Em dia</span>
                        @endif
                        <a href="{{ route('workouts.create', ['student_id' => $student->id]) }}" class="mgr-btn-criar">
                            + Criar treino
                        </a>
                    </div>
                </div>

                @forelse($student->workouts as $workout)
                <div class="mgr-workouts">

                    {{-- Linha do treino --}}
                    <div class="mgr-workout-row">
                        <div class="mgr-workout-icon">
                            <svg viewBox="0 0 24 24">
                                <rect x="2"  y="10" width="3"  height="4" rx="1"/>
                                <rect x="19" y="10" width="3"  height="4" rx="1"/>
                                <rect x="5"  y="8"  width="3"  height="8" rx="1"/>
                                <rect x="16" y="8"  width="3"  height="8" rx="1"/>
                                <rect x="8"  y="11" width="8"  height="2" rx="1"/>
                            </svg>
                        </div>

                        <span class="mgr-workout-name">{{ $workout->name }}</span>

                        <div class="mgr-workout-btns">

                            <button type="button"
                                class="mgr-btn-view"
                                onclick="toggleWorkoutMgr('workout-{{ $workout->id }}', this)">
                                <svg class="icon-eye-open" viewBox="0 0 24 24">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <span class="btn-view-label">Ver exercícios</span>
                            </button>

                            <a href="{{ route('workouts.edit', [$workout->id, 'student_id' => $student->id]) }}"
                                class="mgr-btn-sm mgr-btn-edit-workout">
                                <svg width="10" height="10" viewBox="0 0 14 14" fill="none"
                                    style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; display:inline-block; vertical-align:middle; margin-right:2px;">
                                    <path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/>
                                </svg>
                                Editar
                            </a>

                            <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                <button type="submit" class="mgr-btn-del" title="Deletar treino">
                                    <svg width="11" height="11" viewBox="0 0 14 16" fill="none"
                                        style="stroke:#f87171; stroke-width:1.8; stroke-linecap:round;">
                                        <path d="M1 3.5h12M4.5 3.5V2a.5.5 0 01.5-.5h3a.5.5 0 01.5.5v1.5M5.5 7v5M8.5 7v5M2.5 3.5l.9 10a.5.5 0 00.5.5h6.2a.5.5 0 00.5-.5l.9-10"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Tabela de exercícios colapsável --}}
                    <div id="workout-{{ $workout->id }}" style="display:none;">
                        @if($workout->workoutExercises->count())
                        <div class="mgr-exercises-wrap">

                           
                            <div class="mgr-ex-head">
                                <span>Exercício</span>
                                <span>Grupo Musc.</span>
                                <span>Séries</span>
                                <span>Reps</span>
                                <span>Desc.</span>
                            </div>

                            @foreach($workout->workoutExercises as $we)
                          
                            <div class="mgr-ex-row">
                                <span class="mgr-ex-name">{{ $we->exercise->name }}</span>
                                <span class="mgr-ex-group">{{ $we->exercise->muscle_group ?? '—' }}</span>
                                <span><span class="chip-xs chip-xs--s">{{ $we->sets }}x</span></span>
                                <span><span class="chip-xs chip-xs--r">{{ $we->reps }}</span></span>
                                <span><span class="chip-xs chip-xs--t">{{ $we->rest_time ?? 0 }}s</span></span>
                            </div>
                            @endforeach

                        </div>
                        @else
                        <div class="mgr-no-workouts">Nenhum exercício neste treino.</div>
                        @endif
                    </div>

                </div>
                @empty
                <div class="mgr-no-workouts">Nenhum treino cadastrado.</div>
                @endforelse

            </div>
            @empty
            <div class="mgr-no-students">Nenhum aluno vinculado.</div>
            @endforelse
        </div>

    </div>
    @empty
    <div class="empty-state"><p>Nenhum instrutor cadastrado.</p></div>
    @endforelse
</div>

            {{-- VISÃO DO INSTRUTOR--}}
            @elseif(Auth::user()->isInstructor())

            <div class="dash-hero">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Bem-vindo de volta</div>
                        <h2 class="dash-hero__title">Meus Alunos</h2>
                        <p class="dash-hero__sub">{{ $instructor->specialty ?? 'Instrutor' }}</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            INSTRUTOR
                        </span>
                    </div>
                </div>
            </div>

            <div class="invite-box">
                <div>
                    <p class="invite-code-label">Seu código de convite</p>
                    <p class="invite-code">{{ $instructor->invite_code ?? '—' }}</p>
                </div>
                <form action="{{ route('instructors.regenerate-code', $instructor->id) }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn-ghost">Regenerar código</button>
                </form>
            </div>

            <div class="students-grid">
                @forelse($instructor->students as $student)
                <div class="student-card {{ $student->is_defaulter ? 'student-card--bad' : 'student-card--ok' }}">

                    <div class="student-card__header">
                        <div class="student-avatar">{{ mb_substr($student->user->name, 0, 2) }}</div>
                        <div style="flex:1; min-width:0;">
                            <p class="student-card__name">{{ $student->user->name }}</p>
                            <p class="student-card__email">{{ $student->user->email }}</p>
                        </div>
                        @if($student->is_defaulter)
                        <span class="badge-devedor badge-devedor--sim">Devedor</span>
                        @else
                        <span class="badge-devedor badge-devedor--nao">Em dia</span>
                        @endif
                    </div>

                    <div class="student-card__workouts">
                        @forelse($student->workouts as $workout)
                        <div class="workout-block">
                            <div class="workout-block__name">
                                {{ $workout->name }}
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span>{{ $workout->workoutExercises->count() }} exerc.</span>
                                    <button
                                        type="button"
                                        class="btn-workout-action"
                                        style="font-size:11px; padding:4px 12px;"
                                        onclick="toggleWorkout('workout-inst-{{ $workout->id }}')"
                                        id="btn-workout-inst-{{ $workout->id }}"
                                    >
                                        Ver exercícios ▾
                                    </button>
                                </div>
                            </div>

                            <div id="workout-inst-{{ $workout->id }}" style="display:none; margin-top:10px;">
                                @if($workout->workoutExercises->count())
                                <div class="ex-table">
                                    <div class="ex-table__head">
                                        <span>Exercício</span>
                                        <span>Grupo</span>
                                        <span>Séries</span>
                                        <span>Reps</span>
                                        <span>Desc.</span>
                                    </div>
                                    @foreach($workout->workoutExercises as $we)
                                    <div class="ex-table__row">
                                        <span class="ex-table__name">{{ $we->exercise->name }}</span>
                                        <span class="ex-table__group">{{ $we->exercise->muscle_group ?? '—' }}</span>
                                        <span><span class="chip chip--series">{{ $we->sets }}</span></span>
                                        <span><span class="chip chip--reps">{{ $we->reps }}</span></span>
                                        <span><span class="chip chip--rest">{{ $we->rest_time ?? 0 }}s</span></span>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <p style="font-size:13px; color:var(--text-muted); opacity:.6;">Nenhum exercício neste treino.</p>
                                @endif
                            </div>

                            <div style="display:flex; gap:8px; margin-top:12px; flex-wrap:wrap;">
                                <a href="{{ route('workouts.edit', [$workout->id, 'student_id' => $student->id]) }}"
                                   class="btn-workout-action">
                                    <svg width="11" height="11" viewBox="0 0 14 14" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/>
                                    </svg>
                                    Editar
                                </a>
                                <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                    <button type="submit" class="btn-workout-action" style="border-color:rgba(214,21,50,.6); color:#f87171;">
                                        🗑 Deletar
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="workout-empty">Nenhum treino cadastrado.</div>
                        @endforelse
                    </div>

                    <div style="padding:14px 16px; border-top:1px solid rgba(255,255,255,.06); display:flex; justify-content:flex-end;">
                        <a href="{{ route('workouts.create', ['student_id' => $student->id]) }}"
                           class="btn-save" style="text-decoration:none; font-size:12px; padding:7px 16px;">
                            + Criar treino
                        </a>
                    </div>
                </div>
                @empty
                <div class="inst-empty" style="grid-column:1/-1;">Nenhum aluno vinculado a você.</div>
                @endforelse
            </div>

            {{-- VISÃO DO ALUNO SEM MATRÍCULA--}}
            @elseif(isset($enrolled) && !$enrolled)

            <div class="dash-hero">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Bem-vindo ao FitPulse</div>
                        <h2 class="dash-hero__title">Acesso Limitado</h2>
                        <p class="dash-hero__sub">Faça sua matrícula para acessar todas as funcionalidades.</p>
                    </div>
                    <div class="dash-hero__right">
                        <a href="{{ route('enrollment.index') }}" class="btn-save"
                            style="text-decoration:none; display:inline-flex; align-items:center; gap:7px;">
                            Matricular-se
                        </a>
                    </div>
                </div>
            </div>

            <div class="empty-state" style="padding:4rem 1rem;">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none"
                    style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 18px; display:block; opacity:.20;">
                    <rect x="3" y="11" width="18" height="11" rx="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                <p>Você ainda não possui uma matrícula ativa.</p>
                <p style="font-size:13px; margin-top:6px; opacity:.45;">Escolha um plano para liberar o acesso completo.</p>
                <a href="{{ route('enrollment.index') }}" class="btn-save" style="text-decoration:none; display:inline-block; margin-top:20px;">
                    Ver Planos
                </a>
            </div>

            {{-- VISÃO DO ALUNO COM MATRÍCULA --}}
            @else

            <div class="dash-hero">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Bem-vindo de volta</div>
                        <h2 class="dash-hero__title">Seu Treino</h2>
                        <p class="dash-hero__sub">Pronto para mais um dia?</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            FITPULSE ATIVO
                        </span>
                        <a href="{{ route('workouts.create') }}" class="btn-save"
                            style="text-decoration:none; display:inline-flex; align-items:center; gap:7px;">
                            <svg width="11" height="11" viewBox="0 0 12 12" fill="none"
                                style="stroke:#fff; stroke-width:2.5; stroke-linecap:round;">
                                <line x1="6" y1="1" x2="6" y2="11" />
                                <line x1="1" y1="6" x2="11" y2="6" />
                            </svg>
                            Criar Treino
                        </a>
                    </div>
                </div>
            </div>

            @if(isset($workout))

            <div class="dash-stats">
                <div class="dash-stat dash-stat--red">
                    <div class="dash-stat__bg-icon">⚡</div>
                    <div class="dash-stat__header"><span class="dash-stat__dot"></span><span class="dash-stat__label">Séries totais</span></div>
                    <div class="dash-stat__value">{{ $exercises->sum(fn($e) => (int) $e->sets) }}</div>
                </div>
                <div class="dash-stat dash-stat--blue">
                    <div class="dash-stat__bg-icon">🔁</div>
                    <div class="dash-stat__header"><span class="dash-stat__dot"></span><span class="dash-stat__label">Reps totais</span></div>
                    <div class="dash-stat__value">{{ $exercises->sum(fn($e) => (int) $e->reps) }}</div>
                </div>
                <div class="dash-stat dash-stat--green">
                    <div class="dash-stat__bg-icon">🏋️</div>
                    <div class="dash-stat__header"><span class="dash-stat__dot"></span><span class="dash-stat__label">Descanso</span></div>
                    <div class="dash-stat__value">{{ $exercises->sum(fn($e) => (int) $e->rest_time) }}</div>
                </div>
            </div>

            <div class="exercises-header">
                <div class="exercises-header__left">
                    <span class="exercises-header__tag">Treino atual</span>
                    <h3 class="exercises-header__name">{{ $workout->name }}</h3>
                    <span class="exercises-header__badge">{{ $exercises->count() }} exerc.</span>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <a href="{{ route('workouts.edit', $workout->id) }}" class="btn-ghost">
                        <svg viewBox="0 0 14 14" fill="none">
                            <path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z" />
                        </svg>
                        Editar
                    </a>
                    <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-del">
                            <svg viewBox="0 0 14 16" fill="none">
                                <path d="M1 3.5h12M4.5 3.5V2a.5.5 0 01.5-.5h3a.5.5 0 01.5.5v1.5M5.5 7v5M8.5 7v5M2.5 3.5l.9 10a.5.5 0 00.5.5h6.2a.5.5 0 00.5-.5l.9-10" />
                            </svg>
                            Deletar
                        </button>
                    </form>
                </div>
            </div>

            @if($exercises->count())
            <ul class="exercise-grid">
                @foreach($exercises as $item)
                <li class="exercise-grid-card">
                    <div class="exercise-grid-card__thumb">
                        @if(!empty($item->exercise->image_url))
                        <img src="{{ $item->exercise->image_url }}" alt="{{ $item->exercise->name }}">
                        @else
                        <div class="exercise-grid-card__thumb-placeholder">
                            <svg viewBox="0 0 24 24">
                                <rect x="2" y="9" width="4" height="6" rx="1" />
                                <rect x="18" y="9" width="4" height="6" rx="1" />
                                <rect x="7" y="11" width="10" height="2" rx="1" />
                            </svg>
                            <span>{{ $item->exercise->muscle_group ?? 'Exercício' }}</span>
                        </div>
                        @endif
                        <span class="exercise-grid-card__num">{{ $loop->iteration }}</span>
                    </div>
                    <div class="exercise-grid-card__body">
                        <div class="exercise-grid-card__name">{{ $item->exercise->name }}</div>
                        <div class="chips">
                            <span class="chip chip--series">{{ $item->sets }} séries</span>
                            <span class="chip chip--reps">{{ $item->reps }} reps</span>
                            <span class="chip chip--rest">{{ $item->rest_time ?? 0 }}s</span>
                        </div>
                    </div>
                    <div class="exercise-grid-card__footer">
                        <span style="font-size:11px; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.07em;">
                            {{ $item->exercise->muscle_group ?? '' }}
                        </span>
                        <button class="btn-play" title="Iniciar">
                            <svg viewBox="0 0 10 12">
                                <polygon points="0,0 10,6 0,12" />
                            </svg>
                        </button>
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <div class="empty-state">
                <p>Nenhum exercício encontrado.</p>
            </div>
            @endif

            @else
            <div class="empty-state" style="padding:5rem 1rem;">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none"
                    style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 18px; display:block; opacity:.20;">
                    <rect x="2" y="9" width="4" height="6" rx="1" />
                    <rect x="18" y="9" width="4" height="6" rx="1" />
                    <rect x="7" y="11" width="10" height="2" rx="1" />
                </svg>
                <p>Nenhum treino disponível.</p>
                <p style="font-size:13px; margin-top:6px; opacity:.45;">Crie seu primeiro treino para começar.</p>
            </div>
            @endif

            @endif

        </div>
    </div>

<script>
function toggleWorkoutMgr(id, btn) {
    const el = document.getElementById(id);
    if (!el) return;
    const isOpen = el.style.display !== 'none';
    el.style.display = isOpen ? 'none' : 'block';

    const label = btn.querySelector('.btn-view-label');
    const eye   = btn.querySelector('.icon-eye-open');

    if (isOpen) {
        if (label) label.textContent = 'Ver exercícios';
        if (eye) eye.innerHTML = `
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>`;
    } else {
        if (label) label.textContent = 'Ocultar';
        if (eye) eye.innerHTML = `
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
            <line x1="1" y1="1" x2="23" y2="23"/>`;
    }
}

function toggleWorkout(id) {
    const el  = document.getElementById(id);
    const btn = document.getElementById('btn-' + id);
    if (!el) return;
    const isOpen = el.style.display !== 'none';
    el.style.display = isOpen ? 'none' : 'block';
    if (btn) btn.textContent = isOpen ? 'Ver exercícios ▾' : 'Ocultar exercícios ▴';
}
</script>

</x-app-layout>