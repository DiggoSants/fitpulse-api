<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endpush

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- HERO --}}
            <div class="dash-hero" style="margin-bottom:1.5rem;">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Relatórios</div>
                        <h2 class="dash-hero__title">Inadimplência e Churn</h2>
                        <p class="dash-hero__sub">Alunos devendo, cancelados e inativos</p>
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
            <div class="mgr-stats" style="margin-bottom:28px;">
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Devendo</span>
                    <strong class="mgr-stat-card__value" style="color:#f87171;">{{ $delinquents->count() }}</strong>
                    <span class="mgr-stat-card__sub">com pagamento pendente</span>
                </div>
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Cancelados</span>
                    <strong class="mgr-stat-card__value" style="color:#f87171;">{{ $cancelled->count() }}</strong>
                    <span class="mgr-stat-card__sub">plano encerrado</span>
                </div>
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Inativos</span>
                    <strong class="mgr-stat-card__value" style="color:#fbbf24;">{{ $inactive->count() }}</strong>
                    <span class="mgr-stat-card__sub">+30 dias sem presença</span>
                </div>
            </div>

            {{-- ABAS --}}
            <div class="mgr-tabs" style="margin-bottom:24px;">
                <button type="button" class="mgr-tab is-active" onclick="switchTab('delinquents-tab', this)">
                    Devendo
                    <span class="mgr-tab__count">{{ $delinquents->count() }}</span>
                </button>
                <button type="button" class="mgr-tab" onclick="switchTab('cancelled-tab', this)">
                    Cancelados
                    <span class="mgr-tab__count">{{ $cancelled->count() }}</span>
                </button>
                <button type="button" class="mgr-tab" onclick="switchTab('inactive-tab', this)">
                    Inativos
                    <span class="mgr-tab__count">{{ $inactive->count() }}</span>
                </button>
            </div>

            {{-- TABELA DEVENDO --}}
            <div id="delinquents-tab">
                @if($delinquents->count())
                    <div class="mgr-table-wrap">
                        <table class="mgr-table">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Email</th>
                                    <th>Status do usuário</th>
                                    <th>Pagamento</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($delinquents as $student)
                                    <tr>
                                        <td>
                                            <div class="mgr-student-cell">
                                                <div class="mgr-student-cell__avatar">
                                                    {{ mb_strtoupper(mb_substr($student['name'], 0, 2)) }}
                                                </div>
                                                <div class="mgr-student-cell__content">
                                                    <span class="mgr-student-cell__name">{{ $student['name'] }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="mgr-student-cell__email">{{ $student['email'] }}</span></td>
                                        <td>
                                            @if($student['status'] === 'delinquent')
                                                <span class="access-badge-delinquent">Devendo</span>
                                            @elseif($student['status'] === 'blocked')
                                                <span class="mgr-badge-bad">Bloqueado</span>
                                            @else
                                                <span class="mgr-badge-ok">Ativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="mgr-badge-bad">Devendo</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state" style="padding:3rem 1rem;">
                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none"
                             style="stroke:var(--text-muted);stroke-width:1.1;margin:0 auto 14px;display:block;opacity:.20;">
                            <circle cx="12" cy="12" r="9"/><path d="M9 9l6 6M15 9l-6 6"/>
                        </svg>
                        <p>Nenhum aluno devendo encontrado.</p>
                    </div>
                @endif
            </div>

            {{-- TABELA CANCELADOS --}}
            <div id="cancelled-tab" style="display:none;">
                @if($cancelled->count())
                    <div class="mgr-table-wrap">
                        <table class="mgr-table">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Email</th>
                                    <th>Plano cancelado</th>
                                    <th>Cancelado em</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cancelled as $enrollment)
                                    <tr>
                                        <td>
                                            <div class="mgr-student-cell">
                                                <div class="mgr-student-cell__avatar">
                                                    {{ mb_strtoupper(mb_substr($enrollment['name'], 0, 2)) }}
                                                </div>
                                                <div class="mgr-student-cell__content">
                                                    <span class="mgr-student-cell__name">{{ $enrollment['name'] }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="mgr-student-cell__email">{{ $enrollment['email'] }}</span></td>
                                        <td><span style="font-size:13px;color:var(--text-white);font-weight:600;">{{ $enrollment['plan_name'] }}</span></td>
                                        <td><span style="font-size:13px;color:var(--text-muted);">{{ $enrollment['cancelled_at'] }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state" style="padding:3rem 1rem;">
                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none"
                             style="stroke:var(--text-muted);stroke-width:1.1;margin:0 auto 14px;display:block;opacity:.20;">
                            <circle cx="12" cy="12" r="9"/><path d="M9 9l6 6M15 9l-6 6"/>
                        </svg>
                        <p>Nenhum cancelamento registrado.</p>
                    </div>
                @endif
            </div>

            {{-- TABELA INATIVOS --}}
            <div id="inactive-tab" style="display:none;">
                @if($inactive->count())
                    <div class="mgr-table-wrap">
                        <table class="mgr-table">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Email</th>
                                    <th>Última presença</th>
                                    <th>Dias sem aparecer</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($inactive as $student)
                                    <tr>
                                        <td>
                                            <div class="mgr-student-cell">
                                                <div class="mgr-student-cell__avatar">
                                                    {{ mb_strtoupper(mb_substr($student['name'], 0, 2)) }}
                                                </div>
                                                <div class="mgr-student-cell__content">
                                                    <span class="mgr-student-cell__name">{{ $student['name'] }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="mgr-student-cell__email">{{ $student['email'] }}</span></td>
                                        <td><span style="font-size:13px;color:var(--text-muted);">{{ $student['last_frequency'] }}</span></td>
                                        <td>
                                            @if($student['days_inactive'] === null)
                                                <span class="mgr-badge-bad">Nunca registrou</span>
                                            @elseif($student['days_inactive'] >= 60)
                                                <span class="mgr-badge-neutral" style="background:rgba(214,21,50,0.12);border:1px solid rgba(214,21,50,0.25);color:#f87171;">
                                                    {{ $student['days_inactive'] }} dias
                                                </span>
                                            @else
                                                <span class="mgr-badge-neutral" style="background:rgba(251,191,36,0.12);border:1px solid rgba(251,191,36,0.25);color:#fbbf24;">
                                                    {{ $student['days_inactive'] }} dias
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state" style="padding:3rem 1rem;">
                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none"
                             style="stroke:var(--text-muted);stroke-width:1.1;margin:0 auto 14px;display:block;opacity:.20;">
                            <circle cx="12" cy="12" r="9"/><path d="M9 9l6 6M15 9l-6 6"/>
                        </svg>
                        <p>Nenhum aluno inativo encontrado.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script>
        function switchTab(tabId, btn) {
            document.querySelectorAll('[id$="-tab"]').forEach(el => el.style.display = 'none');
            document.getElementById(tabId).style.display = 'block';
            document.querySelectorAll('.mgr-tab').forEach(t => t.classList.remove('is-active'));
            btn.classList.add('is-active');
        }
    </script>
</x-app-layout>