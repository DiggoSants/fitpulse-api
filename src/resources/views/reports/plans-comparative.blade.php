<x-app-layout>
    @push('styles')
        <style>
            .plan-comparison-cards {
                display: none;
            }

            @media (max-width: 700px) {
                .plan-comparison-table {
                    display: none;
                }

                .plan-comparison-cards {
                    display: grid;
                    gap: 12px;
                }

                .plan-comparison-card {
                    border: 1px solid rgba(255,255,255,0.08);
                    border-radius: 16px;
                    background: rgba(255,255,255,0.04);
                    padding: 16px;
                    overflow: hidden;
                }

                .plan-comparison-card__top {
                    display: flex;
                    align-items: flex-start;
                    justify-content: space-between;
                    gap: 12px;
                    margin-bottom: 12px;
                }

                .plan-comparison-card__name {
                    margin: 0;
                    color: var(--text-white);
                    font-size: 18px;
                    font-weight: 800;
                    line-height: 1.2;
                    overflow-wrap: anywhere;
                }

                .plan-comparison-card__desc {
                    margin: 6px 0 0;
                    color: var(--text-muted);
                    font-size: 13px;
                    line-height: 1.55;
                }

                .plan-comparison-card__grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 10px;
                    margin-top: 12px;
                }

                .plan-comparison-card__metric {
                    border: 1px solid rgba(255,255,255,0.07);
                    border-radius: 12px;
                    background: rgba(255,255,255,0.03);
                    padding: 10px;
                    min-width: 0;
                }

                .plan-comparison-card__metric span {
                    display: block;
                    color: var(--text-muted);
                    font-size: 10px;
                    font-weight: 800;
                    letter-spacing: .08em;
                    text-transform: uppercase;
                    margin-bottom: 5px;
                }

                .plan-comparison-card__metric strong {
                    display: block;
                    color: var(--text-white);
                    font-size: 13px;
                    font-weight: 800;
                    line-height: 1.25;
                    overflow-wrap: anywhere;
                }

                .plan-comparison-card__benefits {
                    margin-top: 10px;
                    border-top: 1px solid rgba(255,255,255,0.07);
                    padding-top: 10px;
                    color: var(--text-muted);
                    font-size: 13px;
                    line-height: 1.5;
                    overflow-wrap: anywhere;
                }

                [data-theme="light"] .plan-comparison-card {
                    background: #fff;
                    border-color: rgba(0,0,0,0.08);
                }

                [data-theme="light"] .plan-comparison-card__name,
                [data-theme="light"] .plan-comparison-card__metric strong {
                    color: #111;
                }

                [data-theme="light"] .plan-comparison-card__metric {
                    background: rgba(0,0,0,0.03);
                    border-color: rgba(0,0,0,0.07);
                }

                [data-theme="light"] .plan-comparison-card__benefits {
                    border-top-color: rgba(0,0,0,0.08);
                }
            }
        </style>
    @endpush

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
                <div class="mgr-table-wrap plan-comparison-table">
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

                <div class="plan-comparison-cards">
                    @foreach($plans as $plan)
                        <article class="plan-comparison-card">
                            <div class="plan-comparison-card__top">
                                <div>
                                    <p class="plan-comparison-card__name">{{ $plan['name'] }}</p>
                                    <p class="plan-comparison-card__desc">
                                        {{ $plan['description'] ?: 'Sem descrição cadastrada.' }}
                                    </p>
                                </div>
                                <span class="mgr-badge-{{ $plan['active_students'] > 0 ? 'ok' : 'neutral' }}">
                                    {{ $plan['active_students'] }} aluno(s)
                                </span>
                            </div>

                            <div class="plan-comparison-card__grid">
                                <div class="plan-comparison-card__metric">
                                    <span>Preço</span>
                                    <strong>R$ {{ number_format($plan['price'], 2, ',', '.') }}</strong>
                                </div>
                                <div class="plan-comparison-card__metric">
                                    <span>Duração</span>
                                    <strong>{{ $plan['duration_days'] }} dias</strong>
                                </div>
                            </div>

                            <div class="plan-comparison-card__benefits">
                                <strong style="display:block;color:var(--text-white);font-size:11px;text-transform:uppercase;letter-spacing:.08em;margin-bottom:5px;">Benefícios</strong>
                                {{ $plan['benefits'] ?: 'Sem benefícios cadastrados.' }}
                            </div>
                        </article>
                    @endforeach
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
