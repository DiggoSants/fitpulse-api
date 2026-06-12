<x-app-layout>
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
                    <div class="mgr-card__left">
                        <div class="mgr-card__left-top">
                            <div class="mgr-inst-big-avatar">{{ mb_strtoupper(mb_substr($instructor->user->name, 0, 2)) }}</div>
                            <div class="mgr-inst-label">Instrutor</div>
                            <div class="mgr-inst-name">{{ $instructor->user->name }}</div>
                            <span class="mgr-inst-specialty">{{ !empty($instructor->specialty) ? $instructor->specialty : 'Personal Trainer' }}</span>
                            <div class="mgr-inst-badge">{{ $instructor->students->count() }} aluno(s)</div>
                        </div>
                        <div class="mgr-inst-divider"></div>
                        <a href="{{ route('instructors.edit', $instructor->id) }}" class="mgr-inst-edit">Editar</a>
                    </div>
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
                                    <a href="{{ route('workouts.create', ['student_id' => $student->id]) }}" class="mgr-btn-criar">+ Treino</a>
                                </div>
                            </div>
                            @forelse($student->workouts as $workout)
                            <div class="mgr-workouts">
                                <div class="mgr-workout-row">
                                    <div class="mgr-workout-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><rect x="2" y="10" width="3" height="4" rx="1"/><rect x="19" y="10" width="3" height="4" rx="1"/><rect x="5" y="8" width="3" height="8" rx="1"/><rect x="16" y="8" width="3" height="8" rx="1"/><rect x="8" y="11" width="8" height="2" rx="1"/></svg>
                                    </div>
                                    <span class="mgr-workout-name">{{ $workout->name }}</span>
                                    @if($workout->week_day)
                                    <span class="wkt-item__day">{{ ['monday'=>'Seg','tuesday'=>'Ter','wednesday'=>'Qua','thursday'=>'Qui','friday'=>'Sex','saturday'=>'Sáb','sunday'=>'Dom'][$workout->week_day] ?? $workout->week_day }}</span>
                                    @endif
                                    <div class="mgr-workout-btns">
                                        <button type="button" class="wkt-btn wkt-btn--toggle" onclick="toggleWkt('mgr-wkt-{{ $workout->id }}', this)">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </button>
                                        <a href="{{ route('workouts.edit', [$workout->id, 'student_id' => $student->id]) }}" class="wkt-btn wkt-btn--edit">
                                            <svg width="12" height="12" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/></svg>
                                        </a>
                                        <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                                            <button type="submit" class="wkt-btn wkt-btn--del">
                                                <svg width="11" height="12" viewBox="0 0 14 16" fill="none" stroke="#f87171" stroke-width="1.8" stroke-linecap="round"><path d="M1 3.5h12M4.5 3.5V2a.5.5 0 01.5-.5h3a.5.5 0 01.5.5v1.5M5.5 7v5M8.5 7v5M2.5 3.5l.9 10a.5.5 0 00.5.5h6.2a.5.5 0 00.5-.5l.9-10"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div id="mgr-wkt-{{ $workout->id }}" class="wkt-item__exercises" style="display:none;">
                                    @if($workout->workoutExercises->count())
                                    <div class="ex-table">
                                        <div class="ex-table__head"><span>Exercício</span><span>Grupo</span><span>Séries</span><span>Reps</span><span>Desc.</span></div>
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
                                    <p class="wkt-item__empty">Nenhum exercício neste treino.</p>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="wkt-empty">Nenhum treino cadastrado.</div>
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

            {{-- VISÃO DO INSTRUTOR --}}
            @elseif(Auth::user()->isInstructor())

            <div class="dash-hero" style="overflow:visible;">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Bem-vindo de volta</div>
                        <h2 class="dash-hero__title">Meus Alunos</h2>
                        <p class="dash-hero__sub">{{ $instructor->specialty ?? 'Instrutor' }}</p>
                    </div>
                    <div class="dash-hero__right" style="display:flex; flex-direction:row; flex-wrap:wrap; align-items:center; justify-content:flex-end; gap:10px;">
                        <span class="dash-hero__pulse"><span class="dash-hero__pulse-dot"></span>INSTRUTOR</span>
                        <a href="{{ route('instructor.availability') }}" class="btn-ghost" style="text-decoration:none; display:inline-flex; align-items:center; gap:7px; font-size:12px; padding:9px 18px;">Agenda</a>
                        <a href="{{ route('evaluations.instructor') }}" class="btn-ghost" style="text-decoration:none; display:inline-flex; align-items:center; gap:7px; font-size:12px; padding:9px 18px;">Evolução Física</a>
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

                    {{-- Cabeçalho do card --}}
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

                    {{-- Agenda compacta --}}
                    @if(isset($studentSchedules[$student->id]))
                    <div class="student-card__schedule">
                        @include('workouts.partials.schedule-form', [
                            'scheduleDays'     => $studentSchedules[$student->id],
                            'scheduleStudent'  => $student,
                            'scheduleIsCompact'=> true,
                            'weekDays'         => $weekDays,
                            'minScheduleDays'  => $minScheduleDays,
                        ])
                    </div>
                    @endif

                    {{-- Lista de treinos --}}
                    <div class="wkt-list">
                        @forelse($student->workouts as $workout)
                        <div class="wkt-item">
                            <div class="wkt-item__row">
                                <div class="wkt-item__icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                                        <rect x="2" y="10" width="3" height="4" rx="1"/><rect x="19" y="10" width="3" height="4" rx="1"/>
                                        <rect x="5" y="8" width="3" height="8" rx="1"/><rect x="16" y="8" width="3" height="8" rx="1"/>
                                        <rect x="8" y="11" width="8" height="2" rx="1"/>
                                    </svg>
                                </div>
                                <div class="wkt-item__info">
                                    <span class="wkt-item__name">{{ $workout->name }}</span>
                                    <div class="wkt-item__meta">
                                        @if($workout->week_day)
                                        <span class="wkt-item__day">
                                            {{ ['monday'=>'Seg','tuesday'=>'Ter','wednesday'=>'Qua','thursday'=>'Qui','friday'=>'Sex','saturday'=>'Sáb','sunday'=>'Dom'][$workout->week_day] ?? $workout->week_day }}
                                        </span>
                                        @endif
                                        <span class="wkt-item__count">{{ $workout->workoutExercises->count() }} exerc.</span>
                                    </div>
                                </div>
                                <div class="wkt-item__actions">
                                    <button type="button" class="wkt-btn wkt-btn--toggle" onclick="toggleWkt('wkt-{{ $workout->id }}', this)">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                    <a href="{{ route('workouts.edit', [$workout->id, 'student_id' => $student->id]) }}" class="wkt-btn wkt-btn--edit">
                                        <svg width="12" height="12" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/></svg>
                                    </a>
                                    <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                                        <button type="submit" class="wkt-btn wkt-btn--del">
                                            <svg width="11" height="12" viewBox="0 0 14 16" fill="none" stroke="#f87171" stroke-width="1.8" stroke-linecap="round"><path d="M1 3.5h12M4.5 3.5V2a.5.5 0 01.5-.5h3a.5.5 0 01.5.5v1.5M5.5 7v5M8.5 7v5M2.5 3.5l.9 10a.5.5 0 00.5.5h6.2a.5.5 0 00.5-.5l.9-10"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Exercícios expansíveis --}}
                            <div id="wkt-{{ $workout->id }}" class="wkt-item__exercises" style="display:none;">
                                @if($workout->workoutExercises->count())
                                <div class="ex-table">
                                    <div class="ex-table__head">
                                        <span>Exercício</span><span>Grupo</span><span>Séries</span><span>Reps</span><span>Desc.</span>
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
                                <p class="wkt-item__empty">Nenhum exercício cadastrado.</p>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="wkt-empty">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" opacity=".35">
                                <rect x="2" y="10" width="3" height="4" rx="1"/><rect x="19" y="10" width="3" height="4" rx="1"/>
                                <rect x="5" y="8" width="3" height="8" rx="1"/><rect x="16" y="8" width="3" height="8" rx="1"/>
                                <rect x="8" y="11" width="8" height="2" rx="1"/>
                            </svg>
                            <span>Nenhum treino cadastrado</span>
                        </div>
                        @endforelse
                    </div>

                    {{-- Rodapé --}}
                    <div class="student-card__footer">
                        <a href="{{ route('workouts.create', ['student_id' => $student->id]) }}" class="wkt-btn-create">
                            <svg width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round">
                                <line x1="6" y1="1" x2="6" y2="11"/><line x1="1" y1="6" x2="11" y2="6"/>
                            </svg>
                            Criar treino
                        </a>
                    </div>

                </div>
                @empty
                <div class="inst-empty" style="grid-column:1/-1;">Nenhum aluno vinculado a você.</div>
                @endforelse
            </div>

            {{-- VISÃO DO ALUNO SEM MATRÍCULA --}}
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
                        <a href="{{ route('enrollment.index') }}" class="btn-save" style="text-decoration:none; display:inline-flex; align-items:center; gap:7px;">Matricular-se</a>
                    </div>
                </div>
            </div>
            <div class="empty-state" style="padding:4rem 1rem;">
                <p>Você ainda não possui uma matrícula ativa.</p>
                <a href="{{ route('enrollment.index') }}" class="btn-save" style="text-decoration:none; display:inline-block; margin-top:20px;">Ver Planos</a>
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
                        <span class="dash-hero__pulse"><span class="dash-hero__pulse-dot"></span>FITPULSE ATIVO</span>
                        <a href="{{ route('workouts.create') }}" class="btn-save" style="text-decoration:none; display:inline-flex; align-items:center; gap:7px;">
                            <svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="stroke:#fff; stroke-width:2.5; stroke-linecap:round;"><line x1="6" y1="1" x2="6" y2="11"/><line x1="1" y1="6" x2="11" y2="6"/></svg>
                            Criar Treino
                        </a>
                    </div>
                </div>
            </div>

            @if(isset($workout))
            <div class="dash-stats">
                <div class="dash-stat dash-stat--red"><div class="dash-stat__bg-icon">⚡</div><div class="dash-stat__header"><span class="dash-stat__dot"></span><span class="dash-stat__label">Séries totais</span></div><div class="dash-stat__value">{{ $exercises->sum(fn($e) => (int) $e->sets) }}</div></div>
                <div class="dash-stat dash-stat--blue"><div class="dash-stat__bg-icon">🔁</div><div class="dash-stat__header"><span class="dash-stat__dot"></span><span class="dash-stat__label">Reps totais</span></div><div class="dash-stat__value">{{ $exercises->sum(fn($e) => (int) $e->reps) }}</div></div>
                <div class="dash-stat dash-stat--green"><div class="dash-stat__bg-icon">🏋️</div><div class="dash-stat__header"><span class="dash-stat__dot"></span><span class="dash-stat__label">Descanso</span></div><div class="dash-stat__value">{{ $exercises->sum(fn($e) => (int) $e->rest_time) }}</div></div>
            </div>
            <div class="exercises-header">
                <div class="exercises-header__left">
                    <span class="exercises-header__tag">Treino atual</span>
                    <h3 class="exercises-header__name">{{ $workout->name }}</h3>
                    <span class="exercises-header__badge">{{ $exercises->count() }} exerc.</span>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <a href="{{ route('workouts.edit', $workout->id) }}" class="btn-ghost">Editar</a>
                    <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">@csrf @method('DELETE')<button type="submit" class="btn-del">Deletar</button></form>
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
                                <svg viewBox="0 0 24 24"><rect x="2" y="9" width="4" height="6" rx="1"/><rect x="18" y="9" width="4" height="6" rx="1"/><rect x="7" y="11" width="10" height="2" rx="1"/></svg>
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
                        <span style="font-size:11px; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.07em;">{{ $item->exercise->muscle_group ?? '' }}</span>
                        <button class="btn-play" title="Iniciar"><svg viewBox="0 0 10 12"><polygon points="0,0 10,6 0,12"/></svg></button>
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <div class="empty-state"><p>Nenhum exercício encontrado.</p></div>
            @endif
            @else
            <div class="empty-state" style="padding:5rem 1rem;">
                <p>Nenhum treino disponível.</p>
                <p style="font-size:13px; margin-top:6px; opacity:.45;">Crie seu primeiro treino para começar.</p>
            </div>
            @endif

            @endif
        </div>
    </div>

