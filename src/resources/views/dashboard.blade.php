<x-app-layout>
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
                    <button type="button" class="mgr-tab is-active" onclick="showManagerSection('students-section', this)">
                        Alunos
                        <span class="mgr-tab__count">{{ $totalStudents ?? 0 }}</span>
                    </button>
                    <button type="button" class="mgr-tab" onclick="showManagerSection('instructors-section', this)">
                        Instrutores e Treinos
                        <span class="mgr-tab__count">{{ $totalInstructors ?? 0 }}</span>
                    </button>
                    <button type="button" class="mgr-tab" onclick="showManagerSection('plans-section', this)">
                        Planos
                        <span class="mgr-tab__count">{{ $totalPlans ?? 0 }}</span>
                    </button>
                    <button type="button" class="mgr-tab" onclick="showManagerSection('frequency-section', this)">
                        Frequência
                    </button>
                    <button type="button" class="mgr-tab" onclick="showManagerSection('reports-section', this)">
                        Relatórios
                        <span class="mgr-tab__count">6</span>
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
                        <input type="text" id="studentSearch" class="mgr-search" placeholder="Buscar aluno..." oninput="searchStudents()">
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
                                    <tr class="student-row"
                                        data-name="{{ \Illuminate\Support\Str::lower($s['name']) }}"
                                        data-email="{{ \Illuminate\Support\Str::lower($s['email']) }}"
                                        data-status="{{ $s['status'] }}">
                                        <td>
                                            <div class="mgr-student-cell">
                                                <div class="mgr-student-cell__avatar">{{ mb_strtoupper(mb_substr($s['name'], 0, 2)) }}</div>
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
                                        <div class="mgr-inst-big-avatar">{{ mb_strtoupper(mb_substr($instructor->user->name, 0, 2)) }}</div>
                                        <div class="mgr-inst-label">Instrutor</div>
                                        <div class="mgr-inst-name">{{ $instructor->user->name }}</div>
                                        <span class="mgr-inst-specialty">
                                            <svg viewBox="0 0 24 24"><rect x="2" y="10" width="3" height="4" rx="1"/><rect x="19" y="10" width="3" height="4" rx="1"/><rect x="5" y="8" width="3" height="8" rx="1"/><rect x="16" y="8" width="3" height="8" rx="1"/><rect x="8" y="11" width="8" height="2" rx="1"/></svg>
                                            {{ !empty($instructor->specialty) ? $instructor->specialty : 'Personal Trainer' }}
                                        </span>
                                        <div class="mgr-inst-badge">{{ $instructor->students->count() }} aluno(s)</div>
                                    </div>
                                    <div class="mgr-inst-divider"></div>
                                    <a href="{{ route('instructors.edit', $instructor->id) }}" class="mgr-inst-edit">
                                        <svg width="12" height="12" viewBox="0 0 14 14" fill="none" style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;"><path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/></svg>
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
                                                </div>
                                            </div>
                                            @forelse($student->workouts as $workout)
                                                <div class="mgr-workouts">
                                                    <div class="mgr-workout-shell">
                                                        <div class="mgr-workout-row" onclick="toggleWorkoutMgr('workout-{{ $workout->id }}', this)" role="button" tabindex="0" onkeydown="handleWorkoutKey(event, 'workout-{{ $workout->id }}', this)">
                                                            <div class="mgr-workout-main">
                                                                <div class="mgr-workout-icon">
                                                                    <svg viewBox="0 0 24 24"><rect x="2" y="10" width="3" height="4" rx="1"/><rect x="19" y="10" width="3" height="4" rx="1"/><rect x="5" y="8" width="3" height="8" rx="1"/><rect x="16" y="8" width="3" height="8" rx="1"/><rect x="8" y="11" width="8" height="2" rx="1"/></svg>
                                                                </div>
                                                                <div class="mgr-workout-meta">
                                                                    <span class="mgr-workout-name">{{ $workout->name }}</span>
                                                                    <span class="mgr-workout-sub">{{ $workout->workoutExercises->count() }} exercício(s)</span>
                                                                </div>
                                                            </div>
                                                            <div class="mgr-workout-actions" onclick="event.stopPropagation()">
                                                                <a href="{{ route('workouts.edit', [$workout->id, 'student_id' => $student->id]) }}" class="mgr-btn-sm mgr-btn-edit-workout">
                                                                    <svg width="10" height="10" viewBox="0 0 14 14" fill="none" style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; display:inline-block; vertical-align:middle; margin-right:2px;"><path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/></svg>
                                                                    Editar
                                                                </a>
                                                                <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;" onclick="event.stopPropagation()">
                                                                    @csrf @method('DELETE')
                                                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                                    <button type="submit" class="mgr-btn-del" title="Deletar treino">
                                                                        <svg width="11" height="11" viewBox="0 0 14 16" fill="none" style="stroke:#f87171; stroke-width:1.8; stroke-linecap:round;"><path d="M1 3.5h12M4.5 3.5V2a.5.5 0 01.5-.5h3a.5.5 0 01.5.5v1.5M5.5 7v5M8.5 7v5M2.5 3.5l.9 10a.5.5 0 00.5.5h6.2a.5.5 0 00.5-.5l.9-10"/></svg>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                        <div id="workout-{{ $workout->id }}" class="mgr-workout-collapse">
                                                            @if($workout->workoutExercises->count())
                                                                <div class="mgr-exercises-wrap">
                                                                    <div class="mgr-ex-head">
                                                                        <span>Exercício</span><span>Grupo Musc.</span><span>Séries</span><span>Reps</span><span>Desc.</span>
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

                {{-- SEÇÃO PLANOS --}}
                <div id="plans-section" class="mgr-section" style="display:none;">
                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
                        <p class="section-label" style="margin-bottom:0;">PLANOS CADASTRADOS</p>
                        <a href="{{ route('plans.create') }}" class="btn-save" style="text-decoration:none; font-size:12px; padding:9px 18px; display:inline-flex; align-items:center; gap:6px;">
                            <svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="stroke:#fff; stroke-width:2.5; stroke-linecap:round;"><line x1="6" y1="1" x2="6" y2="11"/><line x1="1" y1="6" x2="11" y2="6"/></svg>
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
                                                @if($plan->description)<p class="dash-plan-card__desc">{{ $plan->description }}</p>@endif
                                            </div>
                                        </div>
                                        @if($plan->benefits)
                                            <div class="dash-plan-card__benefits">
                                                @foreach(array_slice(explode(',', $plan->benefits), 0, 3) as $benefit)
                                                    <span class="dash-plan-card__benefit-chip">{{ trim($benefit) }}</span>
                                                @endforeach
                                                @if(count(explode(',', $plan->benefits)) > 3)
                                                    <span class="dash-plan-card__benefit-chip" style="opacity:.5;">+{{ count(explode(',', $plan->benefits)) - 3 }} mais</span>
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
                                            <a href="{{ route('plans.edit', $plan->id) }}" class="mgr-btn-sm mgr-btn-edit-workout" style="text-decoration:none;">
                                                <svg width="10" height="10" viewBox="0 0 14 14" fill="none" style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; display:inline-block; vertical-align:middle; margin-right:2px;"><path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/></svg>
                                                Editar
                                            </a>
                                            @if($plan->status === 'active')
                                                <form action="{{ route('plans.destroy', $plan->id) }}" method="POST" style="margin:0;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="mgr-btn-del" onclick="return confirm('Inativar este plano?')" title="Inativar plano">
                                                        <svg width="11" height="11" viewBox="0 0 14 16" fill="none" style="stroke:#f87171; stroke-width:1.8; stroke-linecap:round;"><path d="M1 3.5h12M4.5 3.5V2a.5.5 0 01.5-.5h3a.5.5 0 01.5.5v1.5M5.5 7v5M8.5 7v5M2.5 3.5l.9 10a.5.5 0 00.5.5h6.2a.5.5 0 00.5-.5l.9-10"/></svg>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('plans.restore', $plan->id) }}" method="POST" style="margin:0;">
                                                    @csrf
                                                    <button type="submit" class="mgr-btn-sm" style="color:rgba(74,222,128,.7); border-color:rgba(34,197,94,.25);">Restaurar</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state" style="padding:3rem 1rem;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 16px; display:block; opacity:.20;"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M3 9h18M9 21V9"/></svg>
                            <p>Nenhum plano cadastrado ainda.</p>
                            <a href="{{ route('plans.create') }}" class="btn-save" style="display:inline-flex; margin-top:18px; text-decoration:none; align-items:center; gap:6px;">
                                <svg width="11" height="11" viewBox="0 0 12 12" fill="none" style="stroke:#fff; stroke-width:2.5; stroke-linecap:round;"><line x1="6" y1="1" x2="6" y2="11"/><line x1="1" y1="6" x2="11" y2="6"/></svg>
                                Criar Primeiro Plano
                            </a>
                        </div>
                    @endif
                </div>

                {{-- SEÇÃO RELATÓRIOS --}}
                <div id="reports-section" class="mgr-section" style="display:none;">
                    <div style="margin-bottom:20px;"><p class="section-label">RELATÓRIOS</p></div>
                    <div class="report-cards-grid">
                        <a href="{{ route('reports.plans.comparative') }}" class="report-card report-card--red">
                            <div class="report-card__body">
                                <div class="report-card__icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="stroke:#f87171;stroke-width:1.8;stroke-linecap:round;"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M3 9h18M9 21V9"/></svg></div>
                                <p class="report-card__title">Comparativo de Planos</p>
                                <p class="report-card__desc">Planos ativos lado a lado com preço, duração, benefícios e alunos matriculados.</p>
                            </div>
                            <div class="report-card__footer"><span class="report-card__footer-label">Abrir relatório</span><svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:var(--text-muted);stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><path d="M2.5 7h9M7.5 3l4 4-4 4"/></svg></div>
                        </a>
                        <a href="{{ route('reports.plans.cancellations') }}" class="report-card report-card--pink">
                            <div class="report-card__body">
                                <div class="report-card__icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="stroke:#f87171;stroke-width:1.8;stroke-linecap:round;"><circle cx="12" cy="12" r="9"/><path d="M15 9l-6 6M9 9l6 6"/></svg></div>
                                <p class="report-card__title">Cancelamentos</p>
                                <p class="report-card__desc">Histórico de cancelamentos com data e filtro por período.</p>
                            </div>
                            <div class="report-card__footer"><span class="report-card__footer-label">Abrir relatório</span><svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:var(--text-muted);stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><path d="M2.5 7h9M7.5 3l4 4-4 4"/></svg></div>
                        </a>
                        <a href="{{ route('reports.plans.loyalty') }}" class="report-card report-card--green">
                            <div class="report-card__body">
                                <div class="report-card__icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="stroke:#4ade80;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></div>
                                <p class="report-card__title">Fidelidade</p>
                                <p class="report-card__desc">Ranking dos alunos mais fiéis por tempo de permanência ativo.</p>
                            </div>
                            <div class="report-card__footer"><span class="report-card__footer-label">Abrir relatório</span><svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:var(--text-muted);stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><path d="M2.5 7h9M7.5 3l4 4-4 4"/></svg></div>
                        </a>
                        <a href="{{ route('reports.users.delinquency') }}" class="report-card report-card--pink">
                            <div class="report-card__body">
                                <div class="report-card__icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="stroke:#f87171;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div>
                                <p class="report-card__title">Inadimplência & Churn</p>
                                <p class="report-card__desc">Alunos inadimplentes, cancelados e inativos há mais de 30 dias.</p>
                            </div>
                            <div class="report-card__footer"><span class="report-card__footer-label">Abrir relatório</span><svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:var(--text-muted);stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><path d="M2.5 7h9M7.5 3l4 4-4 4"/></svg></div>
                        </a>
                        <a href="{{ route('reports.plans.occupation') }}" class="report-card report-card--red">
                            <div class="report-card__body">
                                <div class="report-card__icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="stroke:#f87171;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                                <p class="report-card__title">Ocupação por Modalidade</p>
                                <p class="report-card__desc">Distribuição de alunos ativos por tipo de plano com percentuais.</p>
                            </div>
                            <div class="report-card__footer"><span class="report-card__footer-label">Abrir relatório</span><svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:var(--text-muted);stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><path d="M2.5 7h9M7.5 3l4 4-4 4"/></svg></div>
                        </a>
                        <a href="{{ route('reports.shop.products') }}" class="report-card report-card--green">
                            <div class="report-card__body">
                                <div class="report-card__icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="stroke:#4ade80;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg></div>
                                <p class="report-card__title">Vendas da Lojinha</p>
                                <p class="report-card__desc">Produtos mais vendidos, receita total e lucro por item.</p>
                            </div>
                            <div class="report-card__footer"><span class="report-card__footer-label">Abrir relatório</span><svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:var(--text-muted);stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><path d="M2.5 7h9M7.5 3l4 4-4 4"/></svg></div>
                        </a>
                        <a href="{{ route('evaluations.manager') }}" class="report-card report-card--blue">
                            <div class="report-card__body">
                                <div class="report-card__icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="stroke:#93c5fd;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;"><path d="M3 3v18h18"/><path d="M7 16l4-4 4 4 4-6"/></svg></div>
                                <p class="report-card__title">Evolução Física</p>
                                <p class="report-card__desc">Histórico de avaliações físicas dos alunos com evolução de peso, IMC e gordura corporal.</p>
                            </div>
                            <div class="report-card__footer"><span class="report-card__footer-label">Abrir relatório</span><svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:var(--text-muted);stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><path d="M2.5 7h9M7.5 3l4 4-4 4"/></svg></div>
                        </a>
                    </div>
                </div>

                {{-- SEÇÃO FREQUÊNCIA --}}
                <div id="frequency-section" class="mgr-section" style="display:none;">
                    <div style="margin-bottom:20px;"><p class="section-label">FREQUÊNCIA</p></div>
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
                        <div id="hm-skeleton" class="hm-skeleton-wrap">
                            <div class="hm-skeleton-grid">
                                @for($i = 0; $i < 7; $i++)
                                    <div class="hm-skeleton-row">
                                        <div class="sk hm-skeleton-label"></div>
                                        @for($j = 0; $j < 24; $j++)<div class="sk hm-skeleton-cell"></div>@endfor
                                    </div>
                                @endfor
                            </div>
                        </div>
                        <div id="hm-grid" class="hm-grid" style="display:none;">
                            <div class="hm-hour-row">
                                <div class="hm-day-label"></div>
                                @for($h = 0; $h < 24; $h++)<div class="hm-hour-label">{{ sprintf('%02d', $h) }}</div>@endfor
                            </div>
                            <div id="hm-rows"></div>
                        </div>
                        <div id="hm-tooltip" class="hm-tooltip" style="display:none;"></div>
                        <div id="hm-empty" class="hm-empty" style="display:none;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 14px; display:block; opacity:.20;"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M3 9h18M9 21V9"/></svg>
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
                            <span class="dash-hero__pulse"><span class="dash-hero__pulse-dot"></span>INSTRUTOR</span>
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
                <div class="instructor-search">
                    <div class="instructor-search__icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    </div>
                    <input type="text" id="instructor-student-search" placeholder="Buscar aluno matriculado..." oninput="filterInstructorStudents()">
                </div>
                <div class="students-grid">
                    @forelse($instructor->students as $student)
                        <div class="student-card instructor-student-card {{ $student->is_defaulter ? 'student-card--bad' : 'student-card--ok' }}"
                             data-name="{{ mb_strtolower($student->user->name) }}"
                             data-email="{{ mb_strtolower($student->user->email) }}">
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
                                                <button type="button" class="btn-workout-action" style="font-size:11px; padding:4px 12px;" onclick="toggleWorkout('workout-inst-{{ $workout->id }}')" id="btn-workout-inst-{{ $workout->id }}">Ver exercícios ▾</button>
                                            </div>
                                        </div>
                                        <div id="workout-inst-{{ $workout->id }}" style="display:none; margin-top:10px;">
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
                                                <p style="font-size:13px; color:var(--text-muted); opacity:.6;">Nenhum exercício neste treino.</p>
                                            @endif
                                        </div>
                                        <div style="display:flex; gap:8px; margin-top:12px; flex-wrap:wrap;">
                                            <a href="{{ route('workouts.edit', [$workout->id, 'student_id' => $student->id]) }}" class="btn-workout-action">
                                                <svg width="11" height="11" viewBox="0 0 14 14" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/></svg>
                                                Editar
                                            </a>
                                            <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">
                                                @csrf @method('DELETE')
                                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                <button type="submit" class="btn-workout-action" style="border-color:rgba(214,21,50,.6); color:#f87171;">🗑 Deletar</button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div class="workout-empty">Nenhum treino cadastrado.</div>
                                @endforelse
                            </div>
                            <div style="padding:14px 16px; border-top:1px solid rgba(255,255,255,.06); display:flex; justify-content:flex-end;">
                                <a href="{{ route('workouts.create', ['student_id' => $student->id]) }}" class="btn-save" style="text-decoration:none; font-size:12px; padding:7px 16px;">+ Criar treino</a>
                            </div>
                        </div>
                    @empty
                        <div class="inst-empty" style="grid-column:1/-1;">Nenhum aluno vinculado a você.</div>
                    @endforelse
                    <div id="instructor-students-empty" class="inst-empty" style="grid-column:1/-1; display:none;">Nenhum aluno encontrado.</div>
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
                            <a href="{{ route('enrollment.index') }}" class="btn-save" style="text-decoration:none; display:inline-flex; align-items:center; gap:7px;">Matricular-se</a>
                        </div>
                    </div>
                </div>
                <div class="empty-state" style="padding:4rem 1rem;">
                    <svg width="56" height="56" viewBox="0 0 24 24" fill="none" style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 18px; display:block; opacity:.20;"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <p>Você ainda não possui uma matrícula ativa.</p>
                    <p style="font-size:13px; margin-top:6px; opacity:.45;">Escolha um plano para liberar o acesso completo.</p>
                    <a href="{{ route('enrollment.index') }}" class="btn-save" style="text-decoration:none; display:inline-block; margin-top:20px;">Ver Planos</a>
                </div>

            {{-- ══════════════════════════════════════════════════════════════
                 VISÃO DO ALUNO COM MATRÍCULA 
            ══════════════════════════════════════════════════════════════ --}}
            @else
           @php
    $dashUser      = Auth::user();
    $st            = $dashUser->student?->status ?? 'active';
    $firstName     = explode(' ', $dashUser->name)[0];

    $studentPlan = $dashUser->student?->plan;
    $daysRemaining = null;

    if ($dashUser->student?->plan_end) {
        $daysRemaining = now()->diffInDays($dashUser->student->plan_end, false);
        $daysRemaining = max(0, $daysRemaining);
    }

    $dashGroup     = $dashUser->planGroups()->with('plan')->first();
    $dashBonus     = $dashUser->gamificationBonus();
    $dashBase      = $dashGroup ? $dashGroup->baseDiscount() : 0.0;
    $dashTotal     = min($dashBase + $dashBonus, 25.0);
    $dashThreshold = 100;
    $dashCycle     = $dashUser->points % $dashThreshold;
    $dashPct       = $dashUser->points > 0
        ? ($dashCycle === 0 && $dashUser->points >= $dashThreshold
            ? 100
            : ($dashCycle / $dashThreshold) * 100)
        : 0;
