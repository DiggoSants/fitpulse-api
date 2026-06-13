<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div style="margin-bottom:16px; padding:12px 16px; background:rgba(74,222,128,0.08); border:1px solid rgba(74,222,128,0.2); border-radius:10px; color:#4ade80; font-size:13px; font-weight:600;">
                    {{ session('success') }}
                </div>
            @endif
            {{-- HERO --}}
            <div class="dash-hero" style="margin-bottom:1.25rem;">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Área do Aluno</div>
                        <h2 class="dash-hero__title">Meus Treinos</h2>
                        <p class="dash-hero__sub">
                            {{ $allWorkouts->count() }} treino{{ $allWorkouts->count() !== 1 ? 's' : '' }} cadastrado{{ $allWorkouts->count() !== 1 ? 's' : '' }}
                        </p>
                    </div>
                    <div class="dash-hero__right">
                        @php $st = Auth::user()->student?->status ?? 'active'; @endphp
                        @if($st === 'active')
                            <span class="dash-hero__pulse">
                                <span class="dash-hero__pulse-dot"></span>
                                FITPULSE ATIVO
                            </span>
                        @elseif($st === 'blocked')
                            <span class="dash-hero__pulse" style="background:rgba(214,21,50,.14);border-color:rgba(214,21,50,.28);color:#f87171;">
                                <span class="dash-hero__pulse-dot" style="background:#d61532;animation:none;"></span>
                                ACESSO BLOQUEADO
                            </span>
                        @else
                            <span class="dash-hero__pulse" style="background:rgba(251,191,36,.10);border-color:rgba(251,191,36,.25);color:#fbbf24;">
                                <span class="dash-hero__pulse-dot" style="background:#fbbf24;animation:none;"></span>
                                PAGAMENTO PENDENTE
                            </span>
                        @endif
                        <a href="{{ route('workouts.create') }}" class="btn-save"
                           style="text-decoration:none; display:inline-flex; align-items:center; gap:7px;">
                            <svg width="11" height="11" viewBox="0 0 12 12" fill="none"
                                 style="stroke:#fff; stroke-width:2.5; stroke-linecap:round;">
                                <line x1="6" y1="1" x2="6" y2="11"/>
                                <line x1="1" y1="6" x2="11" y2="6"/>
                            </svg>
                            Criar Treino
                        </a>
                    </div>
                </div>
            </div>

            @php
                $todayStatus = $todaySession?->status ?? 'empty';
                $todayStatusLabels = [
                    'pending' => 'Não iniciado',
                    'in_progress' => 'Em andamento',
                    'completed' => 'Finalizado',
                    'empty' => 'Sem treino',
                ];
                $todayProgress = $todaySession ? $todaySession->progress_percentage : 0;
                $completedHistoryCount = $workoutHistory->where('status', 'completed')->count();
                $incompleteHistoryCount = $workoutHistory->where('status', '!=', 'completed')->count();
            @endphp

            <div class="workout-top-grid">
            <div
                id="today-workout-panel"
                class="session-card session-card--embedded workout-check-panel"
                data-session-status="{{ $todayStatus }}"
                data-completed-count="{{ $todaySession?->completed_exercises ?? 0 }}"
                data-total-count="{{ $todaySession?->total_exercises ?? 0 }}"
            >
                <div class="session-list-head session-list-head--embedded">
                    <div>
                        <p class="session-section-label">CHECK DO DIA</p>
                        <h2>{{ $todayWorkout ? $todayWorkout->name : 'Nenhum treino para hoje' }}</h2>
                    </div>
                    <span id="today-session-status" class="session-status session-status--{{ $todayStatus }}">
                        {{ $todayStatusLabels[$todayStatus] ?? $todayStatus }}
                    </span>
                </div>

                @if($todaySession && $todayWorkout)
                    <div class="session-progress-head">
                        <div>
                            <strong id="today-session-progress-text">{{ $todayProgress }}%</strong>
                            <span id="today-session-count">
                                {{ $todaySession->completed_exercises }} de {{ $todaySession->total_exercises }} exercícios concluídos
                            </span>
                        </div>
                        <span>{{ $weekDays[$todayWeekDay] ?? 'Hoje' }}</span>
                    </div>

                    <div class="session-progress-bar" aria-label="Progresso do treino">
                        <span id="today-session-progress-bar" style="width: {{ $todayProgress }}%;"></span>
                    </div>

                    <div id="today-session-message" class="session-alert" style="display:none; margin-top:16px;"></div>

                    <div class="session-actions">
                        <button
                            type="button"
                            id="today-session-start"
                            class="btn-save"
                            data-start-url="{{ route('workout-sessions.start', $todaySession->id) }}"
                            style="{{ $todayStatus === 'pending' ? '' : 'display:none;' }}"
                        >
                            Iniciar treino
                        </button>
                        <button
                            type="button"
                            id="today-session-finish"
                            class="btn-save"
                            data-finish-url="{{ route('workout-sessions.complete', $todaySession->id) }}"
                            style="{{ $todayStatus === 'in_progress' ? '' : 'display:none;' }}"
                        >
                            Finalizar treino
                        </button>
                    </div>

                    <div class="session-list-head session-list-head--exercises">
                        <h2>Exercícios</h2>
                        <span>{{ $todaySessionExercises->count() }} itens</span>
                    </div>

                    <div class="session-exercise-list">
                        @foreach($todaySessionExercises as $sessionExercise)
                            @php
                                $sessionWorkoutExercise = $sessionExercise->workoutExercise;
                                $sessionExerciseData = $sessionWorkoutExercise?->exercise;
                            @endphp
                            <div
                                class="session-exercise-card {{ $sessionExercise->completed ? 'is-completed' : '' }}"
                                data-session-exercise-card
                                data-completed="{{ $sessionExercise->completed ? '1' : '0' }}"
                            >
                                <div class="session-exercise-main">
                                    <span class="workout-check-mark {{ $sessionExercise->completed ? 'is-checked' : '' }}" aria-hidden="true">
                                        {!! $sessionExercise->completed ? '&#10003;' : '' !!}
                                    </span>
                                    <span class="session-exercise-order">{{ $loop->iteration }}</span>
                                    <div>
                                        <h3>{{ $sessionExerciseData?->name ?? 'Exercício' }}</h3>
                                        <p>{{ $sessionExerciseData?->muscle_group ?? 'Grupo muscular' }}</p>
                                        <div class="session-exercise-meta">
                                            <span>{{ $sessionWorkoutExercise?->sets ?? 0 }} séries</span>
                                            <span>{{ $sessionWorkoutExercise?->reps ?? 0 }} reps</span>
                                            <span>{{ $sessionWorkoutExercise?->rest_time ?? 0 }}s descanso</span>
                                        </div>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    class="session-exercise-check"
                                    data-complete-url="{{ route('workout-sessions.complete-exercise', $sessionExercise->id) }}"
                                    {{ $todayStatus !== 'in_progress' || $sessionExercise->completed ? 'disabled' : '' }}
                                >
                                    {{ $sessionExercise->completed ? 'Concluído' : 'Marcar concluído' }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="session-empty-state session-empty-state--compact">
                        <h2>Nenhum treino para hoje</h2>
                        <p>{{ $todaySessionMessage }}</p>
                    </div>
                @endif
            </div>
            <div class="session-card workout-history-panel" id="workout-history-panel">
                <div class="workout-history-head">
                    <div>
                        <p class="session-section-label">HISTÓRICO</p>
                        <h2>Histórico de treinos</h2>
                    </div>
                    <div class="workout-history-summary">
                        <span>{{ $completedHistoryCount }} completo{{ $completedHistoryCount !== 1 ? 's' : '' }}</span>
                        <span>{{ $incompleteHistoryCount }} não completo{{ $incompleteHistoryCount !== 1 ? 's' : '' }}</span>
                    </div>
                </div>

                @if($workoutHistory->isEmpty())
                    <div class="session-empty-state session-empty-state--compact">
                        <h2>Nenhum histórico ainda</h2>
                        <p>Quando você iniciar treinos, eles aparecerão aqui como completos ou não completos.</p>
                    </div>
                @else
                    <div class="workout-history-list">
                        @foreach($workoutHistory as $historySession)
                            @php
                                $historyIsComplete = $historySession->status === 'completed';
                                $historyStatusLabel = $historyIsComplete ? 'Completo' : 'Não completo';
                                $historyProgress = $historySession->progress_percentage;
                            @endphp
                            <div class="workout-history-row {{ $historyIsComplete ? 'is-complete' : 'is-incomplete' }}">
                                <span class="workout-history-check {{ $historyIsComplete ? 'is-checked' : 'is-open' }}" aria-hidden="true">
                                    {!! $historyIsComplete ? '&#10003;' : '!' !!}
                                </span>
                                <div class="workout-history-row__main">
                                    <strong>{{ $historySession->workout?->name ?? 'Treino removido' }}</strong>
                                    <span>
                                        {{ $historySession->session_date?->format('d/m/Y') ?? '--/--/----' }}
                                        • {{ $historySession->completed_exercises }} de {{ $historySession->total_exercises }} exercícios
                                    </span>
                                    <div class="workout-history-progress">
                                        <span style="width: {{ $historyProgress }}%;"></span>
                                    </div>
                                </div>
                                <div class="workout-history-row__side">
                                    <span class="workout-history-badge {{ $historyIsComplete ? 'is-complete' : 'is-incomplete' }}">
                                        {{ $historyStatusLabel }}
                                    </span>
                                    <em>{{ $historyProgress }}%</em>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            </div>

            <div class="weekly-schedule-panel">
                <div class="weekly-schedule-head">
                    <div>
                        <p class="section-label">AGENDA SEMANAL</p>
                        <h3>Configurar dias de treino</h3>
                    </div>
                    <span class="weekly-schedule-min">Mínimo {{ $minScheduleDays }} dias</span>
                </div>

                @include('workouts.partials.schedule-form', [
                    'weekDays' => $weekDays,
                    'scheduleDays' => $scheduleDays,
                    'minScheduleDays' => $minScheduleDays,
                ])

                <div class="weekly-agenda-grid">
                    @forelse($scheduleDays as $dayKey)
                        @php $dayWorkouts = $workoutsByDay->get($dayKey, collect()); @endphp
                        <div class="weekly-agenda-day">
                            <div class="weekly-agenda-day__head">
                                <strong>{{ $weekDays[$dayKey] ?? $dayKey }}</strong>
                                <span>{{ $dayWorkouts->count() }} treino{{ $dayWorkouts->count() !== 1 ? 's' : '' }}</span>
                            </div>

                            @forelse($dayWorkouts as $dayWorkout)
                                <a href="{{ route('workouts.index', ['workout_id' => $dayWorkout->id]) }}"
                                   class="weekly-workout-link {{ (isset($workout) && $workout->id === $dayWorkout->id) ? 'is-active' : '' }}">
                                    <span>{{ $dayWorkout->name }}</span>
                                    <em>{{ $dayWorkout->workoutExercises->count() }} exerc.</em>
                                </a>
                            @empty
                                <div class="weekly-day-empty">Sem treino vinculado a este dia.</div>
                            @endforelse
                        </div>
                    @empty
                        <div class="weekly-day-empty weekly-day-empty--wide">
                            Selecione os dias da agenda para vincular treinos.
                        </div>
                    @endforelse
                </div>

                @if($workoutsWithoutDay->count())
                    <div class="weekly-unscheduled">
                        <span>Sem dia definido</span>
                        @foreach($workoutsWithoutDay as $looseWorkout)
                            <a href="{{ route('workouts.index', ['workout_id' => $looseWorkout->id]) }}">{{ $looseWorkout->name }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            @if($allWorkouts->isEmpty())
                {{-- EMPTY STATE --}}
                <div class="empty-state" style="padding:5rem 1rem;">
                    <svg width="56" height="56" viewBox="0 0 24 24" fill="none"
                         style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 18px; display:block; opacity:.20;">
                        <rect x="2" y="9" width="4" height="6" rx="1"/>
                        <rect x="18" y="9" width="4" height="6" rx="1"/>
                        <rect x="7" y="11" width="10" height="2" rx="1"/>
                    </svg>
                    <p>Nenhum treino disponível.</p>
                    <p style="font-size:13px; margin-top:6px; opacity:.45;">Crie seu primeiro treino para começar.</p>
                    <a href="{{ route('workouts.create') }}" class="btn-save"
                       style="text-decoration:none; display:inline-block; margin-top:20px;">
                        + Criar Primeiro Treino
                    </a>
                </div>
            @else

                {{-- SELETOR DE TREINOS --}}
                <div class="wkt-selector">
                    @foreach($allWorkouts as $w)
                        <a href="{{ route('workouts.index', ['workout_id' => $w->id]) }}"
                           class="wkt-selector__pill {{ (isset($workout) && $workout->id === $w->id) ? 'is-active' : '' }}">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                 style="stroke:currentColor; stroke-width:2; stroke-linecap:round;">
                                <rect x="2" y="9" width="4" height="6" rx="1"/>
                                <rect x="18" y="9" width="4" height="6" rx="1"/>
                                <rect x="7" y="11" width="10" height="2" rx="1"/>
                            </svg>
                            {{ $w->name }}
                            <span class="wkt-selector__count">{{ $w->workoutExercises->count() }}</span>
                        </a>
                    @endforeach
                </div>

                @if(isset($workout))
                    {{-- STATS DO TREINO SELECIONADO --}}
                    <div class="dash-stats">
                        <div class="dash-stat dash-stat--red">
                            <div class="dash-stat__bg-icon"></div>
                            <div class="dash-stat__header">
                                <span class="dash-stat__dot"></span>
                                <span class="dash-stat__label">Séries totais</span>
                            </div>
                            <div class="dash-stat__value">{{ $exercises->sum(fn($e) => (int) $e->sets) }}</div>
                        </div>
                        <div class="dash-stat dash-stat--blue">
                            <div class="dash-stat__bg-icon"></div>
                            <div class="dash-stat__header">
                                <span class="dash-stat__dot"></span>
                                <span class="dash-stat__label">Reps totais</span>
                            </div>
                            <div class="dash-stat__value">{{ $exercises->sum(fn($e) => (int) $e->reps) }}</div>
                        </div>
                        <div class="dash-stat dash-stat--green">
                            <div class="dash-stat__bg-icon"></div>
                            <div class="dash-stat__header">
                                <span class="dash-stat__dot"></span>
                                <span class="dash-stat__label">Descanso (s)</span>
                            </div>
                            <div class="dash-stat__value">{{ $exercises->sum(fn($e) => (int) $e->rest_time) }}</div>
                        </div>
                    </div>

                    {{-- HEADER DO TREINO --}}
                    <div class="exercises-header">
                        <div class="exercises-header__left">
                            <span class="exercises-header__tag">Treino selecionado</span>
                            <h3 class="exercises-header__name">{{ $workout->name }}</h3>
                            <span class="exercises-header__badge">{{ $exercises->count() }} exerc.</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <a href="{{ route('workouts.edit', $workout->id) }}" class="btn-ghost">
                                <svg viewBox="0 0 14 14" fill="none"
                                     style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; width:12px; height:12px;">
                                    <path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/>
                                </svg>
                                Editar
                            </a>
                            <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-del"
                                        onclick="openWorkoutDeleteConfirm(this)">
                                    <svg viewBox="0 0 14 16" fill="none"
                                         style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; width:12px; height:12px;">
                                        <path d="M1 3.5h12M4.5 3.5V2a.5.5 0 01.5-.5h3a.5.5 0 01.5.5v1.5M5.5 7v5M8.5 7v5M2.5 3.5l.9 10a.5.5 0 00.5.5h6.2a.5.5 0 00.5-.5l.9-10"/>
                                    </svg>
                                    Deletar
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- GRID DE EXERCÍCIOS --}}
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
                                                    <rect x="2" y="9" width="4" height="6" rx="1"/>
                                                    <rect x="18" y="9" width="4" height="6" rx="1"/>
                                                    <rect x="7" y="11" width="10" height="2" rx="1"/>
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
                                        {{-- BOTÃO PLAY com nome do exercício --}}
                                        <button
                                            class="btn-play"
                                            title="Ver tutorial"
                                            onclick="openExerciseModal('{{ addslashes($item->exercise->name) }}', {{ (int)$item->sets }}, {{ (int)$item->reps }}, {{ (int)($item->rest_time ?? 0) }})"
                                        >
                                            <svg viewBox="0 0 10 12"><polygon points="0,0 10,6 0,12"/></svg>
                                        </button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-state"><p>Nenhum exercício neste treino.</p></div>
                    @endif
                @endif

            @endif

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         MODAL DE TUTORIAL DO EXERCÍCIO
    ══════════════════════════════════════════════════════ --}}
    <div id="workout-delete-overlay" style="display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; padding:20px; background:rgba(0,0,0,.68); backdrop-filter:blur(4px);">
        <div style="width:100%; max-width:380px; border-radius:20px; background:#151515; border:1px solid rgba(255,255,255,.10); box-shadow:0 24px 70px rgba(0,0,0,.45); overflow:hidden;">
            <div style="padding:24px 24px 10px;">
                <div style="width:44px; height:44px; border-radius:14px; display:flex; align-items:center; justify-content:center; background:rgba(214,21,50,.12); border:1px solid rgba(214,21,50,.25); margin-bottom:14px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="stroke:#f87171; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;">
                        <path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14"/>
                        <path d="M10 11v5M14 11v5"/>
                    </svg>
                </div>
                <h2 style="font-size:18px; font-weight:800; margin:0 0 8px; color:#fff;">Deletar treino?</h2>
                <p style="font-size:13px; line-height:1.5; color:rgba(255,255,255,.62); margin:0;">Essa ação remove o treino selecionado e seus exercícios vinculados.</p>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end; padding:18px 24px 24px;">
                <button type="button" class="btn-ghost" onclick="closeWorkoutDeleteConfirm()">Cancelar</button>
                <button type="button" class="btn-del" onclick="submitWorkoutDelete()">Deletar</button>
            </div>
        </div>
    </div>

    <div id="exercise-modal" style="
        display:none;
        position:fixed; inset:0; z-index:9999;
        background:rgba(0,0,0,0.88);
        align-items:center; justify-content:center;
        padding:16px;
    ">
        <div class="ex-modal-box">
            {{-- Fechar --}}
            <button onclick="closeExerciseModal()" class="ex-modal-close">✕</button>

            {{-- Nome do exercício --}}
            <p id="modal-exercise-name" class="ex-modal-title"></p>

            {{-- Stats: séries / reps / descanso --}}
            <div class="ex-modal-stats">
                <div class="ex-modal-stat ex-modal-stat--red">
                    <span class="ex-modal-stat__value" id="modal-sets">—</span>
                    <span class="ex-modal-stat__label">séries</span>
                </div>
                <div class="ex-modal-stat-divider"></div>
                <div class="ex-modal-stat ex-modal-stat--blue">
                    <span class="ex-modal-stat__value" id="modal-reps">—</span>
                    <span class="ex-modal-stat__label">repetições</span>
                </div>
                <div class="ex-modal-stat-divider"></div>
                <div class="ex-modal-stat ex-modal-stat--green">
                    <span class="ex-modal-stat__value" id="modal-rest">—</span>
                    <span class="ex-modal-stat__label">descanso</span>
                </div>
            </div>

            {{-- Loading --}}
            <div id="modal-loading" style="text-align:center; color:var(--text-muted); padding:48px 0;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                     style="stroke:#f87171; stroke-width:2; stroke-linecap:round; margin:0 auto 12px; display:block; animation:spin 1s linear infinite;">
                    <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                </svg>
                Buscando tutorial...
            </div>

            {{-- Vídeo --}}
            <div id="modal-video" style="display:none;">
                <iframe
                    id="yt-iframe"
                    width="100%" height="370"
                    frameborder="0" allowfullscreen
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    style="border-radius:12px; display:block;"
                ></iframe>
            </div>

            {{-- Erro --}}
            <div id="modal-error" style="display:none; text-align:center; padding:48px 0;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                     style="stroke:#f87171; stroke-width:1.8; stroke-linecap:round; margin:0 auto 12px; display:block; opacity:.6;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <p style="color:#f87171; font-size:13px;">Nenhum vídeo encontrado para este exercício.</p>
            </div>
        </div>
    </div>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── MODAL BOX ── */
        .ex-modal-box {
            background: #111;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px;
            padding: 24px;
            width: 100%; max-width: 740px;
            position: relative;
            box-shadow: 0 24px 64px rgba(0,0,0,0.6);
        }
        [data-theme="light"] .ex-modal-box {
            background: #fff;
            border-color: rgba(0,0,0,0.10);
            box-shadow: 0 24px 64px rgba(0,0,0,0.18);
        }

        /* ── FECHAR ── */
        .ex-modal-close {
            position: absolute; top: 14px; right: 16px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.10);
            color: #fff; width: 32px; height: 32px;
            border-radius: 50%; font-size: 16px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: background .15s;
        }
        .ex-modal-close:hover { background: rgba(255,255,255,0.12); }
        [data-theme="light"] .ex-modal-close {
            background: rgba(0,0,0,0.05);
            border-color: rgba(0,0,0,0.10);
            color: #333;
        }
        [data-theme="light"] .ex-modal-close:hover { background: rgba(0,0,0,0.10); }

        /* ── TÍTULO ── */
        .ex-modal-title {
            font-size: 17px; font-weight: 800; color: #fff;
            margin: 0 40px 16px 0; letter-spacing: -.01em;
        }
        [data-theme="light"] .ex-modal-title { color: #111; }

        /* ── STATS BAR ── */
        .ex-modal-stats {
            display: flex;
            align-items: center;
            gap: 0;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 14px;
            padding: 14px 20px;
            margin-bottom: 18px;
        }
        [data-theme="light"] .ex-modal-stats {
            background: rgba(0,0,0,0.03);
            border-color: rgba(0,0,0,0.08);
        }

        .ex-modal-stat {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }

        .ex-modal-stat__value {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 32px;
            letter-spacing: 1px;
            line-height: 1;
        }
        .ex-modal-stat--red  .ex-modal-stat__value { color: #f87171; }
        .ex-modal-stat--blue .ex-modal-stat__value { color: #60a5fa; }
        .ex-modal-stat--green .ex-modal-stat__value { color: #4ade80; }

        [data-theme="light"] .ex-modal-stat--red  .ex-modal-stat__value { color: #dc2626; }
        [data-theme="light"] .ex-modal-stat--blue .ex-modal-stat__value { color: #2563eb; }
        [data-theme="light"] .ex-modal-stat--green .ex-modal-stat__value { color: #16a34a; }

        .ex-modal-stat__label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .10em;
            color: var(--text-muted);
        }
        [data-theme="light"] .ex-modal-stat__label { color: rgba(0,0,0,0.40); }

        .ex-modal-stat-divider {
            width: 1px;
            height: 36px;
            background: rgba(255,255,255,0.07);
            flex-shrink: 0;
        }
        [data-theme="light"] .ex-modal-stat-divider { background: rgba(0,0,0,0.08); }

        .exercise-grid-card__thumb {
    aspect-ratio: 1/1 !important;
    background: #0a0a0a !important;
}
.exercise-grid-card__thumb img {
    object-fit: contain !important;
}
[data-theme="light"] .exercise-grid-card__thumb {
    background: #f5f5f5 !important;
}

/* HISTÓRICO DE TREINOS - layout corrections */
.workout-history-list { display:block; }
.workout-history-row {
    display:flex;
    gap:12px;
    align-items:flex-start;
    padding:12px 18px;
    border-radius:10px;
    background: transparent;
    border:1px solid rgba(255,255,255,0.03);
    margin-bottom:12px;
    position:relative;
    width:100%;
    box-sizing:border-box;
    overflow:visible;
}
.workout-history-row__main { flex:1; min-width:0; }
.workout-history-row__main strong { display:block; font-size:15px; margin-bottom:6px; }
.workout-history-row__main > span { display:flex; align-items:center; gap:12px; color:var(--text-muted); font-size:13px; margin-bottom:10px; flex-wrap:wrap; }
.workout-history-progress { height:8px; background:rgba(255,255,255,0.04); border-radius:6px; overflow:hidden; }
.workout-history-progress span { display:block; height:100%; background:linear-gradient(90deg,#4ade80,#10b981); width:0%; }
.workout-history-row__side { display:flex; flex-direction:column; align-items:flex-end; gap:6px; min-width:90px; margin-left:auto; }
.workout-history-badge { display:inline-block; padding:6px 12px; border-radius:999px; font-size:12px; font-weight:800; white-space:nowrap; position:relative; right:0; }
.workout-history-badge.is-complete { background:rgba(34,197,94,0.12); color:#4ade80; border:1px solid rgba(34,197,94,0.18); }
.workout-history-badge.is-incomplete { background:rgba(245,158,11,0.06); color:#f59e0b; border:1px solid rgba(245,158,11,0.08); }
.workout-history-check { margin-right:8px; font-weight:700; display:flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:999px; background:rgba(255,255,255,0.02); }

    </style>

    <script>
    (function () {
        const panel = document.getElementById('today-workout-panel');
        if (!panel) return;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        const statusLabels = {
            pending: 'Não iniciado',
            in_progress: 'Em andamento',
            completed: 'Finalizado',
            empty: 'Sem treino',
        };

        const statusBadge = document.getElementById('today-session-status');
        const messageBox = document.getElementById('today-session-message');
        const startButton = document.getElementById('today-session-start');
        const finishButton = document.getElementById('today-session-finish');
        const progressText = document.getElementById('today-session-progress-text');
        const progressBar = document.getElementById('today-session-progress-bar');
        const progressCount = document.getElementById('today-session-count');
        const exerciseButtons = panel.querySelectorAll('[data-complete-url]');

        if (!statusBadge) return;

        let currentStatus = panel.dataset.sessionStatus || 'empty';
        let completedCount = Number(panel.dataset.completedCount || 0);
        let totalCount = Number(panel.dataset.totalCount || 0);

        async function postJson(url) {
            const response = await fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({}),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(data.message || 'Não foi possível concluir a ação.');
            }

            return data;
        }

        function showSessionMessage(text, type = 'success') {
            if (!messageBox) return;
            messageBox.textContent = text;
            messageBox.className = 'session-alert ' + (type === 'error' ? 'session-alert--error' : 'session-alert--success');
            messageBox.style.display = 'block';
        }

        function setButtonLoading(button, isLoading) {
            if (!button) return;
            button.disabled = isLoading;
            button.dataset.originalText = button.dataset.originalText || button.textContent.trim();
            button.textContent = isLoading ? 'Aguarde...' : button.dataset.originalText;
        }

        function updateProgress(nextCompleted, nextTotal, nextProgress) {
            completedCount = Number(nextCompleted ?? completedCount);
            totalCount = Number(nextTotal ?? totalCount);

            const progress = Number(nextProgress ?? (totalCount ? Math.round((completedCount / totalCount) * 100) : 0));

            if (progressText) progressText.textContent = progress + '%';
            if (progressBar) progressBar.style.width = progress + '%';
            if (progressCount) {
                progressCount.textContent = completedCount + ' de ' + totalCount + ' exercícios concluídos';
            }
        }

        function applyStatus(nextStatus) {
            currentStatus = nextStatus;
            panel.dataset.sessionStatus = nextStatus;
            statusBadge.textContent = statusLabels[nextStatus] || nextStatus;
            statusBadge.className = 'session-status session-status--' + nextStatus;

            if (startButton) {
                startButton.style.display = nextStatus === 'pending' ? '' : 'none';
                startButton.disabled = nextStatus !== 'pending';
            }

            if (finishButton) {
                finishButton.style.display = nextStatus === 'in_progress' ? '' : 'none';
                finishButton.disabled = nextStatus !== 'in_progress';
            }

            exerciseButtons.forEach((button) => {
                const card = button.closest('[data-session-exercise-card]');
                const completed = card?.dataset.completed === '1';
                button.disabled = nextStatus !== 'in_progress' || completed;
                button.textContent = completed ? 'Concluído' : 'Marcar concluído';
            });
        }

        startButton?.addEventListener('click', async () => {
            setButtonLoading(startButton, true);
            try {
                const data = await postJson(startButton.dataset.startUrl);
                showSessionMessage(data.message || 'Treino iniciado.');
                applyStatus(data.status || 'in_progress');
            } catch (error) {
                showSessionMessage(error.message, 'error');
                applyStatus(currentStatus);
            } finally {
                setButtonLoading(startButton, false);
            }
        });

        finishButton?.addEventListener('click', async () => {
            setButtonLoading(finishButton, true);
            try {
                const data = await postJson(finishButton.dataset.finishUrl);
                showSessionMessage(data.message || 'Treino finalizado.');
                applyStatus(data.status || 'completed');
                if ((data.status || 'completed') === 'completed') {
                    setTimeout(() => {
                        window.location.reload();
                    }, 600);
                }
            } catch (error) {
                showSessionMessage(error.message, 'error');
                applyStatus(currentStatus);
            } finally {
                setButtonLoading(finishButton, false);
            }
        });

        exerciseButtons.forEach((button) => {
            button.addEventListener('click', async () => {
                setButtonLoading(button, true);
                try {
                    const data = await postJson(button.dataset.completeUrl);
                    const card = button.closest('[data-session-exercise-card]');
                    if (card) {
                        card.dataset.completed = '1';
                        card.classList.add('is-completed');
                        const mark = card.querySelector('.workout-check-mark');
                        if (mark) {
                            mark.classList.add('is-checked');
                            mark.innerHTML = '&#10003;';
                        }
                    }
                    updateProgress(data.completed_count, data.total_count, data.progress);
                    showSessionMessage(data.message || 'Exercício marcado como concluído.');
                    applyStatus(currentStatus);
                } catch (error) {
                    showSessionMessage(error.message, 'error');
                    applyStatus(currentStatus);
                } finally {
                    if (button.closest('[data-session-exercise-card]')?.dataset.completed !== '1') {
                        setButtonLoading(button, false);
                    }
                }
            });
        });

        applyStatus(currentStatus);
    })();

    let workoutDeleteForm = null;

    function openWorkoutDeleteConfirm(button) {
        workoutDeleteForm = button.closest('form');
        document.getElementById('workout-delete-overlay').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeWorkoutDeleteConfirm() {
        workoutDeleteForm = null;
        document.getElementById('workout-delete-overlay').style.display = 'none';
        document.body.style.overflow = '';
    }

    function submitWorkoutDelete() {
        if (workoutDeleteForm) workoutDeleteForm.submit();
    }

    const EXERCISE_VIDEO_URL = "{{ route('exercise.video', [], false) }}";

    async function openExerciseModal(exerciseName, sets, reps, rest) {
        const modal = document.getElementById('exercise-modal');
        modal.style.display = 'flex';

        document.getElementById('modal-exercise-name').textContent = exerciseName;
        document.getElementById('modal-sets').textContent  = sets  || '—';
        document.getElementById('modal-reps').textContent  = reps  || '—';
        document.getElementById('modal-rest').textContent  = rest ? rest + 's' : '—';
        document.getElementById('modal-loading').style.display = 'block';
        document.getElementById('modal-video').style.display   = 'none';
        document.getElementById('modal-error').style.display   = 'none';
        document.getElementById('yt-iframe').src               = '';

        try {
            const res  = await fetch(EXERCISE_VIDEO_URL + '?q=' + encodeURIComponent(exerciseName), {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) throw new Error('Erro ao carregar video.');
            const data = await res.json();

            document.getElementById('modal-loading').style.display = 'none';

            if (data.video_id) {
                document.getElementById('yt-iframe').src =
                    'https://www.youtube.com/embed/' + data.video_id + '?autoplay=1&rel=0';
                document.getElementById('modal-video').style.display = 'block';
            } else {
                document.getElementById('modal-error').style.display = 'block';
            }
        } catch (e) {
            document.getElementById('modal-loading').style.display = 'none';
            document.getElementById('modal-error').style.display   = 'block';
        }
    }

    function closeExerciseModal() {
        document.getElementById('exercise-modal').style.display = 'none';
        document.getElementById('yt-iframe').src = '';
    }

    document.getElementById('exercise-modal').addEventListener('click', function (e) {
        if (e.target === this) closeExerciseModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeExerciseModal();
    });
    </script>
</x-app-layout>