<style>
/* ══════════════════════════════════════════
   WKT — lista de treinos no card do aluno
══════════════════════════════════════════ */

.wkt-list {
    display: flex;
    flex-direction: column;
    gap: 2px;
    padding: 6px 10px;
    background: rgba(255,255,255,0.015);
    border-top: 1px solid rgba(255,255,255,0.05);
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.wkt-item {
    border-radius: 10px;
    overflow: hidden;
}

.wkt-item__row {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 8px 8px 8px 6px;
    border-radius: 10px;
    transition: background .15s;
}

.wkt-item__row:hover {
    background: rgba(255,255,255,0.04);
}

/* Ícone halter */
.wkt-item__icon {
    width: 30px;
    height: 30px;
    flex-shrink: 0;
    border-radius: 8px;
    background: rgba(214,21,50,0.08);
    border: 1px solid rgba(214,21,50,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f87171;
}

.wkt-item__icon svg { width: 13px; height: 13px; }

/* Nome + meta */
.wkt-item__info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.wkt-item__name {
    font-size: 12.5px;
    font-weight: 700;
    color: #f0f0f0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.wkt-item__meta {
    display: flex;
    align-items: center;
    gap: 5px;
}

.wkt-item__day {
    font-size: 10px;
    font-weight: 800;
    padding: 1px 6px;
    border-radius: 99px;
    background: rgba(214,21,50,0.10);
    border: 1px solid rgba(214,21,50,0.18);
    color: #f87171;
    text-transform: uppercase;
    letter-spacing: .06em;
}

.wkt-item__count {
    font-size: 10px;
    color: rgba(255,255,255,0.30);
    font-weight: 600;
}

/* Botões ação */
.wkt-item__actions {
    display: flex;
    align-items: center;
    gap: 3px;
    flex-shrink: 0;
}

.wkt-btn {
    width: 28px;
    height: 28px;
    border-radius: 7px;
    border: 1px solid rgba(255,255,255,0.07);
    background: rgba(255,255,255,0.03);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: rgba(255,255,255,0.45);
    text-decoration: none;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
}

.wkt-btn:hover { transform: translateY(-1px); }

.wkt-btn--toggle:hover,
.wkt-btn--toggle.is-open {
    background: rgba(59,130,246,0.12);
    border-color: rgba(59,130,246,0.25);
    color: #93c5fd;
}

.wkt-btn--edit:hover {
    background: rgba(74,222,128,0.10);
    border-color: rgba(74,222,128,0.22);
    color: #4ade80;
}

.wkt-btn--del:hover {
    background: rgba(214,21,50,0.12);
    border-color: rgba(214,21,50,0.28);
}

/* Exercícios expansíveis */
.wkt-item__exercises {
    margin: 0 0 4px 39px;
    border-radius: 9px;
    overflow: hidden;
}

.wkt-item__empty {
    padding: 8px 10px;
    font-size: 11px;
    color: rgba(255,255,255,0.28);
}

/* Empty state treinos */
.wkt-empty {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 6px;
    color: rgba(255,255,255,0.25);
    font-size: 11.5px;
    font-weight: 600;
}

/* Tabela de exercícios */
.ex-table {
    font-size: 11px;
    border-radius: 9px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.06);
    margin-top: 2px;
}

.ex-table__head {
    display: grid;
    grid-template-columns: 2fr 1.2fr 0.6fr 0.6fr 0.6fr;
    padding: 6px 10px;
    background: rgba(255,255,255,0.04);
    color: rgba(255,255,255,0.35);
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    gap: 6px;
}

.ex-table__row {
    display: grid;
    grid-template-columns: 2fr 1.2fr 0.6fr 0.6fr 0.6fr;
    padding: 7px 10px;
    gap: 6px;
    align-items: center;
    border-top: 1px solid rgba(255,255,255,0.04);
    transition: background .12s;
}

.ex-table__row:hover { background: rgba(255,255,255,0.03); }

.ex-table__name {
    font-weight: 600;
    color: #e8e8e8;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ex-table__group {
    color: rgba(255,255,255,0.40);
    font-size: 10px;
    text-transform: capitalize;
}

/* Chips */
.chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 2px 7px;
    border-radius: 99px;
    font-size: 10px;
    font-weight: 700;
    white-space: nowrap;
}