@endphp

               <div class="dash-hero" style="padding:42px 32px; margin-bottom:18px;">
    <div class="dash-hero__ring"></div>

    <div class="dash-hero__inner">
        <div>
            <div class="dash-hero__eyebrow">
                Bem-vindo de volta
            </div>

            <h2 class="dash-hero__title">
                Olá, {{ $firstName }}
            </h2>

            <p class="dash-hero__sub">
                Pronto para mais um dia?
            </p>
        </div>

        <div class="dash-hero__right">
            @if($st === 'active')
                <span class="dash-hero__pulse">
                    <span class="dash-hero__pulse-dot"></span>
                    FITPULSE ATIVO
                </span>
            @endif
        </div>
    </div>
</div>

                {{-- ── BANNER STATUS (só se não ativo) ── --}}
                @php $studentAccess = Auth::user()->student; @endphp
                @if($studentAccess && $studentAccess->status !== 'active')
                    <div style="display:flex; align-items:center; gap:14px; padding:14px 20px; border-radius:14px; margin-bottom:16px;
                        {{ $studentAccess->status === 'blocked'
                            ? 'background:rgba(214,21,50,0.08);border:1px solid rgba(214,21,50,0.22);'
                            : 'background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.22);' }}">
                        @if($studentAccess->status === 'blocked')
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <div style="flex:1;min-width:0;">
                                <span style="font-size:12px;font-weight:800;color:#f87171;text-transform:uppercase;letter-spacing:.08em;">Acesso Bloqueado</span>
                                <p style="font-size:12px;color:var(--text-muted);margin:2px 0 0;">Entre em contato com a administração para mais informações.</p>
                            </div>
                        @else
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <div style="flex:1;min-width:0;">
                                <span style="font-size:12px;font-weight:800;color:#fbbf24;text-transform:uppercase;letter-spacing:.08em;">Pagamento Pendente</span>
                                <p style="font-size:12px;color:var(--text-muted);margin:2px 0 0;">Regularize sua situação para manter o acesso ativo.</p>
                            </div>
                            <a href="{{ route('billing.index') }}" style="font-size:11px;font-weight:700;color:#fbbf24;text-decoration:none;border:1px solid rgba(251,191,36,.30);padding:5px 12px;border-radius:99px;white-space:nowrap;">Regularizar</a>
                        @endif
                    </div>
                @endif

                {{-- ── GRID 2 COLUNAS: PRESENÇA + PONTOS ── --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:16px;">

                    {{-- Card Presença --}}
                    <div class="freq-card" style="flex-direction:column; align-items:stretch; gap:0; padding:0; overflow:hidden;">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; padding:18px 20px 14px;">
                            <div style="display:flex; align-items:center; gap:12px; flex:1;">
                                <div class="freq-card__icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="freq-card__title">Registrar Presença</p>
                                    <p class="freq-card__sub" id="freq-sub">
                                        @if(isset($checkedInToday) && $checkedInToday)
                                            Você já registrou hoje
                                        @else
                                            Marque sua presença
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @if(isset($checkedInToday) && $checkedInToday)
                                <button class="freq-btn freq-btn--done" disabled id="freq-btn" style="flex-shrink:0;">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:currentColor; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round;"><polyline points="2,7 6,11 12,3"/></svg>
                                    Presente
                                </button>
                            @else
                                <button class="freq-btn freq-btn--active" id="freq-btn" onclick="registerFrequency()" style="flex-shrink:0;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="stroke:currentColor; stroke-width:2.2; stroke-linecap:round; stroke-linejoin:round;"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/></svg>
                                    Registrar
                                </button>
                            @endif
                        </div>

                        <div style="height:1px; background:rgba(128,128,128,0.15); margin:0 20px;"></div>

                        <div style="padding:14px 20px 18px;">
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                                <p style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--text-muted); margin:0;">Presenças esta semana</p>
                                <span style="font-size:11px; color:var(--text-muted);">
                                    {{ isset($lastFrequency) && $lastFrequency ? 'Última: '.$lastFrequency->created_at->format('d/m') : '' }}
                                </span>
                            </div>
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
                            <div style="display:flex; gap:4px;">
                                @foreach($days as $day)
                                    @php
                                        $present = in_array($day['num'], $frequencyThisWeek ?? []);
                                        $isToday = $day['num'] === $todayNum;
                                    @endphp
                                    <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:4px;">
                                        <span class="freq-day__label">{{ $day['label'] }}</span>
                                        <div {{ $isToday && !$present ? 'data-today-dot' : '' }}
                                             class="freq-day__dot {{ $present && $isToday ? 'freq-day__dot--today' : ($present ? 'freq-day__dot--present' : '') }}">
                                            {{ $present ? '✓' : '·' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Card Pontos e Recompensas --}}
                    <div id="gami-card" style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:0; overflow:hidden;">
                        <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 20px 14px; border-bottom:1px solid rgba(128,128,128,0.12);">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:36px; height:36px; border-radius:10px; flex-shrink:0; display:flex; align-items:center; justify-content:center; background:rgba(214,21,50,0.12); border:1px solid rgba(214,21,50,0.22);">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" style="stroke:#f87171; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                </div>
                                <div>
                                    <p style="font-size:13px; font-weight:700; color:var(--text-white); margin:0 0 1px;">Pontos e Recompensas</p>
                                    <p style="font-size:11px; color:var(--text-muted); margin:0;">Continue treinando para ganhar bônus</p>
                                </div>
                            </div>
                            <a href="{{ route('gamification.index') }}" style="font-size:11px; font-weight:700; color:var(--text-muted); text-decoration:none; padding:5px 12px; border-radius:99px; border:1px solid rgba(255,255,255,0.09); white-space:nowrap;">Ver detalhes →</a>
                        </div>

                        <div style="padding:16px 20px;">
                            {{-- Pontos + barra --}}
                            <div style="display:flex; align-items:center; gap:16px; margin-bottom:12px; flex-wrap:wrap;">
                                <div style="flex-shrink:0;">
                                    <div style="font-family:'Bebas Neue',sans-serif; font-size:40px; letter-spacing:2px; color:var(--text-white); line-height:1;">
                                        {{ $dashUser->points }}
                                    </div>
                                    <div style="font-size:10px; text-transform:uppercase; letter-spacing:.08em; color:var(--text-muted);">pontos</div>
                                </div>
                                <div style="flex:1; min-width:120px;">
                                    <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--text-muted); margin-bottom:5px;">
                                        <span>Progresso para bônus</span>
                                        <span id="dash-pct">{{ round($dashPct) }}%</span>
                                    </div>
                                    <div style="height:6px; background:rgba(255,255,255,0.08); border-radius:99px; overflow:hidden;">
                                        <div id="dash-progress" style="height:100%; border-radius:99px; background:linear-gradient(90deg,#d61532,#ff5068); width:{{ $dashPct }}%; transition:width .6s ease;"></div>
                                    </div>
                                    <div style="font-size:11px; color:var(--text-muted); margin-top:5px;">
                                        @if($dashUser->hasGamificationBonus())
                                            <span style="color:#4ade80; font-weight:700;">★ Bônus ativo! +{{ $dashBonus }}% de desconto</span>
                                        @else
                                            Faltam <strong id="dash-to-next" style="color:var(--text-white);">{{ $dashUser->pointsToNextReward() }}</strong> pontos para o bônus
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Grupo --}}
                            @if($dashGroup)
                                <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 14px; border-radius:12px; background:rgba(59,130,246,0.07); border:1px solid rgba(59,130,246,0.18); flex-wrap:wrap; gap:8px;">
                                    <div>
                                        <span style="font-size:12px; font-weight:700; color:#93c5fd;">{{ $dashGroup->name }}</span>
                                        <span style="font-size:11px; color:var(--text-muted);"> · {{ $dashGroup->memberCount() }}/5 membros</span>
                                    </div>
                                    <span style="font-size:13px; font-weight:800; color:#4ade80;">{{ $dashTotal }}% desc.</span>
                                </div>
                            @else
                                <div style="display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:12px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07);">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" style="stroke:var(--text-muted); stroke-width:2; stroke-linecap:round; flex-shrink:0;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    <span style="font-size:12px; color:var(--text-muted);">Sem plano conjunto — <a href="{{ route('gamification.index') }}" style="color:#93c5fd; text-decoration:none; font-weight:700;">criar ou entrar em um grupo</a> para desconto extra</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Toast --}}
                <div class="freq-toast" id="freq-toast" style="display:none;"></div>

                {{-- AÇÕES RÁPIDAS --}}
                <div class="student-quick-actions student-quick-actions--three">
                    <a href="{{ route('workouts.index') }}" class="student-action-card student-action-card--workout">
                        <div class="student-action-card__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;"><rect x="2" y="9" width="4" height="6" rx="1"/><rect x="18" y="9" width="4" height="6" rx="1"/><rect x="7" y="11" width="10" height="2" rx="1"/></svg>
                        </div>
                        <div class="student-action-card__content">
                            <p class="student-action-card__label">Meus Treinos</p>
                            <p class="student-action-card__hint">Ver e gerenciar treinos</p>
                        </div>
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; opacity:.45; flex-shrink:0;"><path d="M2.5 7h9M7.5 3l4 4-4 4"/></svg>
                    </a>

                    <a href="{{ route('plans.renewals') }}" class="student-action-card student-action-card--blue">
                        <div class="student-action-card__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;"><path d="M1 4v6h6"/><path d="M23 20v-6h-6"/><path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10M23 14l-4.64 4.36A9 9 0 0 1 3.51 15"/></svg>
                        </div>
                        <div class="student-action-card__content">
                            <p class="student-action-card__label">Renovar Plano</p>
                            <p class="student-action-card__hint">Estenda sua assinatura</p>
                        </div>
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; opacity:.45; flex-shrink:0;"><path d="M2.5 7h9M7.5 3l4 4-4 4"/></svg>
                    </a>

                    <a href="{{ route('billing.index') }}" class="student-action-card student-action-card--green">
                        <div class="student-action-card__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                        </div>
                        <div class="student-action-card__content">
                            <p class="student-action-card__label">Pagar Mensalidade</p>
                            <p class="student-action-card__hint">Ver e processar pagamentos</p>
                        </div>
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; opacity:.45; flex-shrink:0;"><path d="M2.5 7h9M7.5 3l4 4-4 4"/></svg>
                    </a>

                    <a href="{{ route('evaluations.page') }}" class="student-action-card student-action-card--workout">
                        <div class="student-action-card__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;"><path d="M3 3v18h18"/><path d="M7 16l4-4 4 4 4-6"/></svg>
                        </div>
                        <div class="student-action-card__content">
                            <p class="student-action-card__label">Minha Evolução</p>
                            <p class="student-action-card__hint">Ver histórico físico</p>
                        </div>
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; opacity:.45; flex-shrink:0;"><path d="M2.5 7h9M7.5 3l4 4-4 4"/></svg>
                    </a>

                    <button type="button" onclick="openEquipmentModal()" class="student-action-card student-action-card--blue" style="width:100%; text-align:left; cursor:pointer; font-family:inherit; border:1px solid;">
                        <div class="student-action-card__icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;"><rect x="2" y="10" width="3" height="4" rx="1"/><rect x="19" y="10" width="3" height="4" rx="1"/><rect x="5" y="8" width="3" height="8" rx="1"/><rect x="16" y="8" width="3" height="8" rx="1"/><rect x="8" y="11" width="8" height="2" rx="1"/></svg>
                        </div>
                        <div class="student-action-card__content">
                            <p class="student-action-card__label">Equipamentos</p>
                            <p class="student-action-card__hint">Ver status e disponibilidade</p>
                        </div>
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; opacity:.45; flex-shrink:0;"><path d="M2.5 7h9M7.5 3l4 4-4 4"/></svg>
                    </button>

                    <form id="cancel-enrollment-form" action="{{ route('enrollment.cancel', $activeEnrollment->id) }}" method="POST" style="display:contents;">
                        @csrf
                        <button type="button" onclick="confirmCancelPlan()" class="student-action-card student-action-card--green" style="width:100%; text-align:left; cursor:pointer; font-family:inherit;">
                            <div class="student-action-card__icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                            </div>
                            <div class="student-action-card__content">
                                <p class="student-action-card__label">Cancelar Plano</p>
                                <p class="student-action-card__hint">Encerrar matrícula atual</p>
                            </div>
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; opacity:.45; flex-shrink:0;"><path d="M2.5 7h9M7.5 3l4 4-4 4"/></svg>
                        </button>
                    </form>
                </div>

            @endif
        </div>
    </div>

