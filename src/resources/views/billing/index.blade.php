<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <style>
            /* ── Plano ativo card ── */
            .billing-plan-card {
                background: rgba(255,255,255,0.04);
                border: 1px solid rgba(255,255,255,0.08);
                border-left: 3px solid #d61532;
                border-radius: 14px;
                padding: 20px 22px;
            }
            [data-theme="light"] .billing-plan-card {
                background: #fff;
                border-color: rgba(0,0,0,0.08);
                border-left-color: #d61532;
                box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            }
            .billing-plan-card__label {
                font-size: 10px;
                font-weight: 800;
                letter-spacing: .12em;
                text-transform: uppercase;
                color: var(--text-muted);
                margin-bottom: 10px;
            }
            .billing-plan-card__name {
                font-size: 17px;
                font-weight: 800;
                color: #f5f5f5;
                margin: 0 0 4px;
            }
            [data-theme="light"] .billing-plan-card__name { color: #111; }
            .billing-plan-card__sub {
                font-size: 13px;
                color: var(--text-muted);
                margin: 0;
            }
            .billing-plan-card__sub strong { color: #f5f5f5; }
            [data-theme="light"] .billing-plan-card__sub strong { color: #111; }
            .billing-plan-card__price {
                font-family: 'Bebas Neue', sans-serif;
                font-size: 32px;
                letter-spacing: 1px;
                color: #fff;
                line-height: 1;
                margin: 0;
            }
            [data-theme="light"] .billing-plan-card__price { color: #111; }
            .billing-plan-card__period {
                font-size: 11px;
                color: var(--text-muted);
                margin: 2px 0 0;
                text-align: right;
            }

            /* ── Info box ── */
            .billing-info-box {
                padding: 12px 16px;
                border-radius: 10px;
                background: rgba(59,130,246,0.08);
                border: 1px solid rgba(59,130,246,0.18);
                font-size: 12px;
                color: #93c5fd;
                line-height: 1.5;
            }
            [data-theme="light"] .billing-info-box {
                background: rgba(59,130,246,0.06);
                border-color: rgba(59,130,246,0.20);
                color: #1d4ed8;
            }

            /* ── Histórico ── */
            .billing-history {
                background: rgba(255,255,255,0.04);
                border: 1px solid rgba(255,255,255,0.08);
                border-radius: 14px;
                overflow: hidden;
            }
            [data-theme="light"] .billing-history {
                background: #fff;
                border-color: rgba(0,0,0,0.08);
                box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            }
            .billing-history__row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 14px 20px;
                border-bottom: 1px solid rgba(255,255,255,0.05);
                gap: 12px;
                flex-wrap: wrap;
            }
            [data-theme="light"] .billing-history__row { border-bottom-color: rgba(0,0,0,0.06); }
            .billing-history__row:last-child { border-bottom: none; }
            .billing-history__name {
                font-size: 13px;
                font-weight: 700;
                color: #f0f0f0;
                margin: 0 0 3px;
            }
            [data-theme="light"] .billing-history__name { color: #111; }
            .billing-history__date {
                font-size: 11px;
                color: var(--text-muted);
                margin: 0;
            }
            .billing-history__method {
                font-size: 11px;
                font-weight: 500;
                color: var(--text-muted);
                margin-left: 6px;
            }
            .billing-history__amount {
                font-size: 14px;
                font-weight: 800;
                color: #f5f5f5;
            }
            [data-theme="light"] .billing-history__amount { color: #111; }

            /* ── Badges de método ── */
            .billing-method-badge {
                font-size: 11px;
                font-weight: 700;
                border-radius: 6px;
                padding: 3px 8px;
                border: 1px solid transparent;
            }
            .billing-method-badge--pix    { color:#4ade80; background:rgba(74,222,128,0.10);  border-color:rgba(74,222,128,0.20); }
            .billing-method-badge--card   { color:#93c5fd; background:rgba(59,130,246,0.10);  border-color:rgba(59,130,246,0.20); }
            .billing-method-badge--boleto { color:#fbbf24; background:rgba(251,191,36,0.10);  border-color:rgba(251,191,36,0.20); }
            [data-theme="light"] .billing-method-badge--pix    { color:#15803d; background:rgba(22,163,74,0.08);  border-color:rgba(22,163,74,0.20); }
            [data-theme="light"] .billing-method-badge--card   { color:#1d4ed8; background:rgba(59,130,246,0.08); border-color:rgba(59,130,246,0.20); }
            [data-theme="light"] .billing-method-badge--boleto { color:#b45309; background:rgba(217,119,6,0.08);  border-color:rgba(217,119,6,0.20); }

            /* ── Badges de status ── */
            .billing-badge {
                font-size: 11px;
                font-weight: 700;
                border-radius: 6px;
                padding: 3px 8px;
                border: 1px solid transparent;
            }
            .billing-badge--confirmed { color:#4ade80; background:rgba(74,222,128,0.10);  border-color:rgba(74,222,128,0.25); }
            .billing-badge--pending   { color:#fbbf24; background:rgba(251,191,36,0.10);  border-color:rgba(251,191,36,0.25); }
            .billing-badge--rejected  { color:#f87171; background:rgba(214,21,50,0.10);   border-color:rgba(214,21,50,0.25); }
            [data-theme="light"] .billing-badge--confirmed { color:#15803d; background:rgba(22,163,74,0.10);  border-color:rgba(22,163,74,0.25); }
            [data-theme="light"] .billing-badge--pending   { color:#b45309; background:rgba(217,119,6,0.10);  border-color:rgba(217,119,6,0.25); }
            [data-theme="light"] .billing-badge--rejected  { color:#b91c1c; background:rgba(185,28,28,0.08);  border-color:rgba(185,28,28,0.20); }
        </style>
    @endpush

    <div class="py-6 form-page">
        <div class="form-watermark" aria-hidden="true">
            <span>PAG</span>
        </div>

        <div class="form-content enrollment-wrap">

            {{-- CABEÇALHO --}}
            <div class="enrollment-header">
                <div>
                    <div class="enrollment-kicker">Financeiro</div>
                    <h1 class="enrollment-title">Pagar Mensalidade</h1>
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
                <div class="enrollment-errors"><p>{{ session('error') }}</p></div>
            @endif

            @if($errors->any())
                <div class="enrollment-errors">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- PLANO ATIVO --}}
            @if($activeEnrollment)
                <div class="billing-plan-card">
                    <p class="billing-plan-card__label">Plano Ativo</p>
                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                        <div>
                            <p class="billing-plan-card__name">{{ $activeEnrollment->plan->name }}</p>
                            <p class="billing-plan-card__sub">
                                Vence em
                                <strong>{{ \Carbon\Carbon::parse($activeEnrollment->end_date)->format('d/m/Y') }}</strong>
                            </p>
                        </div>
                        <div>
                            <p class="billing-plan-card__price">
                                R$ {{ number_format($activeEnrollment->plan->price, 2, ',', '.') }}
                            </p>
                            <p class="billing-plan-card__period">/ {{ $activeEnrollment->plan->duration_days }} dias</p>
                        </div>
                    </div>
                </div>

                {{-- FORMULÁRIO --}}
                <form action="{{ route('billing.process') }}" method="POST">
                    @csrf
                    <input type="hidden" name="enrollment_id" value="{{ $activeEnrollment->id }}">

                    <div class="enrollment-card">

                        <p class="enrollment-section-label">Escolha o método de pagamento</p>

                        <ul class="plan-list">
                            <li class="plan-option">
                                <input type="radio" name="payment_method" id="pix" value="pix"
                                    {{ old('payment_method') == 'pix' ? 'checked' : '' }}>
                                <label for="pix">
                                    <div class="plan-option__info">
                                        <p class="plan-option__name">PIX</p>
                                        <p class="plan-option__meta">Confirmação imediata</p>
                                    </div>
                                    <span class="billing-method-badge billing-method-badge--pix">Instantâneo</span>
                                </label>
                            </li>

                            <li class="plan-option">
                                <input type="radio" name="payment_method" id="credit_card" value="credit_card"
                                    {{ old('payment_method') == 'credit_card' ? 'checked' : '' }}>
                                <label for="credit_card">
                                    <div class="plan-option__info">
                                        <p class="plan-option__name">Cartão de Crédito</p>
                                        <p class="plan-option__meta">Aprovação automática</p>
                                    </div>
                                    <span class="billing-method-badge billing-method-badge--card">Crédito</span>
                                </label>
                            </li>

                            <li class="plan-option">
                                <input type="radio" name="payment_method" id="boleto" value="boleto"
                                    {{ old('payment_method') == 'boleto' ? 'checked' : '' }}>
                                <label for="boleto">
                                    <div class="plan-option__info">
                                        <p class="plan-option__name">Boleto Bancário</p>
                                        <p class="plan-option__meta">Prazo de até 3 dias úteis</p>
                                    </div>
                                    <span class="billing-method-badge billing-method-badge--boleto">Boleto</span>
                                </label>
                            </li>
                        </ul>

                        @error('payment_method')
                            <span class="profile-field-error">{{ $message }}</span>
                        @enderror

                        <div class="billing-info-box">
                            PIX é confirmado na hora. Cartão tem 90% de aprovação automática. Boleto pode levar até 3 dias úteis para compensar.
                        </div>

                        <div class="enrollment-actions">
                            <button type="submit" class="btn-save" id="btnPay">
                                <svg width="13" height="13" viewBox="0 0 14 14" fill="none"
                                     style="stroke:#fff; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round;">
                                    <path d="M2 7l4 4 6-6"/>
                                </svg>
                                Confirmar Pagamento
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn-cancel">Cancelar</a>
                        </div>

                    </div>
                </form>

            @else
                <div class="enrollment-info">
                    Você não possui nenhuma matrícula ativa no momento.
                </div>
                <div style="text-align:center; margin-top:24px;">
                    <a href="{{ route('enrollment.index') }}" class="btn-save"
                       style="text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
                        Fazer Matrícula
                    </a>
                </div>
            @endif

            {{-- HISTÓRICO DE PAGAMENTOS --}}
            @if(isset($payments) && $payments->count())
                <div>
                    <p class="enrollment-section-label" style="margin-top:8px;">Histórico de Pagamentos</p>

                    <div class="billing-history">
                        @foreach($payments as $payment)
                            @php
                                $statusClass = match($payment->status) {
                                    'confirmed' => 'billing-badge--confirmed',
                                    'pending'   => 'billing-badge--pending',
                                    'rejected'  => 'billing-badge--rejected',
                                    default     => '',
                                };
                                $statusLabel = match($payment->status) {
                                    'confirmed' => 'Confirmado',
                                    'pending'   => 'Pendente',
                                    'rejected'  => 'Recusado',
                                    default     => $payment->status,
                                };
                                $methodLabel = match($payment->payment_method) {
                                    'pix'                => 'PIX',
                                    'credit_card','card' => 'Cartão',
                                    'boleto'             => 'Boleto',
                                    default              => $payment->payment_method,
                                };
                            @endphp
                            <div class="billing-history__row">
                                <div>
                                    <p class="billing-history__name">
                                        {{ $payment->plan->name ?? '—' }}
                                        <span class="billing-history__method">via {{ $methodLabel }}</span>
                                    </p>
                                    <p class="billing-history__date">
                                        {{ $payment->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <span class="billing-history__amount">
                                        R$ {{ number_format($payment->amount, 2, ',', '.') }}
                                    </span>
                                    <span class="billing-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

    <script>
        document.getElementById('btnPay')?.addEventListener('click', function () {
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