.chip--series {
    background: rgba(99,102,241,0.12);
    border: 1px solid rgba(99,102,241,0.22);
    color: #a5b4fc;
}

.chip--reps {
    background: rgba(234,179,8,0.10);
    border: 1px solid rgba(234,179,8,0.20);
    color: #fde68a;
}

.chip--rest {
    background: rgba(20,184,166,0.10);
    border: 1px solid rgba(20,184,166,0.20);
    color: #5eead4;
}

/* Rodapé do card */
.student-card__footer {
    padding: 10px 12px;
    display: flex;
    justify-content: flex-end;
    border-top: 1px solid rgba(255,255,255,0.05);
}

.wkt-btn-create {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 99px;
    background: #d61532;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none;
    letter-spacing: .04em;
    transition: opacity .18s, transform .15s;
}

.wkt-btn-create:hover {
    opacity: .86;
    transform: translateY(-1px);
}

/* Painel gerente — linha de treino */
.mgr-workout-row {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 8px;
    border-radius: 8px;
    transition: background .12s;
}

.mgr-workout-row:hover { background: rgba(255,255,255,0.03); }

.mgr-workout-icon {
    width: 26px;
    height: 26px;
    flex-shrink: 0;
    border-radius: 7px;
    background: rgba(214,21,50,0.08);
    border: 1px solid rgba(214,21,50,0.14);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f87171;
}