{{-- MODAL CANCELAMENTO --}}
<div id="cancel-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#161616; border:1px solid rgba(255,255,255,0.10); border-radius:20px; width:100%; max-width:400px; box-shadow:0 24px 60px rgba(0,0,0,0.50); animation:shopModalIn .22s ease; overflow:hidden;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 22px 16px; border-bottom:1px solid rgba(255,255,255,0.07);">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:34px; height:34px; border-radius:10px; background:rgba(214,21,50,0.12); border:1px solid rgba(214,21,50,0.25); display:flex; align-items:center; justify-content:center;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="stroke:#f87171; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                </div>
                <p style="font-size:14px; font-weight:800; color:#f5f5f5; margin:0;">Cancelar Plano?</p>
            </div>
            <button type="button" class="shop-modal__close" onclick="closeCancelModal()">✕</button>
        </div>
        <div style="padding:20px 22px 22px;">
            <p style="font-size:14px; color:rgba(255,255,255,0.75); line-height:1.6; margin:0 0 20px;">
                Esta ação encerrará sua matrícula e bloqueará seu acesso. Esta ação <strong style="color:#f87171;">não pode ser desfeita</strong>.
            </p>
            <div style="display:flex; gap:10px;">
                <button type="button" onclick="closeCancelModal()" class="shop-modal__btn-cancel" style="flex:1; padding:11px;">Voltar</button>
                <button type="button" onclick="document.getElementById('cancel-enrollment-form').submit()" style="flex:2; padding:11px; border-radius:12px; background:#d61532; border:none; color:#fff; font-size:13px; font-weight:700; cursor:pointer; font-family:'Montserrat',sans-serif;">Sim, cancelar plano</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EQUIPAMENTOS --}}
