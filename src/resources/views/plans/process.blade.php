<x-app-layout>
    <div class="py-6 form-page">
        <div class="form-watermark" aria-hidden="true">
            <span>PAG</span>
        </div>

        <div class="form-content enrollment-wrap" style="max-width:600px;">

            {{-- CABEÇALHO --}}
            <div class="enrollment-header">
                <div>
                    <div class="enrollment-kicker">Financeiro</div>
                    <h1 class="enrollment-title">Mensalidade</h1>
                </div>
                <a href="{{ route('dashboard') }}" class="enrollment-back">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                         style="stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;">
                        <path d="M7.5 2L3.5 6l4 4"/>
                    </svg>
                    Voltar
                </a>
            </div>

            {{-- FEEDBACK --}}
            @if(session('success'))
                <div style="padding:14px 18px; background:rgba(74,222,128,0.08); border:1px solid rgba(74,222,128,0.2); border-radius:12px; color:#4ade80; font-size:13px; font-weight:600;">
                    {{ session('success') }}
                </div>
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

            {{-- PLANO ATIVO --}}
            @if($activeEnrollment)
                <div style="
                    background: rgba(255,255,255,0.04);
                    border: 1px solid rgba(255,255,255,0.08);
                    border-radius: 16px;
                    overflow: hidden;
                ">
                    {{-- Cabeçalho do plano --}}
                    <div style="
                        background: linear-gradient(130deg, #1c0307 0%, #300a10 50%, #0e0203 100%);
                        border-bottom: 1px solid rgba(214,21,50,0.18);
                        padding: 22px 24px;
                        position: relative;
                        overflow: hidden;
                    ">
                        <div style="
                            position:absolute; inset:0;
                            background: radial-gradient(ellipse 70% 100% at 100% 50%, rgba(214,21,50,0.14) 0%, transparent 60%);
                            pointer-events:none;
                        "></div>

                        <p style="font-size:10px; font-weight:800; letter-spacing:.14em; text-transform:uppercase; color:rgba(255,255,255,0.32); margin:0 0 6px; position:relative;">
                            Plano Ativo
                        </p>
                        <div style="display:flex; align-items:flex-end; justify-content:space-between; flex-wrap:wrap; gap:12px; position:relative;">
                            <div>
                                <p style="font-family:'Bebas Neue',sans-serif; font-size:32px; letter-spacing:2px; color:#fff; margin:0 0 4px; line-height:1;">
                                    {{ $activeEnrollment->plan->name }}
                                </p>
                                <p style="font-size:12px; color:rgba(255,255,255,0.38); margin:0;">
                                    Vence em
                                    <strong style="color:rgba(255,255,255,0.70);">
                                        {{ \Carbon\Carbon::parse($activeEnrollment->end_date)->format('d/m/Y') }}
                                    </strong>
                                </p>
                            </div>
                            <div style="text-align:right;">
                                <p style="font-family:'Bebas Neue',sans-serif; font-size:40px; letter-spacing:1px; color:#fff; line-height:1; margin:0;">
                                    R$ {{ number_format($activeEnrollment->plan->price, 2, ',', '.') }}
                                </p>
                                <p style="font-size:11px; color:rgba(255,255,255,0.32); margin:2px 0 0;">por mensalidade</p>
                            </div>
                        </div>
                    </div>

                    {{-- Detalhes --}}
                    <div style="padding:18px 24px; display:flex; gap:24px; flex-wrap:wrap; border-bottom:1px solid rgba(255,255,255,0.06);">
                        <div>
                            <p style="font-size:10px; font-weight:800; letter-spacing:.10em; text-transform:uppercase; color:var(--text-muted); margin:0 0 4px;">Duração</p>
                            <p style="font-size:14px; font-weight:700; color:#f5f5f5; margin:0;">{{ $activeEnrollment->plan->duration_days }} dias</p>
                        </div>
                        <div>
                            <p style="font-size:10px; font-weight:800; letter-spacing:.10em; text-transform:uppercase; color:var(--text-muted); margin:0 0 4px;">Início</p>
                            <p style="font-size:14px; font-weight:700; color:#f5f5f5; margin:0;">
                                {{ \Carbon\Carbon::parse($activeEnrollment->start_date)->format('d/m/Y') }}
                            </p>
                        </div>
                        <div>
                            <p style="font-size:10px; font-weight:800; letter-spacing:.10em; text-transform:uppercase; color:var(--text-muted); margin:0 0 4px;">Status</p>
                            @if($activeEnrollment->status === 'active')
                                <span class="mgr-badge-ok">Ativo</span>
                            @else
                                <span class="mgr-badge-bad">Vencido</span>
                            @endif
                        </div>
                    </div>

                    {{-- Ação de pagamento --}}
                    <div style="padding:20px 24px;">
                        <p style="font-size:10px; font-weight:800; letter-spacing:.10em; text-transform:uppercase; color:var(--text-muted); margin:0 0 16px;">
                            Processar Pagamento
                        </p>

                        <form action="{{ route('billing.process') }}" method="POST" id="billingForm">
                            @csrf
                            <input type="hidden" name="enrollment_id" value="{{ $activeEnrollment->id }}">

                            {{-- Método de pagamento (simulado) --}}
                            <div style="margin-bottom:18px;">
                                <p style="font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.08em; margin:0 0 10px;">
                                    Método de Pagamento
                                </p>
                                <div style="display:flex; flex-direction:column; gap:8px;">
                                    @foreach([
                                        ['pix',    'PIX',           'Aprovação imediata'],
                                        ['boleto', 'Boleto',        'Prazo de até 3 dias úteis'],
                                        ['card',   'Cartão de Crédito', 'Aprovação imediata'],
                                    ] as [$val, $label, $hint])
                                        <label style="
                                            display:flex; align-items:center; gap:14px;
                                            padding:13px 16px;
                                            border:1px solid rgba(255,255,255,0.09);
                                            border-radius:12px;
                                            background:rgba(255,255,255,0.04);
                                            cursor:pointer;
                                            transition: border-color .2s, background .2s;
                                        "
                                        onmouseover="this.style.borderColor='rgba(214,21,50,0.30)'; this.style.background='rgba(255,255,255,0.06)'"
                                        onmouseout="this.style.borderColor='rgba(255,255,255,0.09)'; this.style.background='rgba(255,255,255,0.04)'"
                                        >
                                            <input type="radio" name="payment_method" value="{{ $val }}"
                                                {{ old('payment_method', 'pix') === $val ? 'checked' : '' }}
                                                style="accent-color:#d61532; width:16px; height:16px; flex-shrink:0;">
                                            <div>
                                                <p style="font-size:13px; font-weight:700; color:#f0f0f0; margin:0 0 2px;">{{ $label }}</p>
                                                <p style="font-size:11px; color:var(--text-muted); margin:0;">{{ $hint }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Resumo do valor --}}
                            <div style="
                                display:flex; align-items:center; justify-content:space-between;
                                padding:14px 16px;
                                background:rgba(214,21,50,0.06);
                                border:1px solid rgba(214,21,50,0.15);
                                border-radius:12px;
                                margin-bottom:18px;
                            ">
                                <p style="font-size:13px; font-weight:600; color:var(--text-muted); margin:0;">Total a pagar</p>
                                <p style="font-family:'Bebas Neue',sans-serif; font-size:28px; letter-spacing:1px; color:#fff; margin:0;">
                                    R$ {{ number_format($activeEnrollment->plan->price, 2, ',', '.') }}
                                </p>
                            </div>

                            <button type="submit" class="btn-save" id="btnPay" style="width:100%; justify-content:center;">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                     style="stroke:#fff; stroke-width:2.2; stroke-linecap:round; stroke-linejoin:round;">
                                    <rect x="1" y="3" width="12" height="9" rx="2"/>
                                    <path d="M1 6.5h12"/>
                                </svg>
                                Confirmar Pagamento
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="enrollment-empty" style="padding:3rem 1rem;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none"
                         style="stroke:var(--text-muted); stroke-width:1.2; margin:0 auto 16px; display:block; opacity:.25;">
                        <rect x="2" y="5" width="20" height="14" rx="2"/>
                        <path d="M2 10h20"/>
                    </svg>
                    <p>Você não possui plano ativo para pagar mensalidade.</p>
                    <a href="{{ route('enrollment.index') }}" class="btn-save" style="display:inline-flex; margin-top:18px; text-decoration:none;">
                        Escolher um Plano
                    </a>
                </div>
            @endif

            {{-- HISTÓRICO DE PAGAMENTOS --}}
            @if(isset($payments) && $payments->count())
                <div>
                    <p class="section-label" style="margin-top:8px;">Histórico de Pagamentos</p>

                    <div style="
                        background: rgba(255,255,255,0.04);
                        border: 1px solid rgba(255,255,255,0.08);
                        border-radius: 14px;
                        overflow: hidden;
                    ">
                        @foreach($payments as $payment)
                            <div style="
                                display:flex; align-items:center; justify-content:space-between;
                                padding:14px 20px;
                                border-bottom:1px solid rgba(255,255,255,0.05);
                                gap:12px; flex-wrap:wrap;
                            ">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    {{-- Ícone de status --}}
                                    @if($payment->status === 'confirmed')
                                        <div style="width:34px; height:34px; border-radius:50%; background:rgba(74,222,128,0.10); border:1px solid rgba(74,222,128,0.20); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                 style="stroke:#4ade80; stroke-width:2.2; stroke-linecap:round; stroke-linejoin:round;">
                                                <path d="M2 7l4 4 6-6"/>
                                            </svg>
                                        </div>
                                    @elseif($payment->status === 'pending')
                                        <div style="width:34px; height:34px; border-radius:50%; background:rgba(251,191,36,0.10); border:1px solid rgba(251,191,36,0.20); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                 style="stroke:#fbbf24; stroke-width:2.2; stroke-linecap:round;">
                                                <circle cx="7" cy="7" r="5"/>
                                                <path d="M7 4.5V7l1.5 1.5"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div style="width:34px; height:34px; border-radius:50%; background:rgba(248,113,113,0.10); border:1px solid rgba(248,113,113,0.20); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                 style="stroke:#f87171; stroke-width:2.2; stroke-linecap:round;">
                                                <path d="M3 3l8 8M11 3l-8 8"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <div>
                                        <p style="font-size:13px; font-weight:700; color:#f0f0f0; margin:0 0 2px;">
                                            {{ $payment->plan->name ?? 'Plano' }}
                                        </p>
                                        <p style="font-size:11px; color:var(--text-muted); margin:0;">
                                            {{ \Carbon\Carbon::parse($payment->payment_date ?? $payment->created_at)->format('d/m/Y') }}
                                            · {{ ucfirst($payment->payment_method ?? 'pix') }}
                                        </p>
                                    </div>
                                </div>

                                <div style="display:flex; align-items:center; gap:10px;">
                                    <p style="font-size:15px; font-weight:800; color:#f5f5f5; margin:0;">
                                        R$ {{ number_format($payment->amount, 2, ',', '.') }}
                                    </p>

                                    @if($payment->status === 'confirmed')
                                        <span class="mgr-badge-ok">Confirmado</span>
                                    @elseif($payment->status === 'pending')
                                        <span style="
                                            font-size:9px; font-weight:800; letter-spacing:.07em; text-transform:uppercase;
                                            padding:3px 9px; border-radius:99px; white-space:nowrap;
                                            background:rgba(251,191,36,0.10); border:1px solid rgba(251,191,36,0.20); color:#fbbf24;
                                        ">Pendente</span>
                                    @else
                                        <span class="mgr-badge-bad">Recusado</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

    <script>
        document.getElementById('billingForm')?.addEventListener('submit', function () {
            const btn = document.getElementById('btnPay');
            if (!btn) return;
            btn.innerHTML = `
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                     class="btn-save__check"
                     style="stroke:#fff; stroke-width:2.2; stroke-linecap:round; stroke-linejoin:round;">
                    <path d="M2 7l4 4 6-6"/>
                </svg>
                Processando...
            `;
            btn.classList.add('btn-save--saved');
            btn.disabled = true;
        });
    </script>
</x-app-layout>