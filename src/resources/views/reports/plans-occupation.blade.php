<x-app-layout>
    @push('styles')
        <style>
            .plan-occupation-cards {
                display: none;
            }

            @media (max-width: 640px) {
                .plan-occupation-table {
                    display: none;
                }

                .plan-occupation-cards {
                    display: grid;
                    gap: 10px;
                }

                .plan-occupation-card {
                    background: rgba(255,255,255,0.04);
                    border: 1px solid rgba(255,255,255,0.08);
                    border-radius: 16px;
                    padding: 14px;
                }

                .plan-occupation-card__top {
                    display: flex;
                    align-items: flex-start;
                    justify-content: space-between;
                    gap: 12px;
                    margin-bottom: 12px;
                }

                .plan-occupation-card__name {
                    color: #f5f5f5;
                    font-size: 15px;
                    font-weight: 800;
                    line-height: 1.2;
                    margin: 0 0 4px;
                    overflow-wrap: anywhere;
                }

                .plan-occupation-card__meta {
                    color: var(--text-muted);
                    font-size: 12px;
                    margin: 0;
                }

                .plan-occupation-card__grid {
                    display: grid;
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 8px;
                    margin-bottom: 12px;
                }

                .plan-occupation-card__metric {
                    border-radius: 12px;
                    background: rgba(255,255,255,0.035);
                    border: 1px solid rgba(255,255,255,0.07);
                    padding: 10px;
                }

                .plan-occupation-card__metric span {
                    display: block;
                    color: var(--text-muted);
                    font-size: 10px;
                    font-weight: 800;
                    letter-spacing: .07em;
                    text-transform: uppercase;
                    margin-bottom: 4px;
                }

                .plan-occupation-card__metric strong {
                    display: block;
                    color: #f5f5f5;
                    font-size: 13px;
                    font-weight: 800;
                    overflow-wrap: anywhere;
                }

                .plan-occupation-card__bar {
                    height: 7px;
                    background: rgba(255,255,255,0.09);
                    border-radius: 999px;
                    overflow: hidden;
                }

                .plan-occupation-card__bar span {
                    display: block;
                    height: 100%;
                    background: rgba(74,222,128,0.75);
                    border-radius: inherit;
                }

                [data-theme="light"] .plan-occupation-card {
                    background: #fff;
                    border-color: rgba(0,0,0,0.08);
                }

                [data-theme="light"] .plan-occupation-card__name,
                [data-theme="light"] .plan-occupation-card__metric strong {
                    color: #111;
                }

                [data-theme="light"] .plan-occupation-card__metric {
                    background: rgba(0,0,0,0.025);
                    border-color: rgba(0,0,0,0.07);
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
                <div class="mgr-table-wrap plan-occupation-table">
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

                <div class="plan-occupation-cards">
                    @foreach($occupation as $plan)
                        <article class="plan-occupation-card">
                            <div class="plan-occupation-card__top">
                                <div style="min-width:0;">
                                    <p class="plan-occupation-card__name">{{ $plan['plan_name'] }}</p>
                                    <p class="plan-occupation-card__meta">{{ $plan['duration_days'] }} dias</p>
                                </div>
                                @if($plan['plan_status'] === 'inactive')
                                    <span class="mgr-badge-bad">Inativo</span>
                                @else
                                    <span class="mgr-badge-ok">Ativo</span>
                                @endif
                            </div>

                            <div class="plan-occupation-card__grid">
                                <div class="plan-occupation-card__metric">
                                    <span>Preço</span>
                                    <strong>R$ {{ number_format($plan['price'], 2, ',', '.') }}</strong>
                                </div>
                                <div class="plan-occupation-card__metric">
                                    <span>Alunos</span>
                                    <strong>{{ $plan['active_students'] }} ativo(s)</strong>
                                </div>
                            </div>

                            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:7px;">
                                <span style="font-size:11px;font-weight:800;color:var(--text-muted);text-transform:uppercase;letter-spacing:.07em;">Ocupação</span>
                                <strong style="font-size:12px;color:#4ade80;">{{ $plan['percentage'] }}%</strong>
                            </div>
                            <div class="plan-occupation-card__bar">
                                <span style="width:{{ $plan['percentage'] }}%;"></span>
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
                    <p>Nenhum plano ou aluno ativo encontrado.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
