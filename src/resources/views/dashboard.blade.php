<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endpush

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div style="margin-bottom:16px; padding:12px 16px; background:rgba(74,222,128,0.08); border:1px solid rgba(74,222,128,0.2); border-radius:10px; color:#4ade80; font-size:13px; font-weight:600;">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ══════════════════════════════════════════════════════════════
                 VISÃO DO GERENTE
            ══════════════════════════════════════════════════════════════ --}}
            @if(Auth::user()->isManager())
                <div class="dash-hero">
                    <div class="dash-hero__ring"></div>
                    <div class="dash-hero__inner">
                        <div>
                            <div class="dash-hero__eyebrow">Gerenciamento</div>
                            <h2 class="dash-hero__title">Painel Geral</h2>
                            <p class="dash-hero__sub">Visão completa da academia</p>
                        </div>

                        <div class="dash-hero__right">
                            <span class="dash-hero__pulse">
                                <span class="dash-hero__pulse-dot"></span>
                                GERENTE
                            </span>

                            <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                                <a
                                    href="{{ route('plans.create') }}"
                                    class="btn-save"
                                    style="text-decoration:none; display:inline-flex; align-items:center; gap:7px; font-size:12px; padding:9px 18px;"
                                >
                                    <svg width="11" height="11" viewBox="0 0 12 12" fill="none"
                                         style="stroke:#fff; stroke-width:2.5; stroke-linecap:round;">
                                        <line x1="6" y1="1" x2="6" y2="11"/>
                                        <line x1="1" y1="6" x2="11" y2="6"/>
                                    </svg>
                                    Novo Plano
                                </a>

                                <a
                                    href="{{ route('instructors.create') }}"
                                    class="btn-ghost"
                                    style="text-decoration:none; display:inline-flex; align-items:center; gap:7px; font-size:12px; padding:9px 18px;"
                                >
                                    + Novo Instrutor
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARDS RESUMO --}}
                <div class="mgr-stats">
                    <div class="mgr-stat-card">
                        <span class="mgr-stat-card__label">Total de alunos</span>
                        <strong class="mgr-stat-card__value">{{ $totalStudents ?? 0 }}</strong>
                        <span class="mgr-stat-card__sub">cadastrados</span>
                    </div>

                    <div class="mgr-stat-card mgr-stat-card--green">
                        <span class="mgr-stat-card__label">Ativos</span>
                        <strong class="mgr-stat-card__value">{{ $activeStudents ?? 0 }}</strong>
                        <span class="mgr-stat-card__sub">em dia</span>
                    </div>

                    <div class="mgr-stat-card">
                        <span class="mgr-stat-card__label">Instrutores</span>
                        <strong class="mgr-stat-card__value">{{ $totalInstructors ?? 0 }}</strong>
                        <span class="mgr-stat-card__sub">cadastrados</span>
                    </div>
                </div>

                {{-- ABAS --}}
                <div class="mgr-tabs">
                    <button
                        type="button"
                        class="mgr-tab is-active"
                        onclick="showManagerSection('students-section', this)"
                    >
                        Alunos
                        <span class="mgr-tab__count">{{ $totalStudents ?? 0 }}</span>
                    </button>

                    <button
                        type="button"
                        class="mgr-tab"
                        onclick="showManagerSection('instructors-section', this)"
                    >
                        Instrutores e Treinos
                        <span class="mgr-tab__count">{{ $totalInstructors ?? 0 }}</span>
                    </button>

                    <button
                        type="button"
                        class="mgr-tab"
                        onclick="showManagerSection('plans-section', this)"
                    >
                        Planos
                        <span class="mgr-tab__count">{{ $totalPlans ?? 0 }}</span>
                    </button>

                    <button
                        type="button"
                        class="mgr-tab"
                        onclick="showManagerSection('frequency-section', this)"
                    >
                        Frequência
                    </button>

                    <button
                        type="button"
                        class="mgr-tab"
                        onclick="showManagerSection('reports-section', this)"
                    >
                        Relatórios
                        <span class="mgr-tab__count">3</span>
                    </button>
                </div>

                {{-- SEÇÃO ALUNOS --}}
                <div id="students-section" class="mgr-section">
                    <div class="mgr-section-head">
                        <div>
                            <p class="section-label" style="margin-bottom:0;">ALUNOS</p>
                        </div>

                        <div class="mgr-filters">
                            <button type="button" class="mgr-filter is-active" onclick="filterStudents('all', this)">Todos</button>
                            <button type="button" class="mgr-filter" onclick="filterStudents('ativo', this)">Ativo</button>
                            <button type="button" class="mgr-filter" onclick="filterStudents('inadimplente', this)">Devendo</button>
                            <button type="button" class="mgr-filter" onclick="filterStudents('sem_matricula', this)">Sem matrícula</button>
                        </div>

                        <input
                            type="text"
                            id="studentSearch"
                            class="mgr-search"
                            placeholder="Buscar aluno..."
                            oninput="searchStudents()"
                        >
                    </div>

                    <div class="mgr-table-wrap">
                        <table class="mgr-table">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Instrutor</th>
                                    <th>Plano</th>
                                    <th>Vencimento</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($studentsData as $s)
                                    <tr
                                        class="student-row"
                                        data-name="{{ \Illuminate\Support\Str::lower($s['name']) }}"
                                        data-email="{{ \Illuminate\Support\Str::lower($s['email']) }}"
                                        data-status="{{ $s['status'] }}"
                                    >
                                        <td>
                                            <div class="mgr-student-cell">
                                                <div class="mgr-student-cell__avatar">
                                                    {{ mb_strtoupper(mb_substr($s['name'], 0, 2)) }}
                                                </div>
                                                <div class="mgr-student-cell__content">
                                                    <span class="mgr-student-cell__name">{{ $s['name'] }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="mgr-student-cell__email">{{ $s['email'] }}</span></td>
                                        <td>
                                            @if($s['status'] === 'ativo')
                                                <span class="mgr-badge-ok">Ativo</span>
                                            @elseif($s['status'] === 'inadimplente')
                                                <span class="mgr-badge-bad">Devendo</span>
                                            @else
                                                <span class="mgr-badge-neutral">Sem matrícula</span>
                                            @endif
                                        </td>
                                        <td>{{ $s['instructor'] ?? '—' }}</td>
                                        <td>{{ $s['plan'] ?? '—' }}</td>
                                        <td>{{ $s['plan_end'] ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr id="empty-students-row">
                                        <td colspan="6" style="text-align:center; padding:28px; color:var(--text-muted); font-size:13px;">
                                            Nenhum aluno cadastrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- SEÇÃO INSTRUTORES --}}
                <div id="instructors-section" class="mgr-section" style="display:none;">
                    <div class="mgr-grid">
                        @forelse($instructors as $instructor)
                            <div class="mgr-card">
                                <div class="mgr-card__left">
                                    <div class="mgr-card__left-top">
                                        <div class="mgr-inst-big-avatar">
                                            {{ mb_strtoupper(mb_substr($instructor->user->name, 0, 2)) }}
                                        </div>
                                        <div class="mgr-inst-label">Instrutor</div>
                                        <div class="mgr-inst-name">{{ $instructor->user->name }}</div>
                                        <span class="mgr-inst-specialty">
                                            <svg viewBox="0 0 24 24">
                                                <rect x="2" y="10" width="3" height="4" rx="1"/>
                                                <rect x="19" y="10" width="3" height="4" rx="1"/>
                                                <rect x="5" y="8" width="3" height="8" rx="1"/>
                                                <rect x="16" y="8" width="3" height="8" rx="1"/>
                                                <rect x="8" y="11" width="8" height="2" rx="1"/>
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

                                <div class="mgr-card__right">
                                    <div class="mgr-right-header">
                                        <span class="mgr-right-title">Alunos</span>
                                        <span class="mgr-right-count">{{ $instructor->students->count() }} vinculado(s)</span>
                                    </div>

                                    @forelse($instructor->students as $student)
                                        <div class="mgr-student">
                                            <div class="mgr-student-row">
                                                <div class="mgr-student-av">
                                                    {{ mb_strtoupper(mb_substr($student->user->name, 0, 2)) }}
                                                </div>
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
                                                    <div class="mgr-workout-shell">
                                                        <div
                                                            class="mgr-workout-row"
                                                            onclick="toggleWorkoutMgr('workout-{{ $workout->id }}', this)"
                                                            role="button"
                                                            tabindex="0"
                                                            onkeydown="handleWorkoutKey(event, 'workout-{{ $workout->id }}', this)"
                                                        >
                                                            <div class="mgr-workout-main">
                                                                <div class="mgr-workout-icon">
                                                                    <svg viewBox="0 0 24 24">
                                                                        <rect x="2" y="10" width="3" height="4" rx="1"/>
                                                                        <rect x="19" y="10" width="3" height="4" rx="1"/>
                                                                        <rect x="5" y="8" width="3" height="8" rx="1"/>
                                                                        <rect x="16" y="8" width="3" height="8" rx="1"/>
                                                                        <rect x="8" y="11" width="8" height="2" rx="1"/>
                                                                    </svg>
                                                                </div>
                                                                <div class="mgr-workout-meta">
                                                                    <span class="mgr-workout-name">{{ $workout->name }}</span>
                                                                    <span class="mgr-workout-sub">{{ $workout->workoutExercises->count() }} exercício(s)</span>
                                                                </div>
                                                            </div>
                                                            <div class="mgr-workout-actions" onclick="event.stopPropagation()">
                                                                <a href="{{ route('workouts.edit', [$workout->id, 'student_id' => $student->id]) }}"
                                                                   class="mgr-btn-sm mgr-btn-edit-workout">
                                                                    <svg width="10" height="10" viewBox="0 0 14 14" fill="none"
                                                                         style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; display:inline-block; vertical-align:middle; margin-right:2px;">
                                                                        <path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/>
                                                                    </svg>
                                                                    Editar
                                                                </a>
                                                                <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST"
                                                                      style="margin:0;" onclick="event.stopPropagation()">
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

                                                        <div id="workout-{{ $workout->id }}" class="mgr-workout-collapse">
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
                </div>

                {{-- ══════════════════════════════════════════════════════════════
                     SEÇÃO PLANOS
                ══════════════════════════════════════════════════════════════ --}}
                <div id="plans-section" class="mgr-section" style="display:none;">

                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
                        <p class="section-label" style="margin-bottom:0;">PLANOS CADASTRADOS</p>
                        <a href="{{ route('plans.create') }}" class="btn-save"
                           style="text-decoration:none; font-size:12px; padding:9px 18px; display:inline-flex; align-items:center; gap:6px;">
                            <svg width="11" height="11" viewBox="0 0 12 12" fill="none"
                                 style="stroke:#fff; stroke-width:2.5; stroke-linecap:round;">
                                <line x1="6" y1="1" x2="6" y2="11"/>
                                <line x1="1" y1="6" x2="11" y2="6"/>
                            </svg>
                            Novo Plano
                        </a>
                    </div>

                    @if(isset($plans) && $plans->count())
                        <div style="display:flex; flex-direction:column; gap:12px;">
                            @foreach($plans as $plan)
                                <div class="dash-plan-card">
                                    <div class="dash-plan-card__left">
                                        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                                            <div>
                                                <p class="dash-plan-card__name">{{ $plan->name }}</p>
                                                @if($plan->description)
                                                    <p class="dash-plan-card__desc">{{ $plan->description }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        @if($plan->benefits)
                                            <div class="dash-plan-card__benefits">
                                                @foreach(array_slice(explode(',', $plan->benefits), 0, 3) as $benefit)
                                                    <span class="dash-plan-card__benefit-chip">{{ trim($benefit) }}</span>
                                                @endforeach
                                                @if(count(explode(',', $plan->benefits)) > 3)
                                                    <span class="dash-plan-card__benefit-chip" style="opacity:.5;">
                                                        +{{ count(explode(',', $plan->benefits)) - 3 }} mais
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <div class="dash-plan-card__right">
                                        <div style="text-align:right;">
                                            <p class="dash-plan-card__price">R$ {{ number_format($plan->price, 2, ',', '.') }}</p>
                                            <p class="dash-plan-card__duration">{{ $plan->duration_days }} dias</p>
                                        </div>

                                        <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end; margin-top:12px;">
                                            @if($plan->status === 'inactive')
                                                <span class="mgr-badge-bad">Inativo</span>
                                            @else
                                                <span class="mgr-badge-ok">Ativo</span>
                                            @endif
                                            <a href="{{ route('plans.edit', $plan->id) }}"
                                               class="mgr-btn-sm mgr-btn-edit-workout"
                                               style="text-decoration:none;">
                                                <svg width="10" height="10" viewBox="0 0 14 14" fill="none"
                                                     style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; display:inline-block; vertical-align:middle; margin-right:2px;">
                                                    <path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/>
                                                </svg>
                                                Editar
                                            </a>
                                            @if($plan->status === 'active')
                                                <form action="{{ route('plans.destroy', $plan->id) }}" method="POST" style="margin:0;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="mgr-btn-del"
                                                            onclick="return confirm('Inativar este plano?')"
                                                            title="Inativar plano">
                                                        <svg width="11" height="11" viewBox="0 0 14 16" fill="none"
                                                             style="stroke:#f87171; stroke-width:1.8; stroke-linecap:round;">
                                                            <path d="M1 3.5h12M4.5 3.5V2a.5.5 0 01.5-.5h3a.5.5 0 01.5.5v1.5M5.5 7v5M8.5 7v5M2.5 3.5l.9 10a.5.5 0 00.5.5h6.2a.5.5 0 00.5-.5l.9-10"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('plans.restore', $plan->id) }}" method="POST" style="margin:0;">
                                                    @csrf
                                                    <button type="submit" class="mgr-btn-sm" style="color:rgba(74,222,128,.7); border-color:rgba(34,197,94,.25);">
                                                        Restaurar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state" style="padding:3rem 1rem;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none"
                                 style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 16px; display:block; opacity:.20;">
                                <rect x="3" y="3" width="18" height="18" rx="3"/>
                                <path d="M3 9h18M9 21V9"/>
                            </svg>
                            <p>Nenhum plano cadastrado ainda.</p>
                            <a href="{{ route('plans.create') }}" class="btn-save"
                               style="display:inline-flex; margin-top:18px; text-decoration:none; align-items:center; gap:6px;">
                                <svg width="11" height="11" viewBox="0 0 12 12" fill="none"
                                     style="stroke:#fff; stroke-width:2.5; stroke-linecap:round;">
                                    <line x1="6" y1="1" x2="6" y2="11"/>
                                    <line x1="1" y1="6" x2="11" y2="6"/>
                                </svg>
                                Criar Primeiro Plano
                            </a>
                        </div>
                    @endif
                </div>

                {{-- ══════════════════════════════════════════════════════════════
                     SEÇÃO RELATÓRIOS
                ══════════════════════════════════════════════════════════════ --}}
                <div id="reports-section" class="mgr-section" style="display:none;">
                    <div style="margin-bottom:20px;">
                        <p class="section-label">RELATÓRIOS</p>
                    </div>

                    <div class="report-cards-grid">
                        <a href="{{ route('reports.plans.comparative') }}" class="report-card report-card--red">
                            <div class="report-card__body">
                                <div class="report-card__icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="stroke:#f87171; stroke-width:1.8; stroke-linecap:round;">
                                        <rect x="3" y="3" width="18" height="18" rx="3"/>
                                        <path d="M3 9h18M9 21V9"/>
                                    </svg>
                                </div>
                                <p class="report-card__title">Comparativo de Planos</p>
                                <p class="report-card__desc">Planos ativos lado a lado com preço, duração, benefícios e alunos matriculados.</p>
                            </div>
                            <div class="report-card__footer">
                                <span class="report-card__footer-label">Abrir relatório</span>
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:var(--text-muted); stroke-width:2; stroke-linecap:round; stroke-linejoin:round;">
                                    <path d="M2.5 7h9M7.5 3l4 4-4 4"/>
                                </svg>
                            </div>
                        </a>

                        <a href="{{ route('reports.plans.cancellations') }}" class="report-card report-card--pink">
                            <div class="report-card__body">
                                <div class="report-card__icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="stroke:#f87171; stroke-width:1.8; stroke-linecap:round;">
                                        <circle cx="12" cy="12" r="9"/>
                                        <path d="M15 9l-6 6M9 9l6 6"/>
                                    </svg>
                                </div>
                                <p class="report-card__title">Cancelamentos</p>
                                <p class="report-card__desc">Histórico de cancelamentos com data e filtro por período.</p>
                            </div>
                            <div class="report-card__footer">
                                <span class="report-card__footer-label">Abrir relatório</span>
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:var(--text-muted); stroke-width:2; stroke-linecap:round; stroke-linejoin:round;">
                                    <path d="M2.5 7h9M7.5 3l4 4-4 4"/>
                                </svg>
                            </div>
                        </a>

                        <a href="{{ route('reports.plans.loyalty') }}" class="report-card report-card--green">
                            <div class="report-card__body">
                                <div class="report-card__icon">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="stroke:#4ade80; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </div>
                                <p class="report-card__title">Fidelidade</p>
                                <p class="report-card__desc">Ranking dos alunos mais fiéis por tempo de permanência ativo.</p>
                            </div>
                            <div class="report-card__footer">
                                <span class="report-card__footer-label">Abrir relatório</span>
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:var(--text-muted); stroke-width:2; stroke-linecap:round; stroke-linejoin:round;">
                                    <path d="M2.5 7h9M7.5 3l4 4-4 4"/>
                                </svg>
                            </div>
                        </a>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════════
                     SEÇÃO FREQUÊNCIA
                ══════════════════════════════════════════════════════════════ --}}
                <div id="frequency-section" class="mgr-section" style="display:none;">
                    <div style="margin-bottom:20px;">
                        <p class="section-label">FREQUÊNCIA</p>
                    </div>

                    {{-- CARDS RESUMO --}}
                    <div class="hm-summary" id="hm-summary" style="margin-bottom:24px;">
                        <div class="hm-summary-card">
                            <span class="hm-summary-card__label">Total de registros</span>
                            <strong class="hm-summary-card__value" id="hm-total">—</strong>
                            <span class="hm-summary-card__sub">últimos 90 dias</span>
                        </div>
                        <div class="hm-summary-card hm-summary-card--red">
                            <span class="hm-summary-card__label">Dia mais movimentado</span>
                            <strong class="hm-summary-card__value" id="hm-peak-day">—</strong>
                            <span class="hm-summary-card__sub">maior volume</span>
                        </div>
                        <div class="hm-summary-card hm-summary-card--green">
                            <span class="hm-summary-card__label">Horário de pico</span>
                            <strong class="hm-summary-card__value" id="hm-peak-hour">—</strong>
                            <span class="hm-summary-card__sub">hora mais frequente</span>
                        </div>
                    </div>

                    {{-- HEATMAP --}}
                    <div class="hm-wrap">
                        <div class="hm-header">
                            <p class="section-label" style="margin:0;">MAPA DE CALOR — PRESENÇA POR DIA E HORA</p>
                            <div class="hm-legend">
                                <span class="hm-legend__label">Menos</span>
                                <div class="hm-legend__bar">
                                    <div class="hm-legend__cell" style="--intensity:0"></div>
                                    <div class="hm-legend__cell" style="--intensity:0.25"></div>
                                    <div class="hm-legend__cell" style="--intensity:0.5"></div>
                                    <div class="hm-legend__cell" style="--intensity:0.75"></div>
                                    <div class="hm-legend__cell" style="--intensity:1"></div>
                                </div>
                                <span class="hm-legend__label">Mais</span>
                            </div>
                        </div>

                        {{-- Skeleton enquanto carrega --}}
                        <div id="hm-skeleton" class="hm-skeleton-wrap">
                            <div class="hm-skeleton-grid">
                                @for($i = 0; $i < 7; $i++)
                                    <div class="hm-skeleton-row">
                                        <div class="sk hm-skeleton-label"></div>
                                        @for($j = 0; $j < 24; $j++)
                                            <div class="sk hm-skeleton-cell"></div>
                                        @endfor
                                    </div>
                                @endfor
                            </div>
                        </div>

                        {{-- Grid real (injetado via JS) --}}
                        <div id="hm-grid" class="hm-grid" style="display:none;">
                            {{-- horas no topo --}}
                            <div class="hm-hour-row">
                                <div class="hm-day-label"></div>
                                @for($h = 0; $h < 24; $h++)
                                    <div class="hm-hour-label">{{ sprintf('%02d', $h) }}</div>
                                @endfor
                            </div>
                            {{-- linhas injetadas pelo JS --}}
                            <div id="hm-rows"></div>
                        </div>

                        {{-- Tooltip --}}
                        <div id="hm-tooltip" class="hm-tooltip" style="display:none;"></div>

                        {{-- Empty state --}}
                        <div id="hm-empty" class="hm-empty" style="display:none;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none"
                                 style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 14px; display:block; opacity:.20;">
                                <rect x="3" y="3" width="18" height="18" rx="3"/>
                                <path d="M3 9h18M9 21V9"/>
                            </svg>
                            <p>Nenhum registro de frequência ainda.</p>
                            <p style="font-size:13px; margin-top:6px; opacity:.45;">Os dados aparecerão aqui conforme os alunos registrarem presença.</p>
                        </div>
                    </div>
                </div>

            {{-- ══════════════════════════════════════════════════════════════
                 VISÃO DO INSTRUTOR
            ══════════════════════════════════════════════════════════════ --}}
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
                                                <button type="button" class="btn-workout-action"
                                                        style="font-size:11px; padding:4px 12px;"
                                                        onclick="toggleWorkout('workout-inst-{{ $workout->id }}')"
                                                        id="btn-workout-inst-{{ $workout->id }}">
                                                    Ver exercícios ▾
                                                </button>
                                            </div>
                                        </div>

                                        <div id="workout-inst-{{ $workout->id }}" style="display:none; margin-top:10px;">
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
                                                <p style="font-size:13px; color:var(--text-muted); opacity:.6;">Nenhum exercício neste treino.</p>
                                            @endif
                                        </div>

                                        <div style="display:flex; gap:8px; margin-top:12px; flex-wrap:wrap;">
                                            <a href="{{ route('workouts.edit', [$workout->id, 'student_id' => $student->id]) }}"
                                               class="btn-workout-action">
                                                <svg width="11" height="11" viewBox="0 0 14 14" fill="none"
                                                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/>
                                                </svg>
                                                Editar
                                            </a>
                                            <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                <button type="submit" class="btn-workout-action"
                                                        style="border-color:rgba(214,21,50,.6); color:#f87171;">
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

            {{-- ══════════════════════════════════════════════════════════════
                 VISÃO DO ALUNO SEM MATRÍCULA
            ══════════════════════════════════════════════════════════════ --}}
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
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <p>Você ainda não possui uma matrícula ativa.</p>
                    <p style="font-size:13px; margin-top:6px; opacity:.45;">Escolha um plano para liberar o acesso completo.</p>
                    <a href="{{ route('enrollment.index') }}" class="btn-save"
                       style="text-decoration:none; display:inline-block; margin-top:20px;">
                        Ver Planos
                    </a>
                </div>

            {{-- ══════════════════════════════════════════════════════════════
                 VISÃO DO ALUNO COM MATRÍCULA
            ══════════════════════════════════════════════════════════════ --}}
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

                {{-- ── BANNER STATUS DE ACESSO ── --}}
                @php $studentAccess = Auth::user()->student; @endphp
                @if($studentAccess && $studentAccess->status !== 'active')
                    <div style="
                        display:flex; align-items:center; gap:14px;
                        padding:14px 20px; border-radius:14px; margin-bottom:16px;
                        {{ $studentAccess->status === 'blocked'
                            ? 'background:rgba(214,21,50,0.08);border:1px solid rgba(214,21,50,0.22);'
                            : 'background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.22);' }}
                    ">
                        @if($studentAccess->status === 'blocked')
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <div style="flex:1;min-width:0;">
                                <span style="font-size:12px;font-weight:800;color:#f87171;text-transform:uppercase;letter-spacing:.08em;">Acesso Bloqueado</span>
                                <p style="font-size:12px;color:var(--text-muted);margin:2px 0 0;">Entre em contato com a administração para mais informações.</p>
                            </div>
                        @else
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <div style="flex:1;min-width:0;">
                                <span style="font-size:12px;font-weight:800;color:#fbbf24;text-transform:uppercase;letter-spacing:.08em;">Pagamento Pendente</span>
                                <p style="font-size:12px;color:var(--text-muted);margin:2px 0 0;">Regularize sua situação para manter o acesso ativo.</p>
                            </div>
                            <a href="{{ route('billing.index') }}" style="font-size:11px;font-weight:700;color:#fbbf24;text-decoration:none;border:1px solid rgba(251,191,36,.30);padding:5px 12px;border-radius:99px;white-space:nowrap;">Regularizar</a>
                        @endif
                    </div>
                @endif

                {{-- ── REGISTRO DE PRESENÇA ──────────────────────────────────────────── --}}
                <div class="freq-card" style="flex-direction:column; align-items:stretch; gap:0; padding:0; overflow:hidden;">

                    {{-- Topo: ícone + título + stats + botão --}}
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:20px; padding:20px 24px 16px;">
                        <div style="display:flex; align-items:center; gap:14px; flex:1;">
                            <div class="freq-card__icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                     style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </div>
                            <div>
                                <p class="freq-card__title">Registrar Presença</p>
                                <p class="freq-card__sub" id="freq-sub">
                                    @if(isset($checkedInToday) && $checkedInToday)
                                        Você já registrou presença hoje
                                    @else
                                        Marque sua presença na academia
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Stats + Botão do lado direito --}}
                        <div style="display:flex; align-items:center; gap:16px; flex-shrink:0;">
                            <div class="freq-card__stats" style="display:flex; align-items:center; gap:12px;">
                                <div class="freq-card__stat">
                                    <span class="freq-card__stat-value" id="freq-count">{{ $frequencyThisMonth ?? 0 }}</span>
                                    <span class="freq-card__stat-label">este mês</span>
                                </div>
                                <div class="freq-card__stat-divider"></div>
                                <div class="freq-card__stat">
                                    <span class="freq-card__stat-value">
                                        {{ isset($lastFrequency) && $lastFrequency ? $lastFrequency->created_at->format('d/m') : '—' }}
                                    </span>
                                    <span class="freq-card__stat-label">última vez</span>
                                </div>
                            </div>

                            @if(isset($checkedInToday) && $checkedInToday)
                                <button class="freq-btn freq-btn--done" disabled id="freq-btn" style="flex-shrink:0;">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                         style="stroke:currentColor; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round;">
                                        <polyline points="2,7 6,11 12,3"/>
                                    </svg>
                                    Presente
                                </button>
                            @else
                                <button class="freq-btn freq-btn--active" id="freq-btn" onclick="registerFrequency()" style="flex-shrink:0;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                         style="stroke:currentColor; stroke-width:2.2; stroke-linecap:round; stroke-linejoin:round;">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                        <circle cx="12" cy="9" r="2.5"/>
                                    </svg>
                                    Registrar
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Divisor --}}
                    <div style="height:1px; background:rgba(128,128,128,0.15); margin:0 24px;"></div>

                    {{-- Dias da semana --}}
                    <div style="padding:16px 24px;">
                        <p style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em;
                                  color:var(--text-muted); margin-bottom:10px;">
                            Presenças esta semana
                        </p>
                        @php
                            $days = [
                                ['label' => 'DOM', 'num' => 0],
                                ['label' => 'SEG', 'num' => 1],
                                ['label' => 'TER', 'num' => 2],
                                ['label' => 'QUA', 'num' => 3],
                                ['label' => 'QUI', 'num' => 4],
                                ['label' => 'SEX', 'num' => 5],
                                ['label' => 'SÁB', 'num' => 6],
                            ];
                            $todayNum = \Carbon\Carbon::now()->dayOfWeek;
                        @endphp
                        <div style="display:flex; gap:6px;">
                            @foreach($days as $day)
                                @php
                                    $present = in_array($day['num'], $frequencyThisWeek ?? []);
                                    $isToday = $day['num'] === $todayNum;
                                @endphp
                                <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:5px;">
                                    <span class="freq-day__label">{{ $day['label'] }}</span>
                                    <div {{ $isToday && !$present ? 'data-today-dot' : '' }}
                                         class="freq-day__dot {{ $present && $isToday ? 'freq-day__dot--today' : ($present ? 'freq-day__dot--present' : '') }}">
                                        {{ $present ? '✓' : '·' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Divisor --}}
                    <div style="height:1px; background:rgba(128,128,128,0.15); margin:0 24px;"></div>
                </div>

                {{-- Toast --}}
                <div class="freq-toast" id="freq-toast" style="display:none;"></div>

                {{-- AÇÕES RÁPIDAS DO ALUNO: Renovar + Pagar mensalidade --}}
                <div class="student-quick-actions">
                    <a href="{{ route('plans.renewals') }}" class="student-action-card student-action-card--blue">
                        <div class="student-action-card__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                 style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;">
                                <path d="M1 4v6h6"/>
                                <path d="M23 20v-6h-6"/>
                                <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10M23 14l-4.64 4.36A9 9 0 0 1 3.51 15"/>
                            </svg>
                        </div>
                        <div class="student-action-card__content">
                            <p class="student-action-card__label">Renovar Plano</p>
                            <p class="student-action-card__hint">Estenda sua assinatura</p>
                        </div>
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                             style="stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; opacity:.45; flex-shrink:0;">
                            <path d="M2.5 7h9M7.5 3l4 4-4 4"/>
                        </svg>
                    </a>

                    <a href="{{ route('billing.index') }}" class="student-action-card student-action-card--green">
                        <div class="student-action-card__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                 style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;">
                                <rect x="2" y="5" width="20" height="14" rx="2"/>
                                <path d="M2 10h20"/>
                            </svg>
                        </div>
                        <div class="student-action-card__content">
                            <p class="student-action-card__label">Pagar Mensalidade</p>
                            <p class="student-action-card__hint">Ver e processar pagamentos</p>
                        </div>
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                             style="stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; opacity:.45; flex-shrink:0;">
                            <path d="M2.5 7h9M7.5 3l4 4-4 4"/>
                        </svg>
                    </a>
                </div>

                @if(isset($workout))
                    <div class="dash-stats">
                        <div class="dash-stat dash-stat--red">
                            <div class="dash-stat__bg-icon">⚡</div>
                            <div class="dash-stat__header">
                                <span class="dash-stat__dot"></span>
                                <span class="dash-stat__label">Séries totais</span>
                            </div>
                            <div class="dash-stat__value">{{ $exercises->sum(fn($e) => (int) $e->sets) }}</div>
                        </div>
                        <div class="dash-stat dash-stat--blue">
                            <div class="dash-stat__bg-icon">🔁</div>
                            <div class="dash-stat__header">
                                <span class="dash-stat__dot"></span>
                                <span class="dash-stat__label">Reps totais</span>
                            </div>
                            <div class="dash-stat__value">{{ $exercises->sum(fn($e) => (int) $e->reps) }}</div>
                        </div>
                        <div class="dash-stat dash-stat--green">
                            <div class="dash-stat__bg-icon">🏋️</div>
                            <div class="dash-stat__header">
                                <span class="dash-stat__dot"></span>
                                <span class="dash-stat__label">Descanso</span>
                            </div>
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
                                <svg viewBox="0 0 14 14" fill="none"><path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/></svg>
                                Editar
                            </a>
                            <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-del">
                                    <svg viewBox="0 0 14 16" fill="none">
                                        <path d="M1 3.5h12M4.5 3.5V2a.5.5 0 01.5-.5h3a.5.5 0 01.5.5v1.5M5.5 7v5M8.5 7v5M2.5 3.5l.9 10a.5.5 0 00.5.5h6.2a.5.5 0 00.5-.5l.9-10"/>
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
                                        <button class="btn-play" title="Iniciar">
                                            <svg viewBox="0 0 10 12"><polygon points="0,0 10,6 0,12"/></svg>
                                        </button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-state"><p>Nenhum exercício encontrado.</p></div>
                    @endif
                @else
                    <div class="empty-state" style="padding:5rem 1rem;">
                        <svg width="56" height="56" viewBox="0 0 24 24" fill="none"
                             style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 18px; display:block; opacity:.20;">
                            <rect x="2" y="9" width="4" height="6" rx="1"/>
                            <rect x="18" y="9" width="4" height="6" rx="1"/>
                            <rect x="7" y="11" width="10" height="2" rx="1"/>
                        </svg>
                        <p>Nenhum treino disponível.</p>
                        <p style="font-size:13px; margin-top:6px; opacity:.45;">Crie seu primeiro treino para começar.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <script>
        function showManagerSection(sectionId, btn) {
            document.querySelectorAll('.mgr-section').forEach(s => s.style.display = 'none');
            const target = document.getElementById(sectionId);
            if (target) target.style.display = 'block';
            document.querySelectorAll('.mgr-tab').forEach(t => t.classList.remove('is-active'));
            if (btn) btn.classList.add('is-active');
        }

        function filterStudents(type, btn) {
            document.querySelectorAll('.mgr-filter').forEach(f => f.classList.remove('is-active'));
            if (btn) btn.classList.add('is-active');
            document.querySelectorAll('.student-row').forEach(row => {
                const matches = type === 'all' || row.dataset.status === type;
                row.style.display = matches ? '' : 'none';
            });
            searchStudents();
        }

        function searchStudents() {
            const input = document.getElementById('studentSearch');
            const query = input ? input.value.toLowerCase().trim() : '';
            const activeFilter = document.querySelector('.mgr-filter.is-active');
            const filterType = activeFilter ? activeFilter.textContent.trim().toLowerCase() : 'todos';
            let visibleCount = 0;

            document.querySelectorAll('.student-row').forEach(row => {
                const name  = row.dataset.name  || '';
                const email = row.dataset.email || '';
                let filterOk = true;
                if (filterType === 'ativo')              filterOk = row.dataset.status === 'ativo';
                else if (filterType === 'devendo')       filterOk = row.dataset.status === 'inadimplente';
                else if (filterType === 'sem matrícula') filterOk = row.dataset.status === 'sem_matricula';
                const show = filterOk && (name.includes(query) || email.includes(query));
                row.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });

            let emptyRow = document.getElementById('empty-students-row');
            if (!emptyRow) {
                const tbody = document.querySelector('.mgr-table tbody');
                emptyRow = document.createElement('tr');
                emptyRow.id = 'empty-students-row';
                emptyRow.innerHTML = `<td colspan="6" style="text-align:center; padding:28px; color:var(--text-muted); font-size:13px;"></td>`;
                tbody.appendChild(emptyRow);
            }

            const label = filterType === 'devendo'          ? 'Nenhum aluno devendo.'
                        : filterType === 'ativo'             ? 'Nenhum aluno ativo.'
                        : filterType === 'sem matrícula'     ? 'Nenhum aluno sem matrícula.'
                        : query                              ? 'Nenhum aluno encontrado.'
                        : 'Nenhum aluno cadastrado.';

            emptyRow.querySelector('td').textContent = label;
            emptyRow.style.display = visibleCount === 0 ? '' : 'none';
        }

        function toggleWorkoutMgr(id, row) {
            const el = document.getElementById(id);
            if (!el) return;
            const isOpen = el.classList.contains('is-open');
            if (isOpen) {
                el.style.maxHeight = el.scrollHeight + 'px';
                requestAnimationFrame(() => {
                    el.style.maxHeight = '0px';
                    el.classList.remove('is-open');
                    row.classList.remove('is-open');
                });
            } else {
                el.classList.add('is-open');
                row.classList.add('is-open');
                el.style.maxHeight = el.scrollHeight + 'px';
                el.addEventListener('transitionend', function handler() {
                    if (el.classList.contains('is-open')) el.style.maxHeight = 'none';
                    el.removeEventListener('transitionend', handler);
                });
            }
        }

        function handleWorkoutKey(event, id, row) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                toggleWorkoutMgr(id, row);
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

        async function registerFrequency() {
            const btn   = document.getElementById('freq-btn');
            const sub   = document.getElementById('freq-sub');
            const count = document.getElementById('freq-count');

            btn.disabled      = true;
            btn.style.opacity = '.6';

            try {
                const res = await fetch("{{ route('frequency.register') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                const data = await res.json();

                if (res.ok) {
                    btn.className = 'freq-btn freq-btn--done';
                    btn.innerHTML = `<svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                        style="stroke:currentColor;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round;">
                        <polyline points="2,7 6,11 12,3"/></svg> Presente`;

                    if (sub)   sub.textContent   = 'Você já registrou presença hoje';
                    if (count) count.textContent = parseInt(count.textContent || '0') + 1;

                    document.querySelectorAll('[data-today-dot]').forEach(d => {
                        d.style.background  = 'rgba(214,21,50,0.15)';
                        d.style.border      = '1px solid rgba(214,21,50,0.35)';
                        d.style.color       = '#f87171';
                        d.textContent       = '✓';
                    });

                    showFreqToast('Presença registrada com sucesso!', 'success');

                    // Recarregar a página após 1.5 segundos para atualizar os dados
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    btn.disabled      = false;
                    btn.style.opacity = '1';
                    showFreqToast(data.message || 'Erro ao registrar presença.', 'error');
                }
            } catch (e) {
                btn.disabled      = false;
                btn.style.opacity = '1';
                showFreqToast('Erro de conexão. Tente novamente.', 'error');
            }
        }

        function showFreqToast(msg, type) {
            const toast         = document.getElementById('freq-toast');
            toast.textContent   = msg;
            toast.style.display = 'flex';
            toast.className     = 'freq-toast' + (type === 'error' ? ' freq-toast--error' : '');

            setTimeout(() => {
                toast.style.opacity   = '0';
                toast.style.transform = 'translateY(-6px)';
                setTimeout(() => {
                    toast.style.display   = 'none';
                    toast.style.opacity   = '1';
                    toast.style.transform = 'none';
                }, 300);
            }, 3500);
        }

        // ── HEATMAP ──────────────────────────────────────────────────────────
        (function () {
            const DAYS  = ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'];
            const endpoint = "{{ route('reports.frequency.heatmap') }}";

            async function loadHeatmap() {
                try {
                    const res  = await fetch(endpoint, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const json = await res.json();
                    const data = json.data ?? [];

                    document.getElementById('hm-skeleton').style.display = 'none';

                    if (!data.length || data.every(d => d.count === 0)) {
                        document.getElementById('hm-empty').style.display = 'block';
                        return;
                    }

                    const maxVal   = Math.max(...data.map(d => d.count), 1);
                    const total    = data.reduce((s, d) => s + d.count, 0);

                    // Peak day
                    const dayTotals = {};
                    data.forEach(d => { dayTotals[d.day_of_week] = (dayTotals[d.day_of_week] || 0) + d.count; });
                    const peakDay = Object.entries(dayTotals).sort((a,b) => b[1]-a[1])[0];

                    // Peak hour
                    const hourTotals = {};
                    data.forEach(d => { hourTotals[d.hour] = (hourTotals[d.hour] || 0) + d.count; });
                    const peakHour = Object.entries(hourTotals).sort((a,b) => b[1]-a[1])[0];

                    document.getElementById('hm-total').textContent     = total.toLocaleString('pt-BR');
                    document.getElementById('hm-peak-day').textContent  = peakDay  ? DAYS[peakDay[0]]               : '—';
                    document.getElementById('hm-peak-hour').textContent = peakHour ? sprintf(peakHour[0]) + ':00'   : '—';

                    // Build rows
                    const rowsEl = document.getElementById('hm-rows');
                    DAYS.forEach((dayName, d) => {
                        const row = document.createElement('div');
                        row.className = 'hm-row';

                        const lbl = document.createElement('div');
                        lbl.className   = 'hm-day-label';
                        lbl.textContent = dayName.substring(0, 3);
                        row.appendChild(lbl);

                        for (let h = 0; h < 24; h++) {
                            const cell = data.find(x => x.day_of_week === d && x.hour === h);
                            const count = cell ? cell.count : 0;
                            const intensity = count / maxVal;

                            const el = document.createElement('div');
                            el.className = 'hm-cell';
                            el.style.setProperty('--intensity', intensity);
                            el.setAttribute('data-count', count);
                            el.setAttribute('data-day', dayName);
                            el.setAttribute('data-hour', sprintf(h) + ':00');

                            el.addEventListener('mouseenter', showTooltip);
                            el.addEventListener('mouseleave', hideTooltip);
                            el.addEventListener('mousemove',  moveTooltip);

                            row.appendChild(el);
                        }

                        rowsEl.appendChild(row);
                    });

                    document.getElementById('hm-grid').style.display = 'block';

                } catch (e) {
                    document.getElementById('hm-skeleton').style.display = 'none';
                    document.getElementById('hm-empty').style.display    = 'block';
                    console.error('Heatmap error:', e);
                }
            }

            function sprintf(n) { return String(n).padStart(2, '0'); }

            const tooltip = document.getElementById('hm-tooltip');

            function showTooltip(e) {
                const el    = e.currentTarget;
                const count = el.getAttribute('data-count');
                const day   = el.getAttribute('data-day');
                const hour  = el.getAttribute('data-hour');
                tooltip.innerHTML = `<strong>${day}</strong> às ${hour}<br><span>${count} registro${count != 1 ? 's' : ''}</span>`;
                tooltip.style.display = 'block';
                moveTooltip(e);
            }

            function hideTooltip()  { tooltip.style.display = 'none'; }

            function moveTooltip(e) {
                const x = e.clientX + 14;
                const y = e.clientY - 38;
                tooltip.style.left = x + 'px';
                tooltip.style.top  = y + 'px';
            }

            loadHeatmap();
        })();
    </script>
</x-app-layout>