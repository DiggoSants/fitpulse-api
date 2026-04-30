<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <style>
            /* ── Plano atual card ── */
            .renew-plan-card {
                background: rgba(255,255,255,0.04);
                border: 1px solid rgba(255,255,255,0.08);
                border-left: 3px solid #d61532;
                border-radius: 14px;
                padding: 20px 22px;
            }
            [data-theme="light"] .renew-plan-card {
                background: #fff;
                border-color: rgba(0,0,0,0.08);
                border-left-color: #d61532;
                box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            }
            .renew-plan-card__label {
                font-size: 10px;
                font-weight: 800;
                letter-spacing: .12em;
                text-transform: uppercase;
                color: var(--text-muted);
                margin-bottom: 10px;
            }
            .renew-plan-card__name {
                font-size: 17px;
                font-weight: 800;
                color: #f5f5f5;
                margin: 0 0 4px;
            }
            [data-theme="light"] .renew-plan-card__name { color: #111; }
            .renew-plan-card__sub {
                font-size: 13px;
                color: var(--text-muted);
                margin: 0;
            }
            .renew-plan-card__sub strong { color: #f5f5f5; }
            [data-theme="light"] .renew-plan-card__sub strong { color: #111; }
            .renew-plan-card__price {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 32px;
                letter-spacing: 1px;
                color: #fff;
                line-height: 1;
                margin: 0;
            }
            [data-theme="light"] .renew-plan-card__price { color: #111; }
            .renew-plan-card__period {
                font-size: 11px;
                color: var(--text-muted);
                margin: 2px 0 0;
                text-align: right;
            }

            /* ── Info box ── */
            .renew-info-box {
                padding: 12px 16px;
                border-radius: 10px;
                background: rgba(59,130,246,0.08);
                border: 1px solid rgba(59,130,246,0.18);
                font-size: 12px;
                color: #93c5fd;
                line-height: 1.5;
            }
            [data-theme="light"] .renew-info-box {
                background: rgba(59,130,246,0.06);
                border-color: rgba(59,130,246,0.20);
                color: #1d4ed8;
            }

            /* ── Histórico ── */
            .renew-history {
                background: rgba(255,255,255,0.04);
                border: 1px solid rgba(255,255,255,0.08);
                border-radius: 14px;
                overflow: hidden;
            }
            [data-theme="light"] .renew-history {
                background: #fff;
                border-color: rgba(0,0,0,0.08);
                box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            }
            .renew-history__row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 14px 20px;
                border-bottom: 1px solid rgba(255,255,255,0.05);
                gap: 12px;
                flex-wrap: wrap;
            }
            [data-theme="light"] .renew-history__row { border-bottom-color: rgba(0,0,0,0.06); }
            .renew-history__row:last-child { border-bottom: none; }
            .renew-history__name {
                font-size: 13px;
                font-weight: 700;
                color: #f0f0f0;
                margin: 0 0 3px;
            }
            [data-theme="light"] .renew-history__name { color: #111; }
            .renew-history__date {
                font-size: 11px;
                color: var(--text-muted);
                margin: 0;
            }
            .renew-history__end {
                font-size: 13px;
                font-weight: 700;
                color: #f5f5f5;
                margin: 0 0 3px;
                text-align: right;
            }
            [data-theme="light"] .renew-history__end { color: #111; }
        </style>
    @endpush

    <div class="py-6 form-page">
        <div class="form-watermark" aria-hidden="true">
            <span>RENOV</span>
        </div>

        <div class="form-content enrollment-wrap">

            {{-- CABEÇALHO --}}
            <div class="enrollment-header">
                <div>
                    <div class="enrollment-kicker">Planos</div>
                    <h1 class="enrollment-title">Renovar Plano</h1>
                </div>
                <a href="{{ route('dashboard') }}" class="enrollment-back">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                         style="stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;">
                        <path d="M7.5 2L3.5 6l4 4"/>
                    </svg>
                    Voltar
                </a>
            </div>

            {{-- MENSAGENS --}}
            @if(session('success'))
                <div class="enrollment-info">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="enrollment-errors">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="enrollment-errors">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- PLANO ATUAL --}}
            @if($activeEnrollment)
                <div class="renew-plan-card">
                    <p class="renew-plan-card__label">Plano Atual</p>
                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                        <div>
                            <p class="renew-plan-card__name">{{ $activeEnrollment->plan->name }}</p>
                            <p class="renew-plan-card__sub">
                                Vence em
                                <strong>{{ \Carbon\Carbon::parse($activeEnrollment->end_date)->format('d/m/Y') }}</strong>
                            </p>
                        </div>
                        <div>
                            <p class="renew-plan-card__price">
                                R$ {{ number_format($activeEnrollment->plan->price, 2, ',', '.') }}
                            </p>
                            <p class="renew-plan-card__period">/ {{ $activeEnrollment->plan->duration_days }} dias</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="enrollment-info">
                    Você não possui plano ativo no momento. Escolha um plano abaixo para começar.
                </div>
            @endif

            {{-- FORMULÁRIO DE RENOVAÇÃO --}}
            <form action="{{ route('plans.renew') }}" method="POST">
                @csrf

                <div class="enrollment-card">

                    <p class="enrollment-section-label">Escolha o plano para renovar</p>

                    @if($plans->isEmpty())
                        <div class="enrollment-empty">Nenhum plano disponível no momento.</div>
                    @else
                        <ul class="plan-list">
                            @foreach($plans as $plan)
                                <li class="plan-option">
                                    <input
                                        type="radio"
                                        name="plan_id"
                                        id="plan-{{ $plan->id }}"
                                        value="{{ $plan->id }}"
                                        {{ (old('plan_id') == $plan->id || ($activeEnrollment && $activeEnrollment->plan_id == $plan->id)) ? 'checked' : '' }}
                                    >
                                    <label for="plan-{{ $plan->id }}">
                                        <div class="plan-option__info">
                                            <p class="plan-option__name">{{ $plan->name }}</p>
                                            <p class="plan-option__meta">{{ $plan->duration_days }} dias</p>
                                        </div>
                                        <span class="plan-option__price">
                                            R$ {{ number_format($plan->price, 2, ',', '.') }}
                                        </span>
                                    </label>

                                    @if($plan->benefits)
                                        <button
                                            type="button"
                                            class="plan-option__details-btn"
                                            onclick="openPlanModal('modal-{{ $plan->id }}')"
                                        >
                                            Ver detalhes
                                        </button>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if($activeEnrollment)
                        <div class="renew-info-box">
                            <strong style="display:block; margin-bottom:3px;">Como funciona a renovação?</strong>
                            O novo período será somado à data de vencimento atual, preservando o histórico do plano anterior.
                        </div>
                    @endif

                    <div class="enrollment-actions">
                        <button
                            type="submit"
                            class="btn-save"
                            id="btnRenew"
                            {{ $plans->isEmpty() ? 'disabled' : '' }}
                        >
                            <svg width="13" height="13" viewBox="0 0 14 14" fill="none"
                                 style="stroke:#fff; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round;">
                                <path d="M2 7l4 4 6-6"/>
                            </svg>
                            {{ $activeEnrollment ? 'Renovar Plano' : 'Assinar Plano' }}
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn-cancel">Cancelar</a>
                    </div>

                </div>
            </form>

            {{-- HISTÓRICO DE RENOVAÇÕES --}}
            @if(isset($renewals) && $renewals->count())
                <div>
                    <p class="enrollment-section-label" style="margin-top:8px;">Histórico de Renovações</p>
                    <div class="renew-history">
                        @foreach($renewals as $renewal)
                            <div class="renew-history__row">
                                <div>
                                    <p class="renew-history__name">{{ $renewal->plan->name ?? '—' }}</p>
                                    <p class="renew-history__date">
                                        {{ \Carbon\Carbon::parse($renewal->renewed_at)->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="renew-history__end">
                                        {{ $renewal->newEnrollment?->end_date
                                            ? \Carbon\Carbon::parse($renewal->newEnrollment->end_date)->format('d/m/Y')
                                            : '—' }}
                                    </p>
                                    <p class="renew-history__date" style="text-align:right;">novo vencimento</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- ── MODAIS DE PLANO (igual ao da matrícula) ── --}}
    @foreach($plans as $plan)
        @if($plan->benefits)
            <div class="plan-modal-overlay" id="modal-{{ $plan->id }}"
                 onclick="if(event.target===this) closePlanModal('modal-{{ $plan->id }}')">
                <div class="plan-modal" role="dialog" aria-modal="true">

                    <button class="plan-modal__close"
                            onclick="closePlanModal('modal-{{ $plan->id }}')"
                            aria-label="Fechar">✕</button>

                    <div class="plan-modal__top">
                        <p class="plan-modal__kicker">Detalhes do Plano</p>
                        <h2 class="plan-modal__name">{{ $plan->name }}</h2>
                        <div class="plan-modal__price-row">
                            <span class="plan-modal__price">
                                R$ {{ number_format($plan->price, 2, ',', '.') }}
                            </span>
                            <span class="plan-modal__price-period">/ mês</span>
                            <span class="plan-modal__duration-badge">
                                {{ $plan->duration_days }} dias
                            </span>
                        </div>
                    </div>

                    <div class="plan-modal__body">
                        <div>
                            <p class="plan-modal__features-label">Benefícios incluídos</p>
                            <ul class="plan-modal__features">
                                @foreach(explode(',', $plan->benefits) as $benefit)
                                    <li class="plan-modal__feature">
                                        <span class="plan-modal__feature-icon plan-modal__feature-icon--yes">
                                            <svg viewBox="0 0 12 12"><path d="M2 6l3 3 5-5"/></svg>
                                        </span>
                                        {{ trim($benefit) }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="plan-modal__footer">
                            <button
                                type="button"
                                class="btn-save"
                                onclick="selectPlan({{ $plan->id }}); closePlanModal('modal-{{ $plan->id }}');"
                            >
                                Selecionar este plano
                            </button>
                            <button
                                type="button"
                                class="btn-cancel"
                                onclick="closePlanModal('modal-{{ $plan->id }}')"
                            >
                                Fechar
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        @endif
    @endforeach

    <script>
        function openPlanModal(id) {
            document.getElementById(id)?.classList.add('is-open');
        }

        function closePlanModal(id) {
            document.getElementById(id)?.classList.remove('is-open');
        }

        function selectPlan(planId) {
            const radio = document.getElementById('plan-' + planId);
            if (radio) radio.checked = true;
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.plan-modal-overlay.is-open')
                    .forEach(el => el.classList.remove('is-open'));
            }
        });

        document.getElementById('btnRenew')?.addEventListener('click', function () {
            this.innerHTML = `
                <svg width="13" height="13" viewBox="0 0 14 14" fill="none"
                     style="stroke:#fff; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round;">
                    <path d="M2 7l4 4 6-6"/>
                </svg>
                Processando...
            `;
            this.classList.add('btn-save--saved');
        });
    </script>
</x-app-layout>