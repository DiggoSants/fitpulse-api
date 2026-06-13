<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- HERO --}}
            <div class="dash-hero">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Relatórios</div>
                        <h2 class="dash-hero__title">Fidelidade</h2>
                        <p class="dash-hero__sub">Percentual calculado pela agenda do aluno e presenças registradas</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            {{ Auth::user()->isInstructor() ? 'INSTRUTOR' : 'GERENTE' }}
                        </span>
                        @if(Auth::user()->isManager())
                            <a href="{{ route('reports.plans.comparative') }}" class="btn-ghost" style="text-decoration:none;">
                                Ver Comparativo
                            </a>
                            <a href="{{ route('reports.plans.cancellations') }}" class="btn-ghost" style="text-decoration:none;">
                                Ver Cancelamentos
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- FILTROS --}}
            <form method="GET" action="{{ route('reports.plans.loyalty') }}" class="mgr-section" style="margin-bottom:24px;">
                <div class="mgr-section-head" style="align-items:center;">
                    <div style="align-self:center;">
                        <p class="section-label" style="margin-bottom:0;">FILTROS</p>
                    </div>
                    <label style="display:flex; flex-direction:column; gap:6px; min-width:220px;">
                        <span style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.08em;">Aluno</span>
                        <select name="student_id" class="mgr-search" style="width:100%;">
                            <option value="">Todos</option>
                            @foreach($studentOptions as $student)
                                <option value="{{ $student['id'] }}" @selected((string) request('student_id') === (string) $student['id'])>
                                    {{ $student['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label style="display:flex; flex-direction:column; gap:6px; min-width:150px;">
                        <span style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.08em;">Período</span>
                        <select name="period" class="mgr-search" style="width:100%;">
                            <option value="month" @selected($period === 'month')>Mês atual</option>
                            <option value="custom" @selected($period === 'custom')>Personalizado</option>
                        </select>
                    </label>
                    <label style="display:flex; flex-direction:column; gap:6px; min-width:150px;">
                        <span style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.08em;">Início</span>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="mgr-search" style="width:100%;">
                    </label>
                    <label style="display:flex; flex-direction:column; gap:6px; min-width:150px;">
                        <span style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.08em;">Fim</span>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="mgr-search" style="width:100%;">
                    </label>
                    <button type="submit" class="btn-save" style="height:42px; padding:0 20px; align-self:flex-end;">Aplicar</button>
                </div>
            </form>

            {{-- CARDS RESUMO --}}
            <div class="mgr-stats" style="margin-bottom:28px; grid-template-columns: repeat(4, 1fr);">
                <div class="mgr-stat-card mgr-stat-card--green" style="padding:14px 16px;">
                    <span class="mgr-stat-card__label" style="font-size:11px;">Alunos no relatório</span>
                    <strong class="mgr-stat-card__value" style="font-size:28px;">{{ $summary['total_students'] }}</strong>
                </div>
                <div class="mgr-stat-card" style="padding:14px 16px;">
                    <span class="mgr-stat-card__label" style="font-size:11px;">Média de fidelidade</span>
                    <div style="display:flex; align-items:baseline; gap:3px;">
                        <strong class="mgr-stat-card__value" style="font-size:28px;">{{ $summary['average_rate'] !== null ? number_format($summary['average_rate'], 1, ',', '.') : '—' }}</strong>
                        @if($summary['average_rate'] !== null)
                            <span style="font-size:16px; color:var(--text-muted); font-weight:600;">%</span>
                        @endif
                    </div>
                </div>
                <div class="mgr-stat-card" style="padding:14px 16px;">
                    <span class="mgr-stat-card__label" style="font-size:11px;">Maior fidelidade</span>
                    <div style="display:flex; align-items:baseline; gap:3px;">
                        <strong class="mgr-stat-card__value" style="font-size:28px;">{{ $summary['best_rate'] !== null ? number_format($summary['best_rate'], 1, ',', '.') : '—' }}</strong>
                        @if($summary['best_rate'] !== null)
                            <span style="font-size:16px; color:var(--text-muted); font-weight:600;">%</span>
                        @endif
                    </div>
                </div>
                @if($summary['low_count'] > 0)
                    <div class="mgr-stat-card" style="padding:14px 16px;">
                        <span class="mgr-stat-card__label" style="font-size:11px;">Baixa fidelidade</span>
                        <strong class="mgr-stat-card__value" style="font-size:28px; color:#f87171;">{{ $summary['low_count'] }}</strong>
                    </div>
                @endif
            </div>

            <div style="margin-bottom:16px;">
                <p class="section-label" style="margin-bottom:0;">PERÍODO: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} ATÉ {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
            </div>

            {{-- TABELA --}}
            @if(count($enrollments) > 0)
                <div class="mgr-table-wrap">
                    <table class="mgr-table">
                        <thead>
                            <tr>
                                <th style="width:48px;">#</th>
                                <th>Aluno</th>
                                <th>Email</th>
                                <th>Plano</th>
                                <th>Período</th>
                                <th>Presenças</th>
                                <th>Fidelidade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $index => $item)
                                <tr style="{{ $item['is_low_fidelity'] ? 'background:rgba(214,21,50,0.06);' : '' }}">
                                    <td>
                                        <span style="font-size:13px; color:var(--text-muted); font-weight:700;">{{ $index + 1 }}º</span>
                                    </td>
                                    <td>
                                        <div class="mgr-student-cell">
                                            <div class="mgr-student-cell__avatar" style="background:rgba(74,222,128,0.12); color:#4ade80;">
                                                {{ mb_strtoupper(mb_substr($item['student_name'], 0, 2)) }}
                                            </div>
                                            <div class="mgr-student-cell__content">
                                                <span class="mgr-student-cell__name">{{ $item['student_name'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="mgr-student-cell__email">{{ $item['student_email'] }}</span>
                                    </td>
                                    <td>
                                        <span style="font-size:13px; color:var(--text-white);">{{ $item['plan_name'] }}</span>
                                    </td>
                                    <td>
                                        <span style="font-size:13px; color:var(--text-muted);">{{ $item['period_label'] }}</span>
                                    </td>
                                    <td>
                                        <span style="font-size:13px; color:var(--text-muted);">
                                            {{ $item['total_present'] }} / {{ $item['total_expected'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($item['fidelity_rate'] === null)
                                            <span class="mgr-badge-neutral" style="font-weight:700;">Sem cálculo</span>
                                            @if($item['message'])
                                                <div style="font-size:11px; color:var(--text-muted); margin-top:5px;">{{ $item['message'] }}</div>
                                            @endif
                                        @else
                                            <span class="{{ $item['is_low_fidelity'] ? 'mgr-badge-bad' : 'mgr-badge-ok' }}" style="font-weight:700;">
                                                {{ number_format($item['fidelity_rate'], 1, ',', '.') }}%
                                            </span>
                                            @if($item['status'])
                                                <div style="font-size:11px; color:var(--text-muted); margin-top:5px;">{{ $item['status'] }}</div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state" style="padding:4rem 1rem;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none"
                        style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 16px; display:block; opacity:.20;">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <p>Nenhum aluno ativo encontrado.</p>
                    <p style="font-size:13px; margin-top:6px; opacity:.45;">
                        O relatório será exibido assim que houver alunos com acesso válido no período.
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>