.mgr-workout-icon svg { width: 12px; height: 12px; }

.mgr-workout-name {
    flex: 1;
    font-size: 12px;
    font-weight: 700;
    color: #e8e8e8;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.mgr-workout-btns {
    display: flex;
    align-items: center;
    gap: 3px;
    flex-shrink: 0;
}

/* Light mode */
[data-theme="light"] .wkt-list {
    background: rgba(0,0,0,0.015);
    border-color: rgba(0,0,0,0.06);
}

[data-theme="light"] .wkt-item__row:hover { background: rgba(0,0,0,0.025); }

[data-theme="light"] .wkt-item__icon {
    background: rgba(214,21,50,0.06);
    border-color: rgba(214,21,50,0.12);
    color: #c1121f;
}

[data-theme="light"] .wkt-item__name { color: #111; }
[data-theme="light"] .wkt-item__count { color: rgba(0,0,0,0.35); }

[data-theme="light"] .wkt-item__day {
    background: rgba(214,21,50,0.07);
    border-color: rgba(214,21,50,0.14);
    color: #b91c1c;
}

[data-theme="light"] .wkt-btn {
    background: rgba(0,0,0,0.03);
    border-color: rgba(0,0,0,0.08);
    color: rgba(0,0,0,0.40);
}

[data-theme="light"] .wkt-btn--toggle:hover,
[data-theme="light"] .wkt-btn--toggle.is-open {
    background: rgba(59,130,246,0.08);
    border-color: rgba(59,130,246,0.20);
    color: #1d4ed8;
}

[data-theme="light"] .wkt-btn--edit:hover {
    background: rgba(22,163,74,0.08);
    border-color: rgba(22,163,74,0.18);
    color: #15803d;
}

[data-theme="light"] .wkt-btn--del:hover {
    background: rgba(214,21,50,0.08);
    border-color: rgba(214,21,50,0.20);
}

[data-theme="light"] .wkt-empty { color: rgba(0,0,0,0.28); }

[data-theme="light"] .student-card__footer {
    border-top-color: rgba(0,0,0,0.06);
}

[data-theme="light"] .ex-table {
    border-color: rgba(0,0,0,0.08);
}

[data-theme="light"] .ex-table__head {
    background: rgba(0,0,0,0.03);
    color: rgba(0,0,0,0.40);
}

[data-theme="light"] .ex-table__row { border-top-color: rgba(0,0,0,0.05); }
[data-theme="light"] .ex-table__row:hover { background: rgba(0,0,0,0.02); }
[data-theme="light"] .ex-table__name { color: #111; }
[data-theme="light"] .ex-table__group { color: rgba(0,0,0,0.40); }

[data-theme="light"] .mgr-workout-icon {
    background: rgba(214,21,50,0.06);
    border-color: rgba(214,21,50,0.12);
    color: #c1121f;
}

[data-theme="light"] .mgr-workout-name { color: #111; }
[data-theme="light"] .mgr-workout-row:hover { background: rgba(0,0,0,0.025); }
</style>

<script>
function toggleWkt(id, btn) {
    const el = document.getElementById(id);
    if (!el) return;
    const isOpen = el.style.display !== 'none';
    el.style.display = isOpen ? 'none' : 'block';
    btn.classList.toggle('is-open', !isOpen);
}
</script>

</x-app-layout>