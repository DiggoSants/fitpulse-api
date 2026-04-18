<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endpush

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- HERO --}}
            <div class="dash-hero">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Relatórios</div>
                        <h2 class="dash-hero__title">Fidelidade</h2>
                        <p class="dash-hero__sub">Ranking de alunos ativos ordenados por tempo de permanência</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            GERENTE
                        </span>
                        <a href="{{ route('reports.plans.comparative') }}" class="btn-ghost" style="text-decoration:none;">
                            Ver Comparativo
                        </a>
                        <a href="{{ route('reports.plans.cancellations') }}" class="btn-ghost" style="text-decoration:none;">
                            Ver Cancelamentos
                        </a>
                    </div>
                </div>
            </div>

            {{-- CARDS RESUMO --}}
            <div class="mgr-stats" style="margin-bottom:28px;">
                <div class="mgr-stat-card mgr-stat-card--green">
                    <span class="mgr-stat-card__label">Alunos ativos</span>
                    <strong class="mgr-stat-card__value">{{ count($enrollments) }}</strong>
                    <span class="mgr-stat-card__sub">no ranking</span>
                </div>
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Média de permanência</span>
                    <strong class="mgr-stat-card__value">
                        {{ count($enrollments) > 0 ? round(collect($enrollments)->avg('days_active')) : 0 }}
                    </strong>
                    <span class="mgr-stat-card__sub">dias</span>
                </div>
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Maior fidelidade</span>
                    <strong class="mgr-stat-card__value">
                        {{ count($enrollments) > 0 ? collect($enrollments)->max('days_active') : 0 }}
                    </strong>
                    <span class="mgr-stat-card__sub">dias</span>
                </div>
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
                                <th>Desde</th>
                                <th>Vencimento</th>
                                <th>Tempo Ativo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enrollments as $index => $item)
                                <tr>
                                    <td>
                                        @if($index === 0)
                                            <span style="font-size:18px;" title="1º lugar">🥇</span>
                                        @elseif($index === 1)
                                            <span style="font-size:18px;" title="2º lugar">🥈</span>
                                        @elseif($index === 2)
                                            <span style="font-size:18px;" title="3º lugar">🥉</span>
                                        @else
                                            <span style="font-size:13px; color:var(--text-muted); font-weight:700;">{{ $index + 1 }}º</span>
                                        @endif
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
                                        <span style="font-size:13px; color:var(--text-muted);">{{ $item['start_date'] }}</span>
                                    </td>
                                    <td>
                                        <span style="font-size:13px; color:var(--text-muted);">{{ $item['end_date'] }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $days = $item['days_active'];
                                            $badgeClass = $days >= 180 ? 'mgr-badge-ok' : ($days >= 30 ? 'mgr-badge-neutral' : 'mgr-badge-bad');
                                        @endphp
                                        <span class="{{ $badgeClass }}" style="font-weight:700;">
                                            {{ $days }} {{ $days === 1 ? 'dia' : 'dias' }}
                                        </span>
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
                        O ranking será exibido assim que houver alunos com matrículas ativas.
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>