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
                        <h2 class="dash-hero__title">Cancelamentos</h2>
                        <p class="dash-hero__sub">Histórico de cancelamentos de planos com filtro por período</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            GERENTE
                        </span>
                        <a href="{{ route('reports.plans.comparative') }}" class="btn-ghost" style="text-decoration:none;">
                            Ver Comparativo
                        </a>
                        <a href="{{ route('reports.plans.loyalty') }}" class="btn-ghost" style="text-decoration:none;">
                            Ver Fidelidade
                        </a>
                    </div>
                </div>
            </div>

            {{-- FILTRO POR PERÍODO --}}
            <form method="GET" action="{{ route('reports.plans.cancellations') }}"
                style="display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; margin-bottom:24px; padding:20px 22px; background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg);">

                <div style="display:flex; flex-direction:column; gap:6px; flex:1; min-width:160px;">
                    <label style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em;">
                        Data início
                    </label>
                    <input
                        type="date"
                        name="start_date"
                        value="{{ request('start_date') }}"
                        class="mgr-search"
                        style="width:100%; padding:9px 12px;"
                    >
                </div>

                <div style="display:flex; flex-direction:column; gap:6px; flex:1; min-width:160px;">
                    <label style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em;">
                        Data fim
                    </label>
                    <input
                        type="date"
                        name="end_date"
                        value="{{ request('end_date') }}"
                        class="mgr-search"
                        style="width:100%; padding:9px 12px;"
                    >
                </div>

                <button type="submit" class="btn-save" style="padding:9px 22px; font-size:13px;">
                    Filtrar
                </button>

                @if(request('start_date') || request('end_date'))
                    <a href="{{ route('reports.plans.cancellations') }}" class="btn-ghost" style="text-decoration:none; padding:9px 16px; font-size:13px;">
                        Limpar
                    </a>
                @endif
            </form>

            {{-- CARD RESUMO --}}
            <div class="mgr-stats" style="margin-bottom:28px;">
                <div class="mgr-stat-card" style="border-color:rgba(248,113,113,0.2);">
                    <span class="mgr-stat-card__label">Total de cancelamentos</span>
                    <strong class="mgr-stat-card__value" style="color:#f87171;">{{ count($cancellations) }}</strong>
                    <span class="mgr-stat-card__sub">
                        {{ request('start_date') || request('end_date') ? 'no período' : 'registrados' }}
                    </span>
                </div>
            </div>

            {{-- TABELA --}}
            @if(count($cancellations) > 0)
                <div class="mgr-table-wrap">
                    <table class="mgr-table">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Email</th>
                                <th>Plano</th>
                                <th>Início do Plano</th>
                                <th>Vencimento</th>
                                <th>Cancelado em</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cancellations as $item)
                                <tr>
                                    <td>
                                        <div class="mgr-student-cell">
                                            <div class="mgr-student-cell__avatar" style="background:rgba(248,113,113,0.12); color:#f87171;">
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
                                        <span class="mgr-badge-bad">{{ $item['cancelled_at'] }}</span>
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
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M15 9l-6 6M9 9l6 6"/>
                    </svg>
                    <p>Nenhum cancelamento encontrado.</p>
                    <p style="font-size:13px; margin-top:6px; opacity:.45;">
                        {{ request('start_date') || request('end_date') ? 'Tente ajustar o período do filtro.' : 'Nenhum plano foi cancelado até o momento.' }}
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>