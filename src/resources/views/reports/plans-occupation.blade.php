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
                        <h2 class="dash-hero__title">Ocupação de Planos</h2>
                        <p class="dash-hero__sub">Distribuição de alunos ativos por plano</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            GERENTE
                        </span>
                         <a href="{{ route('dashboard') }}" class="btn-ghost" style="text-decoration:none;">
                            ← Voltar
                        </a>
                    </div>
                </div>
            </div>

            {{-- CARDS RESUMO --}}
            <div class="mgr-stats" style="margin-bottom: 28px;">
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Total de planos</span>
                    <strong class="mgr-stat-card__value">{{ $occupation->count() }}</strong>
                    <span class="mgr-stat-card__sub">cadastrados</span>
                </div>
                <div class="mgr-stat-card mgr-stat-card--green">
                    <span class="mgr-stat-card__label">Alunos ativos</span>
                    <strong class="mgr-stat-card__value">{{ $totalActive }}</strong>
                    <span class="mgr-stat-card__sub">total</span>
                </div>
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Ticket médio</span>
                    <strong class="mgr-stat-card__value">
                        R$ {{ collect($occupation)->count() > 0 ? number_format(collect($occupation)->avg('price'), 2, ',', '.') : '0,00' }}
                    </strong>
                    <span class="mgr-stat-card__sub">por plano</span>
                </div>
            </div>

            {{-- TABELA --}}
            @if($occupation->count() > 0)
                <div class="mgr-table-wrap">
                    <table class="mgr-table">
                        <thead>
                            <tr>
                                <th>Plano</th>
                                <th>Preço</th>
                                <th>Duração</th>
                                <th>Status</th>
                                <th>Alunos Ativos</th>
                                <th>Percentual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($occupation as $plan)
                                <tr>
                                    <td>
                                        <div class="mgr-student-cell">
                                            <div class="mgr-student-cell__avatar" style="background: rgba(214,21,50,0.15); color: #f87171; font-size:11px;">
                                                {{ mb_strtoupper(mb_substr($plan['plan_name'], 0, 2)) }}
                                            </div>
                                            <div class="mgr-student-cell__content">
                                                <span class="mgr-student-cell__name">{{ $plan['plan_name'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="mgr-badge-ok" style="font-size:13px; font-weight:700;">
                                            R$ {{ number_format($plan['price'], 2, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-size:13px; color:var(--text-white);">
                                            {{ $plan['duration_days'] }} dias
                                        </span>
                                    </td>
                                    <td>
                                        @if($plan['plan_status'] === 'inactive')
                                            <span class="mgr-badge-bad">Inativo</span>
                                        @else
                                            <span class="mgr-badge-ok">Ativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="mgr-badge-{{ $plan['active_students'] > 0 ? 'ok' : 'neutral' }}">
                                            {{ $plan['active_students'] }} aluno(s)
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <div style="flex:1; height:6px; background:rgba(255,255,255,0.1); border-radius:3px; overflow:hidden;">
                                                <div style="height:100%; width:{{ $plan['percentage'] }}%; background:rgba(74,222,128,0.6); border-radius:3px;"></div>
                                            </div>
                                            <span style="font-size:12px; font-weight:700; min-width:50px; text-align:right;">{{ $plan['percentage'] }}%</span>
                                        </div>
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
                        <rect x="3" y="3" width="18" height="18" rx="3"/>
                        <path d="M3 9h18M9 21V9"/>
                    </svg>
                    <p>Nenhum plano ou aluno ativo encontrado.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