<div id="equipment-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#161616; border:1px solid rgba(255,255,255,0.10); border-radius:20px; width:100%; max-width:560px; box-shadow:0 24px 60px rgba(0,0,0,0.50); animation:shopModalIn .22s ease; overflow:hidden;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 22px 16px; border-bottom:1px solid rgba(255,255,255,0.07);">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:34px; height:34px; border-radius:10px; background:rgba(59,130,246,0.12); border:1px solid rgba(59,130,246,0.25); display:flex; align-items:center; justify-content:center;">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" style="stroke:#93c5fd; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;"><rect x="2" y="10" width="3" height="4" rx="1"/><rect x="19" y="10" width="3" height="4" rx="1"/><rect x="5" y="8" width="3" height="8" rx="1"/><rect x="16" y="8" width="3" height="8" rx="1"/><rect x="8" y="11" width="8" height="2" rx="1"/></svg>
                </div>
                <p style="font-size:14px; font-weight:800; color:#f5f5f5; margin:0;">Equipamentos</p>
            </div>
            <button type="button" class="shop-modal__close" onclick="closeEquipmentModal()" aria-label="Fechar">&times;</button>
        </div>
        <div style="padding:18px 22px 22px;">
            <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px;">
                <button type="button" class="shop-filter-btn is-active" onclick="filterEqModal('all', this)">Todos</button>
                <button type="button" class="shop-filter-btn" onclick="filterEqModal('ativo', this)">Disponíveis</button>
                <button type="button" class="shop-filter-btn" onclick="filterEqModal('manutencao', this)">Em manutenção</button>
            </div>
            <div id="eq-modal-loading" style="display:none; padding:18px 0; color:var(--text-muted); font-size:13px;">Carregando equipamentos...</div>
            <div id="eq-modal-body" style="display:none; flex-direction:column; gap:10px; max-height:340px; overflow:auto;"></div>
            <div id="eq-modal-empty" style="display:none; padding:22px 0; color:var(--text-muted); font-size:13px; text-align:center;">Nenhum equipamento encontrado.</div>
        </div>
    </div>
