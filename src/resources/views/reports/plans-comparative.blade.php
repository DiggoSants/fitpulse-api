<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- HERO --}}
            <div class="dash-hero">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Relatórios</div>
                        <h2 class="dash-hero__title">Comparativo de Planos</h2>
                        <p class="dash-hero__sub">Visão geral dos planos ativos e seus dados comparativos</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            GERENTE
                        </span>
                        <a href="{{ route('reports.plans.cancellations') }}" class="btn-ghost" style="text-decoration:none;">
                            Ver Cancelamentos
                        </a>
                        <a href="{{ route('reports.plans.loyalty') }}" class="btn-ghost" style="text-decoration:none;">
                            Ver Fidelidade
                        </a>
                    </div>
                </div>
            </div>

            {{-- CARDS RESUMO --}}
            <div class="mgr-stats" style="margin-bottom: 28px;">
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Total de planos</span>
                    <strong class="mgr-stat-card__value">{{ count($plans) }}</strong>
                    <span class="mgr-stat-card__sub">disponíveis</span>
                </div>
                <div class="mgr-stat-card mgr-stat-card--green">
                    <span class="mgr-stat-card__label">Alunos ativos</span>
                    <strong class="mgr-stat-card__value">{{ collect($plans)->sum('active_students') }}</strong>
                    <span class="mgr-stat-card__sub">matriculados</span>
                </div>
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Ticket médio</span>
                    <strong class="mgr-stat-card__value">
                        R$ {{ count($plans) > 0 ? number_format(collect($plans)->avg('price'), 2, ',', '.') : '0,00' }}
                    </strong>
                    <span class="mgr-stat-card__sub">por plano</span>
                </div>
            </div>

            {{-- TABELA --}}
            @if(count($plans) > 0)
                <div class="mgr-table-wrap">
                    <table class="mgr-table">
                        <thead>
                            <tr>
                                <th>Plano</th>
                                <th>Descrição</th>
                                <th>Preço</th>
                                <th>Duração</th>
                                <th>Benefícios</th>
                                <th>Alunos Ativos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plans as $plan)
                                <tr>
                                    <td>
                                        <div class="mgr-student-cell">
                                            <div class="mgr-student-cell__avatar" style="background: rgba(214,21,50,0.15); color: #f87171; font-size:11px;">
                                                {{ mb_strtoupper(mb_substr($plan['name'], 0, 2)) }}
                                            </div>
                                            <div class="mgr-student-cell__content">
                                                <span class="mgr-student-cell__name">{{ $plan['name'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-size:13px; color:var(--text-muted);">
                                            {{ $plan['description'] ?? '—' }}
                                        </span>
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
                                        <span style="font-size:13px; color:var(--text-muted);">
                                            {{ $plan['benefits'] ?? '—' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="mgr-badge-{{ $plan['active_students'] > 0 ? 'ok' : 'neutral' }}">
                                            {{ $plan['active_students'] }} aluno(s)
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
                        <rect x="3" y="3" width="18" height="18" rx="3"/>
                        <path d="M3 9h18M9 21V9"/>
                    </svg>
                    <p>Nenhum plano ativo encontrado.</p>
                    <p style="font-size:13px; margin-top:6px; opacity:.45;">
                        Cadastre planos para visualizar o comparativo.
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>