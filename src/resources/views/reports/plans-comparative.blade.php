<x-app-layout>
    @push('styles')
        <style>
            /* ── Chips de método de pagamento ─────────────────────────── */
            .pay-chip {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 2px 9px;
                border-radius: 99px;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: .04em;
                white-space: nowrap;
                border: 1px solid rgba(147,197,253,0.25);
                background: rgba(59,130,246,0.08);
                color: #93c5fd;
            }
            .pay-chip--pix        { background:rgba(74,222,128,0.08);  border-color:rgba(74,222,128,0.25);  color:#4ade80; }
            .pay-chip--credito    { background:rgba(251,191,36,0.08);  border-color:rgba(251,191,36,0.25);  color:#fbbf24; }
            .pay-chip--debito     { background:rgba(147,197,253,0.08); border-color:rgba(147,197,253,0.25); color:#93c5fd; }
            .pay-chip--dinheiro   { background:rgba(74,222,128,0.08);  border-color:rgba(74,222,128,0.25);  color:#4ade80; }
            .pay-chip--boleto     { background:rgba(251,191,36,0.08);  border-color:rgba(251,191,36,0.25);  color:#fbbf24; }
            .pay-chip--none       { background:rgba(255,255,255,0.04); border-color:rgba(255,255,255,0.10); color:var(--text-muted); }

            [data-theme="light"] .pay-chip--pix      { background:rgba(22,163,74,0.10);  border-color:rgba(22,163,74,0.25);  color:#15803d; }
            [data-theme="light"] .pay-chip--credito  { background:rgba(202,138,4,0.10);  border-color:rgba(202,138,4,0.25);  color:#92400e; }
            [data-theme="light"] .pay-chip--debito   { background:rgba(37,99,235,0.10);  border-color:rgba(37,99,235,0.25);  color:#1d4ed8; }
            [data-theme="light"] .pay-chip--dinheiro { background:rgba(22,163,74,0.10);  border-color:rgba(22,163,74,0.25);  color:#15803d; }
            [data-theme="light"] .pay-chip--none     { background:rgba(0,0,0,0.04);      border-color:rgba(0,0,0,0.10);      color:#9ca3af; }

            /* ── 4 cards de resumo ────────────────────────────────────── */
            .comparative-stats .mgr-stat-card {
                padding: 18px 24px 16px;
            }
            .comparative-stats .mgr-stat-card__label,
            .comparative-stats .mgr-stat-card__value,
            .comparative-stats .mgr-stat-card__sub {
                white-space: nowrap;
            }
            .comparative-stats .mgr-stat-card__value {
                font-size: 46px;
            }

            @media (max-width: 700px) {
                .comparative-stats {
                    grid-template-columns: repeat(2, 1fr) !important;
                }
                .comparative-stats .mgr-stat-card {
                    padding: 14px 16px 12px;
                }
                .comparative-stats .mgr-stat-card__value {
                    font-size: 34px;
                }
            }

            /* ── Cards mobile ─────────────────────────────────────────── */
            .plan-comparison-cards { display: none; }

            @media (max-width: 700px) {
                .plan-comparison-table  { display: none; }
                .plan-comparison-cards  { display: grid; gap: 12px; }

                .plan-comparison-card {
                    border: 1px solid rgba(255,255,255,0.08);
                    border-radius: 16px;
                    background: rgba(255,255,255,0.04);
                    padding: 16px;
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
                    font-size: 17px;
                    font-weight: 800;
                    line-height: 1.2;
                }
                .plan-comparison-card__desc {
                    margin: 5px 0 0;
                    color: var(--text-muted);
                    font-size: 12px;
                    line-height: 1.5;
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
                }
                .plan-comparison-card__payments {
                    margin-top: 10px;
                    border-top: 1px solid rgba(255,255,255,0.07);
                    padding-top: 10px;
                }
                .plan-comparison-card__payments-label {
                    font-size: 10px;
                    font-weight: 800;
                    text-transform: uppercase;
                    letter-spacing: .08em;
                    color: var(--text-muted);
                    margin: 0 0 7px;
                }

                [data-theme="light"] .plan-comparison-card {
                    background: #fff;
                    border-color: rgba(0,0,0,0.08);
                }
                [data-theme="light"] .plan-comparison-card__name strong { color: #111; }
                [data-theme="light"] .plan-comparison-card__metric {
                    background: rgba(0,0,0,0.03);
                    border-color: rgba(0,0,0,0.07);
                }
                [data-theme="light"] .plan-comparison-card__metric strong { color: #111; }
                [data-theme="light"] .plan-comparison-card__payments { border-top-color: rgba(0,0,0,0.08); }
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
                        <p class="dash-hero__sub">Visão comercial dos planos ativos</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            GERENTE
                        </span>
                        <a href="{{ route('reports.plans.cancellations') }}" class="btn-ghost" style="text-decoration:none;">Ver Cancelamentos</a>
                        <a href="{{ route('reports.plans.loyalty') }}"       class="btn-ghost" style="text-decoration:none;">Ver Fidelidade</a>
                    </div>
                </div>
            </div>

            {{-- CARDS RESUMO --}}
            @php
                $totalStudents  = collect($plans)->sum('active_students');
                $totalRevenue   = collect($plans)->sum('revenue');
                $avgPrice       = count($plans) > 0 ? collect($plans)->avg('price') : 0;
            @endphp

            <div class="mgr-stats comparative-stats" style="margin-bottom:28px; grid-template-columns:repeat(4,1fr);">
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Total de planos</span>
                    <strong class="mgr-stat-card__value">{{ count($plans) }}</strong>
                    <span class="mgr-stat-card__sub">ativos</span>
                </div>
                <div class="mgr-stat-card mgr-stat-card--green">
                    <span class="mgr-stat-card__label">Alunos ativos</span>
                    <strong class="mgr-stat-card__value">{{ $totalStudents }}</strong>
                    <span class="mgr-stat-card__sub">matriculados</span>
                </div>
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Receita estimada</span>
                    <strong class="mgr-stat-card__value">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</strong>
                    <span class="mgr-stat-card__sub">planos × alunos</span>
                </div>
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Ticket médio</span>
                    <strong class="mgr-stat-card__value">R$ {{ number_format($avgPrice, 2, ',', '.') }}</strong>
                    <span class="mgr-stat-card__sub">por plano</span>
                </div>
            </div>

            @if(count($plans) > 0)

                {{-- ── TABELA (desktop) ──────────────────────────────────── --}}
                <div class="mgr-table-wrap plan-comparison-table">
                    <table class="mgr-table">
                        <thead>
                            <tr>
                                <th>Plano</th>
                                <th>Preço</th>
                                <th>Duração</th>
                                <th>Alunos ativos</th>
                                <th>Receita estimada</th>
                                <th>Pagamentos mais usados</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plans as $plan)
                                <tr>
                                    {{-- Nome + descrição --}}
                                    <td>
                                        <div class="mgr-student-cell">
                                            <div class="mgr-student-cell__avatar"
                                                 style="background:rgba(214,21,50,0.15); color:#f87171; font-size:11px;">
                                                {{ mb_strtoupper(mb_substr($plan['name'], 0, 2)) }}
                                            </div>
                                            <div class="mgr-student-cell__content">
                                                <span class="mgr-student-cell__name">{{ $plan['name'] }}</span>
                                                @if($plan['description'])
                                                    <span style="font-size:11px; color:var(--text-muted); display:block; margin-top:1px;">
                                                        {{ $plan['description'] }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Preço --}}
                                    <td>
                                        <span class="mgr-badge-ok" style="font-size:13px; font-weight:700;">
                                            R$ {{ number_format($plan['price'], 2, ',', '.') }}
                                        </span>
                                    </td>

                                    {{-- Duração --}}
                                    <td>
                                        <span style="font-size:13px; color:var(--text-white);">
                                            {{ $plan['duration_days'] }} dias
                                        </span>
                                    </td>

                                    {{-- Alunos ativos --}}
                                    <td>
                                        <span class="mgr-badge-{{ $plan['active_students'] > 0 ? 'ok' : 'neutral' }}">
                                            {{ $plan['active_students'] }} aluno(s)
                                        </span>
                                    </td>

                                    {{-- Receita estimada --}}
                                    <td>
                                        @if($plan['active_students'] > 0)
                                            <span style="font-size:13px; font-weight:700; color:var(--text-white);">
                                                R$ {{ number_format($plan['revenue'], 2, ',', '.') }}
                                            </span>
                                        @else
                                            <span style="font-size:13px; color:var(--text-muted);">—</span>
                                        @endif
                                    </td>

                                    {{-- Formas de pagamento --}}
                                    <td>
                                        @if(count($plan['payment_methods']) > 0)
                                            <div style="display:flex; flex-wrap:wrap; gap:5px; align-items:center;">
                                                @foreach($plan['payment_methods'] as $pm)
                                                    @php
                                                        $chipClass = match(strtolower($pm['method'])) {
                                                            'pix'                           => 'pay-chip--pix',
                                                            'credit_card','credito','credit' => 'pay-chip--credito',
                                                            'debit_card','debito','debit'    => 'pay-chip--debito',
                                                            'cash','dinheiro'               => 'pay-chip--dinheiro',
                                                            'boleto'                        => 'pay-chip--boleto',
                                                            default                         => '',
                                                        };
                                                    @endphp
                                                    <span class="pay-chip {{ $chipClass }}"
                                                          title="{{ $pm['total'] }} pagamento(s)">
                                                        {{ $pm['label'] }}
                                                        <span style="opacity:.55; font-size:10px;">{{ $pm['total'] }}×</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="pay-chip pay-chip--none">Sem dados</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- ── CARDS (mobile) ────────────────────────────────────── --}}
                <div class="plan-comparison-cards">
                    @foreach($plans as $plan)
                        <article class="plan-comparison-card">
                            <div class="plan-comparison-card__top">
                                <div>
                                    <p class="plan-comparison-card__name">{{ $plan['name'] }}</p>
                                    @if($plan['description'])
                                        <p class="plan-comparison-card__desc">{{ $plan['description'] }}</p>
                                    @endif
                                </div>
                                <span class="mgr-badge-{{ $plan['active_students'] > 0 ? 'ok' : 'neutral' }}"
                                      style="white-space:nowrap; flex-shrink:0;">
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
                                <div class="plan-comparison-card__metric" style="grid-column:span 2;">
                                    <span>Receita estimada</span>
                                    <strong>
                                        {{ $plan['active_students'] > 0
                                            ? 'R$ ' . number_format($plan['revenue'], 2, ',', '.')
                                            : '—' }}
                                    </strong>
                                </div>
                            </div>

                            <div class="plan-comparison-card__payments">
                                <p class="plan-comparison-card__payments-label">Pagamentos mais usados</p>
                                @if(count($plan['payment_methods']) > 0)
                                    <div style="display:flex; flex-wrap:wrap; gap:5px;">
                                        @foreach($plan['payment_methods'] as $pm)
                                            @php
                                                $chipClass = match(strtolower($pm['method'])) {
                                                    'pix'                           => 'pay-chip--pix',
                                                    'credit_card','credito','credit' => 'pay-chip--credito',
                                                    'debit_card','debito','debit'    => 'pay-chip--debito',
                                                    'cash','dinheiro'               => 'pay-chip--dinheiro',
                                                    'boleto'                        => 'pay-chip--boleto',
                                                    default                         => '',
                                                };
                                            @endphp
                                            <span class="pay-chip {{ $chipClass }}">
                                                {{ $pm['label'] }}
                                                <span style="opacity:.55; font-size:10px;">{{ $pm['total'] }}×</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="pay-chip pay-chip--none">Sem dados de pagamento</span>
                                @endif
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