</div>

{{-- AVISO DE MANUTENCAO --}}
<div id="maint-notify-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#161616; border:1px solid rgba(255,255,255,0.10); border-radius:20px; width:100%; max-width:430px; box-shadow:0 24px 60px rgba(0,0,0,0.50); animation:shopModalIn .22s ease; overflow:hidden;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 22px 16px; border-bottom:1px solid rgba(255,255,255,0.07);">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:34px; height:34px; border-radius:10px; background:rgba(251,191,36,0.12); border:1px solid rgba(251,191,36,0.25); display:flex; align-items:center; justify-content:center;">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" style="stroke:#fbbf24; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <p style="font-size:14px; font-weight:800; color:#f5f5f5; margin:0;">Aviso de manutenção</p>
            </div>
            <button type="button" class="shop-modal__close" onclick="closeNotifyModal()" aria-label="Fechar">&times;</button>
        </div>
        <div style="padding:20px 22px 22px;">
            <p id="maint-notify-msg" style="font-size:14px; color:rgba(255,255,255,0.75); line-height:1.6; margin:0 0 14px;"></p>
            <div id="maint-notify-list" style="display:flex; flex-direction:column; gap:8px; margin-bottom:18px;"></div>
            <button type="button" onclick="closeNotifyModal()" class="shop-modal__btn-confirm" style="width:100%; justify-content:center;">Entendi</button>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    /* KPI grid: 2x2 no mobile */
    .dash-hero div[style*="grid-template-columns:repeat(4,1fr)"] {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    /* Cards lado a lado: empilha no mobile */
    div[style*="grid-template-columns:1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
    const DASH_USER_ROLE = @json(Auth::user()->role());
    const DASH_USER_ID = @json(Auth::id());
    const MAINT_NOTIFY_ALLOWED_ROLES = ['student', 'instructor', 'manager'];
    const MAINT_NOTIFY_STORAGE_KEY = `fitpulse:maintenance-notify-seen:${DASH_USER_ID}`;

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

    function filterInstructorStudents() {
        const input = document.getElementById('instructor-student-search');
        const query = input ? input.value.toLowerCase().trim() : '';
        let visibleCount = 0;

        document.querySelectorAll('.instructor-student-card').forEach(card => {
            const matches = !query
                || (card.dataset.name || '').includes(query)
                || (card.dataset.email || '').includes(query);
            card.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        const empty = document.getElementById('instructor-students-empty');
        if (empty) empty.style.display = visibleCount === 0 ? 'block' : 'none';
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
                btn.innerHTML = `<svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:currentColor;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round;"><polyline points="2,7 6,11 12,3"/></svg> Presente`;

                if (sub) sub.textContent = 'Você já registrou hoje';

                // Atualiza KPI "este mês" no hero
                const heroMonth = document.getElementById('freq-count');
                if (heroMonth) heroMonth.textContent = parseInt(heroMonth.textContent || '0') + 1;

                // Atualiza dot da semana
                document.querySelectorAll('[data-today-dot]').forEach(d => {
                    d.style.background  = 'rgba(214,21,50,0.15)';
                    d.style.border      = '1px solid rgba(214,21,50,0.35)';
                    d.style.color       = '#f87171';
                    d.textContent       = '✓';
                });

                showFreqToast('Presença registrada com sucesso!', 'success');

                if (data.data && data.data.points_earned > 0) {
                    updateDashGamification(data.data);
                }

                setTimeout(() => { location.reload(); }, 1800);
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

    function updateDashGamification(d) {
        const ptsEl = document.getElementById('dash-points');
        if (ptsEl) {
            ptsEl.childNodes[0].textContent = d.total_points;
            ptsEl.style.color = '#4ade80';
            setTimeout(() => { ptsEl.style.color = 'var(--text-white)'; }, 1200);
        }

        const threshold = 100;
        const cycle = d.total_points % threshold;
        const pct   = d.total_points > 0
            ? (cycle === 0 && d.total_points >= threshold ? 100 : (cycle / threshold) * 100)
            : 0;

        const bar   = document.getElementById('dash-progress');
        const pctEl = document.getElementById('dash-pct');
        if (bar)   bar.style.width = pct + '%';
        if (pctEl) pctEl.textContent = Math.round(pct) + '%';

        const nextEl = document.getElementById('dash-to-next');
        if (nextEl) nextEl.textContent = d.points_to_next;

        showFreqToast(`+${d.points_earned} pontos! Total: ${d.total_points} pts ⭐`, 'success');
    }

    function showFreqToast(msg, type) {
        const toast = document.getElementById('freq-toast');
        if (!toast) return;
        toast.textContent   = msg;
        toast.style.display = 'flex';
        toast.className     = 'freq-toast' + (type === 'error' ? ' freq-toast--error' : '');
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => { toast.style.display = 'none'; toast.style.opacity = '1'; }, 300);
        }, 3500);
    }

    // ── HEATMAP ──────────────────────────────────────────────────────────
    (function () {
        const DAYS  = ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'];
        const endpoint = "{{ route('reports.frequency.heatmap') }}";

        async function loadHeatmap() {
            try {
                const res  = await fetch(endpoint, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const json = await res.json();
                const data = json.data ?? [];

                document.getElementById('hm-skeleton').style.display = 'none';

                if (!data.length || data.every(d => d.count === 0)) {
                    document.getElementById('hm-empty').style.display = 'block';
                    return;
                }

                const maxVal = Math.max(...data.map(d => d.count), 1);
                const total  = data.reduce((s, d) => s + d.count, 0);

                const dayTotals = {};
                data.forEach(d => { dayTotals[d.day_of_week] = (dayTotals[d.day_of_week] || 0) + d.count; });
                const peakDay = Object.entries(dayTotals).sort((a,b) => b[1]-a[1])[0];

                const hourTotals = {};
                data.forEach(d => { hourTotals[d.hour] = (hourTotals[d.hour] || 0) + d.count; });
                const peakHour = Object.entries(hourTotals).sort((a,b) => b[1]-a[1])[0];

                document.getElementById('hm-total').textContent     = total.toLocaleString('pt-BR');
                document.getElementById('hm-peak-day').textContent  = peakDay  ? DAYS[peakDay[0]] : '—';
                document.getElementById('hm-peak-hour').textContent = peakHour ? sprintf(peakHour[0]) + ':00' : '—';

                const rowsEl = document.getElementById('hm-rows');
                DAYS.forEach((dayName, d) => {
                    const row = document.createElement('div');
                    row.className = 'hm-row';
                    const lbl = document.createElement('div');
                    lbl.className = 'hm-day-label';
                    lbl.textContent = dayName.substring(0, 3);
                    row.appendChild(lbl);

                    for (let h = 0; h < 24; h++) {
                        const cell = data.find(x => x.day_of_week === d && x.hour === h);
                        const count = cell ? cell.count : 0;
                        const el = document.createElement('div');
                        el.className = 'hm-cell';
                        el.style.setProperty('--intensity', count / maxVal);
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
            }
        }

        function sprintf(n) { return String(n).padStart(2, '0'); }
        const tooltip = document.getElementById('hm-tooltip');
        function showTooltip(e) {
            const el = e.currentTarget;
            tooltip.innerHTML = `<strong>${el.getAttribute('data-day')}</strong> às ${el.getAttribute('data-hour')}<br><span>${el.getAttribute('data-count')} registro(s)</span>`;
            tooltip.style.display = 'block';
            moveTooltip(e);
        }
        function hideTooltip()  { tooltip.style.display = 'none'; }
        function moveTooltip(e) { tooltip.style.left = (e.clientX + 14) + 'px'; tooltip.style.top = (e.clientY - 38) + 'px'; }

        loadHeatmap();
    })();

    // ── LOJINHA ──────────────────────────────────────────────────────────
    (function () {
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        const ENDPOINT_PRODUCTS = "{{ route('products.index') }}";
        const ENDPOINT_SALE     = "{{ route('sales.store') }}";

        let allProducts = [], currentFilter = 'all', selectedProduct = null, currentQty = 1;

        async function loadProducts() {
            try {
                const res  = await fetch(ENDPOINT_PRODUCTS, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const json = await res.json();
                allProducts = json.data ?? [];
                document.getElementById('shop-skeleton').style.display = 'none';
                if (!allProducts.length) { document.getElementById('shop-empty').style.display = 'block'; return; }
                renderProducts(allProducts);
                document.getElementById('shop-grid').style.display = 'grid';
            } catch (e) {
                document.getElementById('shop-skeleton').style.display = 'none';
                document.getElementById('shop-empty').style.display    = 'block';
            }
        }

        function renderProducts(products) {
            const grid = document.getElementById('shop-grid');
            grid.innerHTML = '';
            const filtered = currentFilter === 'all' ? products : products.filter(p => p.category === currentFilter);
            if (!filtered.length) { grid.style.display = 'none'; document.getElementById('shop-empty').style.display = 'block'; return; }
            document.getElementById('shop-empty').style.display = 'none';
            grid.style.display = 'grid';
            filtered.forEach(p => {
                const price = parseFloat(p.price).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                const catLabel = p.category === 'suplemento' ? 'Suplemento' : 'Acessório';
                const catClass  = p.category === 'suplemento' ? 'shop-badge--sup' : 'shop-badge--ace';
                const card = document.createElement('div');
                card.className = 'shop-card';
                card.dataset.category = p.category;
                card.innerHTML = `<div class="shop-card__img-wrap">${p.image ? `<img src="${p.image}" alt="${p.name}" class="shop-card__img" loading="lazy">` : `<div class="shop-card__img-placeholder"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" style="stroke:var(--text-muted); stroke-width:1.5; opacity:.30;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg></div>`}<span class="shop-badge ${catClass}">${catLabel}</span></div><div class="shop-card__body"><p class="shop-card__name">${p.name}</p>${p.description ? `<p class="shop-card__desc">${p.description}</p>` : ''}<div class="shop-card__footer"><span class="shop-card__price">${price}</span><button type="button" class="shop-card__btn" onclick="openShopModal(${JSON.stringify(p).replace(/"/g, '&quot;')})">Comprar</button></div></div>`;
                grid.appendChild(card);
            });
        }

        window.shopFilter = function (type, btn) {
            document.querySelectorAll('.shop-filter-btn').forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            currentFilter = type;
            renderProducts(allProducts);
        };

        window.openShopModal = function (product) {
            selectedProduct = product; currentQty = 1;
            const img = document.getElementById('shop-modal-img');
            const placeholder = document.getElementById('shop-modal-img-placeholder');
            if (product.image) { img.src = product.image; img.style.display = 'block'; placeholder.style.display = 'none'; }
            else { img.style.display = 'none'; placeholder.style.display = 'flex'; }
            document.getElementById('shop-modal-name').textContent = product.name;
            document.getElementById('shop-modal-cat').textContent  = product.category === 'suplemento' ? 'Suplemento' : 'Acessório';
            document.getElementById('shop-modal-qty').textContent  = '1';
            updateModalTotal();
            document.getElementById('shop-modal-overlay').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        };

        window.closeShopModal = function () { document.getElementById('shop-modal-overlay').style.display = 'none'; document.body.style.overflow = ''; selectedProduct = null; };

        window.changeQty = function (delta) { currentQty = Math.max(1, currentQty + delta); document.getElementById('shop-modal-qty').textContent = currentQty; updateModalTotal(); };

        function updateModalTotal() {
            if (!selectedProduct) return;
            const total = (parseFloat(selectedProduct.price) * currentQty).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            document.getElementById('shop-modal-price').textContent = parseFloat(selectedProduct.price).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + ' / un.';
            document.getElementById('shop-modal-total').textContent = total;
        }

        window.confirmPurchase = async function () {
            if (!selectedProduct) return;
            const btn = document.getElementById('shop-modal-confirm-btn');
            btn.disabled = true; btn.textContent = 'Processando...';
            try {
                const res = await fetch(ENDPOINT_SALE, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }, body: JSON.stringify({ product_id: selectedProduct.id, quantity: currentQty }) });
                const data = await res.json();
                if (res.ok) { closeShopModal(); showShopToast('Compra realizada com sucesso! 🎉', 'success'); }
                else { showShopToast(data.message || 'Erro ao processar compra.', 'error'); }
            } catch (e) { showShopToast('Erro de conexão. Tente novamente.', 'error'); }
            finally { btn.disabled = false; btn.textContent = 'Confirmar compra'; }
        };

        function showShopToast(msg, type) {
            const toast = document.getElementById('shop-toast');
            toast.textContent = msg; toast.style.display = 'flex';
            toast.className = 'shop-toast' + (type === 'error' ? ' shop-toast--error' : '');
            setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(-6px)'; setTimeout(() => { toast.style.display = 'none'; toast.style.opacity = '1'; toast.style.transform = 'none'; }, 300); }, 3500);
        }

        loadProducts();
    })();

    // ── EQUIPAMENTOS ─────────────────────────────────────────────────────
    const EP_EQ_STUDENT = "{{ route('equipment.index') }}";
    let eqData = [], eqModalFilter = 'all';

    window.openEquipmentModal = async function () {
        document.getElementById('equipment-modal-overlay').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        document.getElementById('eq-modal-loading').style.display = 'block';
        document.getElementById('eq-modal-body').style.display    = 'none';
        document.getElementById('eq-modal-empty').style.display   = 'none';
        document.getElementById('eq-modal-body').innerHTML = '';
        if (!eqData.length) {
            try {
                const res  = await fetch(EP_EQ_STUDENT, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const json = await res.json();
                eqData = json.data ?? [];
            } catch (e) { console.error(e); }
        }
        document.getElementById('eq-modal-loading').style.display = 'none';
        renderEqModal();
    };

    window.closeEquipmentModal = function () { document.getElementById('equipment-modal-overlay').style.display = 'none'; document.body.style.overflow = ''; };

    window.filterEqModal = function (type, btn) {
        document.querySelectorAll('#equipment-modal-overlay .shop-filter-btn').forEach(b => b.classList.remove('is-active'));
        if (btn) btn.classList.add('is-active');
        eqModalFilter = type;
        renderEqModal();
    };

    function renderEqModal() {
        const body = document.getElementById('eq-modal-body'), empty = document.getElementById('eq-modal-empty');
        body.innerHTML = '';
        const filtered = eqModalFilter === 'all' ? eqData : eqData.filter(e => e.status === eqModalFilter);
        if (!filtered.length) { body.style.display = 'none'; empty.style.display = 'block'; return; }
        empty.style.display = 'none'; body.style.display = 'flex';
        filtered.forEach(eq => {
            const inMaint = eq.status === 'manutencao';
            const row = document.createElement('div');
            row.style.cssText = `display:flex; align-items:center; justify-content:space-between; padding:10px 14px; border-radius:12px; gap:10px; background:${inMaint ? 'rgba(251,191,36,0.06)' : 'rgba(255,255,255,0.03)'}; border:1px solid ${inMaint ? 'rgba(251,191,36,0.20)' : 'rgba(255,255,255,0.07)'};`;
            row.innerHTML = `<div style="display:flex; align-items:center; gap:10px; flex:1; min-width:0;"><div style="width:8px; height:8px; border-radius:50%; flex-shrink:0; background:${inMaint ? '#fbbf24' : '#4ade80'}; box-shadow: 0 0 0 3px ${inMaint ? 'rgba(251,191,36,0.20)' : 'rgba(74,222,128,0.18)'};"></div><span style="font-size:13px; font-weight:600; color:var(--text-white); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${escEqHtml(eq.name)}</span></div><span style="font-size:10px; font-weight:800; letter-spacing:.06em; text-transform:uppercase; padding:2px 9px; border-radius:99px; white-space:nowrap; flex-shrink:0; background:${inMaint ? 'rgba(251,191,36,0.12)' : 'rgba(74,222,128,0.10)'}; border:1px solid ${inMaint ? 'rgba(251,191,36,0.25)' : 'rgba(74,222,128,0.20)'}; color:${inMaint ? '#fbbf24' : '#4ade80'};">${inMaint ? '⚠ Manutenção' : '● Disponível'}</span>`;
            body.appendChild(row);
        });
    }

    function escEqHtml(str) { const d = document.createElement('div'); d.textContent = str ?? ''; return d.innerHTML; }

    async function checkMaintenanceNotify() {
        if (!MAINT_NOTIFY_ALLOWED_ROLES.includes(DASH_USER_ROLE)) return;

        try {
            const res  = await fetch("{{ route('maintenance.index') }}", { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const json = await res.json();
            const inMaint = json.in_maintenance ?? [];
            if (!inMaint.length) return;
            const notifySignature = inMaint.map(e => e.id ?? e.name).sort().join('|');
            if (localStorage.getItem(MAINT_NOTIFY_STORAGE_KEY) === notifySignature) return;

            const count = inMaint.length;
            document.getElementById('maint-notify-msg').textContent = `${count} equipamento${count > 1 ? 's estão' : ' está'} em manutenção no momento.`;
            const listEl = document.getElementById('maint-notify-list');
            listEl.innerHTML = '';
            inMaint.slice(0, 3).forEach(e => {
                const item = document.createElement('div');
                item.style.cssText = 'display:flex; align-items:center; gap:8px; padding:8px 12px; border-radius:10px; background:rgba(251,191,36,0.07); border:1px solid rgba(251,191,36,0.18);';
                item.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="stroke:#fbbf24; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; flex-shrink:0;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg><span style="font-size:13px; font-weight:600; color:rgba(255,255,255,0.85);">${escEqHtml(e.name)}</span>`;
                listEl.appendChild(item);
            });
            if (count > 3) { const more = document.createElement('p'); more.style.cssText = 'font-size:12px; color:var(--text-muted); text-align:center; margin:4px 0 0;'; more.textContent = `+ ${count - 3} outro${count - 3 > 1 ? 's' : ''}`; listEl.appendChild(more); }
            const notifyOverlay = document.getElementById('maint-notify-overlay');
            notifyOverlay.style.pointerEvents = '';
            notifyOverlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            localStorage.setItem(MAINT_NOTIFY_STORAGE_KEY, notifySignature);
        } catch (e) { console.error('Notify check error:', e); }
    }

    setTimeout(checkMaintenanceNotify, 1200);

    window.closeNotifyModal = function () {
        const overlay = document.getElementById('maint-notify-overlay');
        overlay.style.display = 'none';
        document.body.style.overflow = '';
    };

    function confirmCancelPlan() { document.getElementById('cancel-modal-overlay').style.display = 'flex'; document.body.style.overflow = 'hidden'; }
    function closeCancelModal()  { document.getElementById('cancel-modal-overlay').style.display = 'none'; document.body.style.overflow = ''; }
</script>
</x-app-layout>
