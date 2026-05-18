<x-app-layout>
    @push('styles')
        <style>
        :root {
            --g-bg:       rgba(255,255,255,0.04);
            --g-border:   rgba(255,255,255,0.08);
            --g-surface:  rgba(255,255,255,0.03);
            --g-text:     #f5f5f5;
            --g-muted:    rgba(255,255,255,0.42);
            --g-input-bg: rgba(255,255,255,0.05);
            --g-input-bd: rgba(255,255,255,0.10);
            --g-track:    rgba(255,255,255,0.08);
            --g-ring:     rgba(214,21,50,0.08);
            --g-blue:     #93c5fd;
            --g-green:    #4ade80;
            --g-red:      #f87171;
            --g-red-s:    #d61532;
            --g-amber:    #fbbf24;
            --g-select-opt: #1e1e1e;
            --g-number:   #f5f5f5;
        }
        [data-theme="light"] {
            --g-bg:       rgba(0,0,0,0.035);
            --g-border:   rgba(0,0,0,0.10);
            --g-surface:  rgba(0,0,0,0.025);
            --g-text:     #111111;
            --g-muted:    rgba(0,0,0,0.45);
            --g-input-bg: #f3f4f6;
            --g-input-bd: rgba(0,0,0,0.14);
            --g-track:    rgba(0,0,0,0.09);
            --g-ring:     rgba(214,21,50,0.07);
            --g-blue:     #1d4ed8;
            --g-green:    #16a34a;
            --g-red:      #b91c1c;
            --g-red-s:    #d61532;
            --g-amber:    #d97706;
            --g-select-opt: #fff;
            --g-number:   #111111;
        }

        /* ── Componentes base ─────────────────────────────────────────── */
        .g-card   { background:var(--g-bg); border:1px solid var(--g-border); border-radius:20px; }
        .g-text   { color:var(--g-text)  !important; }
        .g-muted  { color:var(--g-muted) !important; }
        .g-label  {
            font-size:10px; font-weight:800; text-transform:uppercase;
            letter-spacing:.10em; color:var(--g-muted);
            display:flex; align-items:center; gap:8px; margin:0 0 14px;
        }
        .g-label::before {
            content:''; display:inline-block; width:18px; height:1px;
            background:rgba(214,21,50,.70); flex-shrink:0;
        }

        /* ── Inputs ───────────────────────────────────────────────────── */
        .g-input {
            width:100%; padding:11px 14px; border-radius:12px;
            border:1px solid var(--g-input-bd);
            background:var(--g-input-bg); color:var(--g-text);
            font-family:'Montserrat',sans-serif; font-size:13px;
            outline:none; box-sizing:border-box;
            transition:border-color .2s, box-shadow .2s;
        }
        .g-input:focus        { border-color:rgba(214,21,50,.45); box-shadow:0 0 0 3px rgba(214,21,50,.08); }
        .g-input::placeholder { color:var(--g-muted); }

        /* ═══════════════════════════════════════════════════════════════
           FIX 1 — SELECT: texto "Selecione um plano" visível no light
           O browser ignora color em <select> em alguns casos, então
           forçamos via !important e também via color-scheme.
        ═══════════════════════════════════════════════════════════════ */
        .g-select {
            width:100%; padding:11px 14px; border-radius:12px;
            border:1px solid var(--g-input-bd);
            background:var(--g-input-bg);
            color:var(--g-text);
            font-family:'Montserrat',sans-serif; font-size:13px;
            outline:none; appearance:none; -webkit-appearance:none;
            box-sizing:border-box; cursor:pointer;
            transition:border-color .2s;
        }
        .g-select:focus { border-color:rgba(214,21,50,.45); }
        .g-select option {
            background: var(--g-select-opt);
            color: var(--g-text);
        }
        .g-select option[value=""][disabled] {
            color: var(--g-muted);
        }

        /* Light mode — sobrescreve com !important para garantir */
        [data-theme="light"] .g-select {
            color: #111111 !important;
            background: #f3f4f6 !important;
            border-color: rgba(0,0,0,0.14) !important;
            color-scheme: light;
        }
        [data-theme="light"] .g-select option {
            background: #ffffff !important;
            color: #111111 !important;
        }
        [data-theme="light"] .g-select option[value=""][disabled] {
            color: rgba(0,0,0,0.40) !important;
        }

        /* ═══════════════════════════════════════════════════════════════
           FIX 2 — PONTUAÇÃO: número grande e texto auxiliar no light
           Usamos classes com var(--g-number) e var(--g-muted) em vez
           de color hardcoded que some no fundo claro.
        ═══════════════════════════════════════════════════════════════ */
        .g-points-number {
            font-family:'Bebas Neue',sans-serif;
            font-size:56px;
            letter-spacing:2px;
            color: var(--g-number);
            line-height:1;
        }
        .g-points-unit {
            font-size:13px;
            color: var(--g-muted);
            padding-bottom:10px;
        }
        /* Textos de progresso (Nível X, XX%, "X pts no ciclo") */
        .g-prog-text   { font-size:12px; color:var(--g-muted); }
        .g-prog-text11 { font-size:11px; color:var(--g-muted); }
        /* "Faltam N pontos" — N em negrito visível */
        .g-prog-strong { color:var(--g-text) !important; font-weight:700; }

        /* ── Progress track ───────────────────────────────────────────── */
        .g-track { height:8px; background:var(--g-track); border-radius:99px; overflow:hidden; }

        /* ── Milestones ───────────────────────────────────────────────── */
        .g-ms     { flex:1; padding:8px 12px; border-radius:10px; text-align:center;
                    background:var(--g-surface); border:1px solid var(--g-border); }
        .g-ms--hit{ background:rgba(74,222,128,.10)!important; border-color:rgba(74,222,128,.22)!important; }
        .g-ms-val { font-size:10px; font-weight:800; letter-spacing:.06em; color:var(--g-muted); }
        .g-ms--hit .g-ms-val { color:var(--g-green); }
        .g-ms-sub { font-size:9px; color:var(--g-muted); margin-top:2px; }

        /* ── Banners ──────────────────────────────────────────────────── */
        .g-banner         { display:flex; align-items:center; gap:14px; padding:16px 22px; border-radius:16px; margin-bottom:14px; }
        .g-banner--bonus  { background:rgba(74,222,128,.07); border:1px solid rgba(74,222,128,.20); }
        .g-banner--pending{ background:var(--g-bg); border:1px solid var(--g-border); }

        /* ── Grupo card (dentro) ──────────────────────────────────────── */
        .g-group-card { background:var(--g-bg); border:1px solid rgba(59,130,246,.22); border-radius:20px; padding:24px 22px; margin-bottom:14px; }
        .g-stat-blue  { padding:12px 14px; border-radius:12px; text-align:center; background:rgba(59,130,246,.08); border:1px solid rgba(59,130,246,.18); }

        /* ── Membro row ───────────────────────────────────────────────── */
        .g-member-row { display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:10px; background:var(--g-surface); border:1px solid var(--g-border); }

        /* ── Grupo na lista ───────────────────────────────────────────── */
        .g-group-row      { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:12px 14px; border-radius:12px; background:var(--g-surface); border:1px solid var(--g-border); }
        .g-group-row-name { font-size:13px; font-weight:700; color:var(--g-text); margin-bottom:2px; }
        .g-group-row-sub  { font-size:11px; color:var(--g-muted); }

        /* ── Botão entrar (lista) ─────────────────────────────────────── */
        .g-btn-join { padding:7px 16px; border-radius:99px; border:1px solid rgba(59,130,246,.30); background:rgba(59,130,246,.10); color:var(--g-blue); font-size:12px; font-weight:700; cursor:pointer; font-family:'Montserrat',sans-serif; white-space:nowrap; transition:background .18s; }
        .g-btn-join:hover { background:rgba(59,130,246,.20); }

        /* ── Tiers ────────────────────────────────────────────────────── */
        .g-tier         { padding:14px 12px; border-radius:12px; text-align:center; background:var(--g-surface); border:1px solid var(--g-border); }
        .g-tier--active { background:rgba(214,21,50,.10)!important; border-color:rgba(214,21,50,.28)!important; }
        .g-tier-val     { font-size:22px; font-weight:800; color:var(--g-text); }
        .g-tier--active .g-tier-val { color:var(--g-red-s); }

        .g-empty { text-align:center; padding:28px; color:var(--g-muted); font-size:13px; }
        .g-sep   { height:1px; background:var(--g-border); margin:14px 0; }

        /* ── Toast ────────────────────────────────────────────────────── */
        #gami-toast { display:none; margin-bottom:14px; padding:12px 18px; border-radius:12px; font-size:13px; font-weight:600; transition:opacity .3s, transform .3s; }

        /* ── Input + botão ID ─────────────────────────────────────────── */
        .g-id-wrap           { display:flex; }
        .g-id-wrap .g-input  { border-radius:12px 0 0 12px; flex:1; width:auto; }
        .g-id-btn            { border-radius:0 12px 12px 0; padding:0 18px; background:rgba(59,130,246,.12); border:1px solid rgba(59,130,246,.28); border-left:none; color:var(--g-blue); font-size:13px; font-weight:700; cursor:pointer; font-family:'Montserrat',sans-serif; white-space:nowrap; transition:background .18s; }
        .g-id-btn:hover      { background:rgba(59,130,246,.22); }

        /* ── Mensagem inline ──────────────────────────────────────────── */
        .g-inline-msg     { display:none; font-size:12px; margin-top:6px; border-radius:8px; padding:7px 11px; }
        .g-inline-msg--ok { background:rgba(74,222,128,.09); border:1px solid rgba(74,222,128,.20); color:var(--g-green); }
        .g-inline-msg--err{ background:rgba(214,21,50,.08); border:1px solid rgba(214,21,50,.20); color:var(--g-red); }

        /* ── Modal de confirmação ─────────────────────────────────────── */
        #confirm-modal-overlay {
            display:none; position:fixed; inset:0;
            background:rgba(0,0,0,0.65); backdrop-filter:blur(4px);
            z-index:9999; align-items:center; justify-content:center; padding:20px;
        }
        #confirm-modal-overlay.is-open { display:flex; }
        #confirm-modal-box {
            background:#161616; border:1px solid rgba(255,255,255,0.10);
            border-radius:20px; width:100%; max-width:380px;
            box-shadow:0 24px 60px rgba(0,0,0,0.50);
            animation:gamiModalIn .22s ease; overflow:hidden;
        }
        [data-theme="light"] #confirm-modal-box {
            background:#fff; border-color:rgba(0,0,0,0.10);
            box-shadow:0 24px 60px rgba(0,0,0,0.15);
        }
        @keyframes gamiModalIn {
            from { opacity:0; transform:scale(.95) translateY(10px); }
            to   { opacity:1; transform:scale(1)  translateY(0);     }
        }
        #confirm-modal-box .cm-header {
            display:flex; align-items:center; gap:12px;
            padding:18px 22px 16px; border-bottom:1px solid var(--g-border);
        }
        #confirm-modal-box .cm-icon {
            width:36px; height:36px; border-radius:10px; flex-shrink:0; display:flex;
            align-items:center; justify-content:center;
            background:rgba(214,21,50,.12); border:1px solid rgba(214,21,50,.25);
        }
        #confirm-modal-box .cm-title { font-size:14px; font-weight:800; color:var(--g-text); margin:0; }
        #confirm-modal-box .cm-body  { padding:18px 22px 22px; }
        #confirm-modal-box .cm-msg   { font-size:13px; color:var(--g-muted); line-height:1.6; margin:0 0 20px; }
        #confirm-modal-box .cm-foot  { display:flex; gap:10px; }
        #confirm-modal-box .cm-cancel {
            flex:1; padding:11px; border-radius:12px;
            background:var(--g-bg); border:1px solid var(--g-border);
            color:var(--g-muted); font-size:13px; font-weight:700; cursor:pointer;
            font-family:'Montserrat',sans-serif; transition:background .15s;
        }
        #confirm-modal-box .cm-cancel:hover { background:var(--g-surface); }
        #confirm-modal-box .cm-confirm {
            flex:2; padding:11px; border-radius:12px;
            background:#d61532; border:none; color:#fff;
            font-size:13px; font-weight:700; cursor:pointer;
            font-family:'Montserrat',sans-serif; transition:opacity .18s;
        }
        #confirm-modal-box .cm-confirm:hover { opacity:.88; }

        @media (max-width: 768px) {
            .dash-hero__inner,
            .dash-hero__right {
                align-items: flex-start;
            }

            .g-card {
                min-width: 0;
            }

            .g-banner,
            .g-group-row,
            .g-member-row {
                align-items: flex-start;
            }

            .g-banner,
            .g-group-row {
                flex-direction: column;
            }

            .g-member-row {
                flex-wrap: wrap;
            }

            .g-id-wrap {
                flex-direction: column;
                gap: 8px;
            }

            .g-id-wrap .g-input,
            .g-id-btn {
                width: 100%;
                border-radius: 12px;
                border: 1px solid var(--g-input-bd);
            }

            .g-btn-join,
            #confirm-modal-box .cm-cancel,
            #confirm-modal-box .cm-confirm {
                width: 100%;
                justify-content: center;
                white-space: normal;
            }

            #confirm-modal-box .cm-foot {
                flex-direction: column-reverse;
            }
        }

        @media (max-width: 640px) {
            .max-w-7xl > div[style*="grid-template-columns:1fr 1fr"],
            .max-w-7xl > div[style*="grid-template-columns: 1fr 1fr"],
            .g-group-card > div[style*="grid-template-columns:repeat(3,1fr)"],
            .g-card div[style*="grid-template-columns:repeat(4,1fr)"] {
                grid-template-columns: 1fr !important;
            }

            .g-points-number {
                font-size: 44px;
            }

            .g-tier,
            .g-stat-blue {
                padding: 12px;
            }

            .g-group-card {
                padding: 18px 16px;
                border-radius: 16px;
            }

            .g-card {
                padding: 18px 16px !important;
                border-radius: 16px;
            }
        }
        </style>
    @endpush

    @php /** @var \App\Models\User $user */ $user = Auth::user(); @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div id="gami-toast"></div>

            {{-- ── HERO ──────────────────────────────────────────────────── --}}
            <div class="dash-hero" style="margin-bottom:20px;">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Sistema de Recompensas</div>
                        <h2 class="dash-hero__title">RECOMPENSAS</h2>
                        <p class="dash-hero__sub">Acumule pontos e ganhe descontos no plano conjunto</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            <span id="hero-points">{{ $user->points }}</span> PTS
                        </span>
                    </div>
                </div>
            </div>

            {{-- ── GRID: PONTUAÇÃO + PROGRESSO ─────────────────────────── --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">

                {{-- Card de pontos --}}
                <div class="g-card" style="padding:24px 22px; position:relative; overflow:hidden;">
                    <div style="position:absolute; top:-30px; right:-30px; width:120px; height:120px;
                        border-radius:50%; background:var(--g-ring); pointer-events:none;"></div>
                    <p class="g-label">Seus pontos</p>
                    {{-- FIX 2: usa .g-points-number e .g-points-unit em vez de inline color --}}
                    <div style="display:flex; align-items:flex-end; gap:8px; margin-bottom:10px;">
                        <span id="points-display" class="g-points-number">{{ $user->points }}</span>
                        <span class="g-points-unit">pontos</span>
                    </div>
                    @if($user->hasGamificationBonus())
                        <span style="display:inline-flex; align-items:center; gap:6px; font-size:11px; font-weight:800;
                            letter-spacing:.06em; text-transform:uppercase; padding:4px 12px; border-radius:99px;
                            background:rgba(74,222,128,.12); border:1px solid rgba(74,222,128,.25); color:var(--g-green);">
                            ★ Bônus ativo — +5% de desconto!
                        </span>
                    @else
                        {{-- FIX 2: usa .g-prog-text e .g-prog-strong --}}
                        <span class="g-prog-text">
                            Faltam <strong class="g-prog-strong">{{ $user->pointsToNextReward() }}</strong> pontos para o próximo bônus
                        </span>
                    @endif
                </div>

                {{-- Card de progresso --}}
                <div class="g-card" style="padding:24px 22px;">
                    <p class="g-label">Progresso para o bônus</p>
                    @php
                        $threshold = 100;
                        $cycle     = $user->points % $threshold;
                        $pct       = $user->points > 0
                            ? ($cycle === 0 && $user->points >= $threshold ? 100 : ($cycle / $threshold) * 100)
                            : 0;
                        $level     = $user->points >= $threshold ? (int)($user->points / $threshold) : 0;
                    @endphp
                    <div style="margin-bottom:18px;">
                        {{-- FIX 2: usa .g-prog-text --}}
                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span class="g-prog-text">Nível {{ $level }}</span>
                            <span id="pct-label" class="g-prog-text">{{ round($pct) }}%</span>
                        </div>
                        <div class="g-track">
                            <div id="progress-bar" style="height:100%; border-radius:99px;
                                background:linear-gradient(90deg,#d61532,#ff5068);
                                width:{{ $pct }}%; transition:width .6s ease;"></div>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-top:6px;">
                            <span class="g-prog-text11">{{ $cycle }} pts no ciclo</span>
                            <span class="g-prog-text11">100 pts para bônus</span>
                        </div>
                    </div>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        @foreach([100, 200, 300] as $ms)
                            <div class="g-ms {{ $user->points >= $ms ? 'g-ms--hit' : '' }}">
                                <div class="g-ms-val">{{ $ms }} pts</div>
                                <div class="g-ms-sub">{{ $user->points >= $ms ? '✓ atingido' : 'pendente' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ── BANNER BÔNUS / PENDENTE ──────────────────────────────── --}}
            @php
                $group        = $user->planGroups()->with('plan')->first();
                $baseDiscount = $group ? $group->baseDiscount() : 0.0;
                $bonus        = $user->gamificationBonus();
                $total        = min($baseDiscount + $bonus, 25.0);
            @endphp

            @if($user->hasGamificationBonus())
                <div class="g-banner g-banner--bonus">
                    <div style="width:42px; height:42px; border-radius:12px; flex-shrink:0;
                        display:flex; align-items:center; justify-content:center;
                        background:rgba(74,222,128,.14); border:1px solid rgba(74,222,128,.25);">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                             style="stroke:var(--g-green); stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <div style="flex:1;">
                        <p style="font-size:13px; font-weight:800; color:var(--g-green); margin:0 0 2px;">Bônus de gamificação ativo!</p>
                        <p class="g-muted" style="font-size:12px; margin:0;">
                            Você tem <strong style="color:var(--g-green);">{{ $user->points }} pontos</strong>
                            → +{{ $bonus }}% de desconto em plano conjunto
                            @if($group) · Desconto total: <strong style="color:var(--g-green);">{{ $total }}%</strong> @endif
                        </p>
                    </div>
                </div>
            @else
                <div class="g-banner g-banner--pending">
                    <div style="width:42px; height:42px; border-radius:12px; flex-shrink:0;
                        display:flex; align-items:center; justify-content:center;
                        background:rgba(214,21,50,.10); border:1px solid rgba(214,21,50,.20);">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                             style="stroke:var(--g-red-s); stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;">
                            <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                        </svg>
                    </div>
                    <div style="flex:1;">
                        <p class="g-text" style="font-size:13px; font-weight:800; margin:0 0 2px;">
                            Acumule mais pontos para desbloquear bônus
                        </p>
                        <p class="g-muted" style="font-size:12px; margin:0;">
                            Faltam <strong class="g-text">{{ $user->pointsToNextReward() }} pontos</strong>
                            → ao atingir 100, você ganha +5% no plano conjunto
                        </p>
                    </div>
                </div>
            @endif

            {{-- ── COMO GANHAR PONTOS ──────────────────────────────────── --}}
            <div class="g-card" style="padding:18px 22px; margin-bottom:14px;">
                <p class="g-label">Como ganhar pontos</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div style="display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:12px;
                        background:rgba(214,21,50,.07); border:1px solid rgba(214,21,50,.16);">
                        <div style="width:36px; height:36px; border-radius:10px; flex-shrink:0; display:flex;
                            align-items:center; justify-content:center;
                            background:rgba(214,21,50,.12); border:1px solid rgba(214,21,50,.22);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                 style="stroke:var(--g-red-s); stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div>
                            <p class="g-text" style="font-size:13px; font-weight:700; margin:0 0 2px;">Registrar presença</p>
                            <p class="g-muted" style="font-size:11px; margin:0;">+10 pontos por dia</p>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:12px;
                        background:var(--g-surface); border:1px solid var(--g-border);">
                        <div style="width:36px; height:36px; border-radius:10px; flex-shrink:0; display:flex;
                            align-items:center; justify-content:center; background:var(--g-bg); border:1px solid var(--g-border);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                 style="stroke:var(--g-muted); stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="g-muted" style="font-size:13px; font-weight:700; margin:0 0 2px;">Mais ações em breve</p>
                            <p class="g-muted" style="font-size:11px; margin:0; opacity:.6;">Novas formas de pontuar</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
                 PLANO CONJUNTO
            ══════════════════════════════════════════════════════════ --}}
            <p class="g-label" style="margin-bottom:14px;">Plano conjunto</p>

            @if($group)
                <div class="g-group-card">
                    <div style="display:flex; align-items:flex-start; justify-content:space-between;
                        gap:14px; flex-wrap:wrap; margin-bottom:20px;">
                        <div>
                            <p style="font-size:10px; font-weight:800; text-transform:uppercase;
                                letter-spacing:.10em; color:rgba(59,130,246,.70); margin:0 0 4px;">Seu grupo</p>
                            <h3 class="g-text" style="font-family:'Bebas Neue',sans-serif; font-size:28px;
                                letter-spacing:2px; margin:0 0 4px;">{{ $group->name }}</h3>
                            <p class="g-muted" style="font-size:12px; margin:0;">
                                Plano: {{ $group->plan->name }}
                                @if($group->owner_id === $user->id)
                                    · <span style="color:rgba(59,130,246,.80);">Você é o responsável</span>
                                @endif
                            </p>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-family:'Bebas Neue',sans-serif; font-size:42px; letter-spacing:1px;
                                color:var(--g-green); line-height:1;">{{ $total }}%</div>
                            <div class="g-muted" style="font-size:11px; margin-top:2px;">desconto total</div>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:20px;">
                        <div class="g-stat-blue">
                            <div style="font-size:20px; font-weight:800; color:var(--g-blue);">{{ $group->memberCount() }}/5</div>
                            <div class="g-muted" style="font-size:10px; margin-top:3px; text-transform:uppercase; letter-spacing:.06em;">membros</div>
                        </div>
                        <div class="g-stat-blue">
                            <div style="font-size:20px; font-weight:800; color:var(--g-blue);">{{ $baseDiscount }}%</div>
                            <div class="g-muted" style="font-size:10px; margin-top:3px; text-transform:uppercase; letter-spacing:.06em;">desc. base</div>
                        </div>
                        <div style="padding:12px 14px; border-radius:12px; text-align:center;
                            background:{{ $bonus > 0 ? 'rgba(74,222,128,.08)' : 'var(--g-surface)' }};
                            border:1px solid {{ $bonus > 0 ? 'rgba(74,222,128,.20)' : 'var(--g-border)' }};">
                            <div style="font-size:20px; font-weight:800;
                                color:{{ $bonus > 0 ? 'var(--g-green)' : 'var(--g-muted)' }};">+{{ $bonus }}%</div>
                            <div class="g-muted" style="font-size:10px; margin-top:3px; text-transform:uppercase; letter-spacing:.06em;">bônus gami.</div>
                        </div>
                    </div>

                    <div style="margin-bottom:18px;">
                        <p class="g-muted" style="font-size:10px; font-weight:800; text-transform:uppercase;
                            letter-spacing:.08em; margin-bottom:8px;">Membros do grupo</p>
                        <div style="display:flex; flex-direction:column; gap:6px;">
                            @foreach($group->members as $member)
                                <div class="g-member-row">
                                    <div style="width:32px; height:32px; border-radius:50%; flex-shrink:0;
                                        background:rgba(59,130,246,.14); border:1px solid rgba(59,130,246,.22);
                                        display:flex; align-items:center; justify-content:center;
                                        font-size:11px; font-weight:800; color:var(--g-blue);">
                                        {{ mb_strtoupper(mb_substr($member->name, 0, 2)) }}
                                    </div>
                                    <div style="flex:1; min-width:0;">
                                        <div class="g-text" style="font-size:13px; font-weight:600;">
                                            {{ $member->name }}
                                            @if($group->owner_id === $member->id)
                                                <span style="font-size:9px; font-weight:800; text-transform:uppercase;
                                                    letter-spacing:.06em; padding:1px 7px; border-radius:99px;
                                                    background:rgba(59,130,246,.14); border:1px solid rgba(59,130,246,.22);
                                                    color:var(--g-blue); margin-left:5px;">responsável</span>
                                            @endif
                                            @if($member->id === $user->id)
                                                <span style="font-size:9px; font-weight:800; text-transform:uppercase;
                                                    letter-spacing:.06em; padding:1px 7px; border-radius:99px;
                                                    background:rgba(74,222,128,.10); border:1px solid rgba(74,222,128,.20);
                                                    color:var(--g-green); margin-left:5px;">você</span>
                                            @endif
                                        </div>
                                        <div class="g-muted" style="font-size:11px;">{{ $member->points }} pontos</div>
                                    </div>
                                    @if($member->hasGamificationBonus())
                                        <span style="font-size:9px; font-weight:800; padding:2px 8px; border-radius:99px;
                                            background:rgba(74,222,128,.10); border:1px solid rgba(74,222,128,.20);
                                            color:var(--g-green);">★ bônus</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if($group->hasVacancy())
                        <div style="display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:12px;
                            background:rgba(251,191,36,.07); border:1px solid rgba(251,191,36,.20); margin-bottom:18px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                 style="stroke:var(--g-amber); stroke-width:2; stroke-linecap:round; flex-shrink:0;">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <p class="g-muted" style="font-size:12px; margin:0;">
                                Ainda {{ 5 - $group->memberCount() }} vaga(s) disponível(is).
                                Compartilhe o ID: <strong class="g-text">#{{ $group->id }}</strong>
                            </p>
                        </div>
                    @endif

                    <button type="button" onclick="openConfirmLeave({{ $group->id }})"
                        style="width:100%; padding:11px; border-radius:12px;
                            background:rgba(214,21,50,.08); border:1px solid rgba(214,21,50,.22);
                            color:var(--g-red-s); font-size:13px; font-weight:700; cursor:pointer;
                            font-family:'Montserrat',sans-serif; transition:background .18s;">
                        Sair do grupo
                    </button>

                    <form id="leave-group-form" action="{{ route('plan-groups.leave', $group->id) }}"
                          method="POST" style="display:none;">
                        @csrf
                    </form>
                </div>

            @else
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">

                    {{-- CRIAR --}}
                    <div class="g-card" style="padding:22px;">
                        <p class="g-label">Criar grupo</p>

                        @if($errors->any())
                            <div style="margin-bottom:12px; padding:10px 14px; border-radius:10px;
                                background:rgba(214,21,50,.08); border:1px solid rgba(214,21,50,.22);
                                color:var(--g-red); font-size:12px;">
                                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                            </div>
                        @endif

                        <form action="{{ route('plan-groups.store') }}" method="POST">
                            @csrf
                            <div style="margin-bottom:12px;">
                                <label class="g-muted" style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Nome do grupo</label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    placeholder="Ex: Turma da manhã" class="g-input">
                            </div>
                            <div style="margin-bottom:16px;">
                                <label class="g-muted" style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">Plano</label>
                                {{-- FIX 1: .g-select tem cor forçada no light mode via CSS --}}
                                <select name="plan_id" class="g-select">
                                    <option value="" disabled {{ old('plan_id') ? '' : 'selected' }}>
                                        Selecione um plano
                                    </option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }} — R$ {{ number_format($plan->price, 2, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn-save"
                                style="width:100%; padding:11px; border-radius:12px; justify-content:center; font-size:13px;">
                                Criar grupo
                            </button>
                        </form>
                    </div>

                    {{-- ENTRAR --}}
                    <div class="g-card" style="padding:22px;">
                        <p class="g-label">Entrar em um grupo</p>

                        <div style="margin-bottom:16px;">
                            <label class="g-muted" style="display:block; font-size:12px; font-weight:600; margin-bottom:6px;">
                                Entrar pelo ID do grupo
                            </label>
                            <div class="g-id-wrap">
                                <input type="number" id="join-id-input" placeholder="Ex: 3" min="1" class="g-input">
                                <button type="button" class="g-id-btn" onclick="joinById()">Entrar</button>
                            </div>
                            <div id="join-id-msg" class="g-inline-msg"></div>
                        </div>

                        <div class="g-sep"></div>

                        <p class="g-muted" style="font-size:11px; font-weight:700; text-transform:uppercase;
                            letter-spacing:.08em; margin-bottom:10px;">Grupos com vagas</p>
                        <div id="groups-loading" class="g-empty">Carregando grupos...</div>
                        <div id="groups-list" style="display:none; flex-direction:column; gap:8px; max-height:220px; overflow-y:auto;"></div>
                        <div id="groups-empty" class="g-empty" style="display:none;">Nenhum grupo com vagas disponíveis.</div>
                    </div>
                </div>
            @endif

            {{-- ── TABELA DE BENEFÍCIOS ──────────────────────────────────── --}}
            <div class="g-card" style="padding:20px 22px; margin-bottom:20px;">
                <p class="g-label">Tabela de benefícios por grupo</p>
                <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:8px;">
                    @foreach([
                        ['members'=>2,'discount'=>5],
                        ['members'=>3,'discount'=>10],
                        ['members'=>4,'discount'=>15],
                        ['members'=>5,'discount'=>20],
                    ] as $tier)
                        @php $isActive = $group && $group->memberCount() === $tier['members']; @endphp
                        <div class="g-tier {{ $isActive ? 'g-tier--active' : '' }}">
                            <div class="g-tier-val">{{ $tier['discount'] }}%</div>
                            <div class="g-muted" style="font-size:10px; margin-top:4px;">{{ $tier['members'] }} membros</div>
                            @if($isActive)
                                <div style="margin-top:6px; font-size:9px; font-weight:800; text-transform:uppercase;
                                    letter-spacing:.06em; color:var(--g-red-s);">● atual</div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <p class="g-muted" style="font-size:11px; margin:12px 0 0; text-align:center;">
                    + até 5% de bônus por gamificação · Desconto máximo total: <strong class="g-text">25%</strong>
                </p>
            </div>

        </div>
    </div>

    {{-- MODAL --}}
    <div id="confirm-modal-overlay">
        <div id="confirm-modal-box">
            <div class="cm-header">
                <div class="cm-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         style="stroke:#f87171; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;">
                        <circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/>
                    </svg>
                </div>
                <p class="cm-title">Sair do grupo?</p>
            </div>
            <div class="cm-body">
                <p class="cm-msg">
                    Você vai sair do grupo. Se for o único membro, o grupo será encerrado automaticamente.
                </p>
                <div class="cm-foot">
                    <button class="cm-cancel" onclick="closeConfirmModal()">Cancelar</button>
                    <button class="cm-confirm" onclick="confirmLeave()">Sim, sair do grupo</button>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <script>document.addEventListener('DOMContentLoaded',()=>showGamiToast(@json(session('success')),'success'));</script>
    @endif
    @if(session('error'))
        <script>document.addEventListener('DOMContentLoaded',()=>showGamiToast(@json(session('error')),'error'));</script>
    @endif

    <script>
    function showGamiToast(msg, type = 'success') {
        const t = document.getElementById('gami-toast');
        t.textContent = msg;
        t.style.display = 'block';
        if (type === 'error') {
            t.style.background  = 'rgba(214,21,50,.10)';
            t.style.border      = '1px solid rgba(214,21,50,.22)';
            t.style.color       = 'var(--g-red)';
        } else {
            t.style.background  = 'rgba(74,222,128,.10)';
            t.style.border      = '1px solid rgba(74,222,128,.22)';
            t.style.color       = 'var(--g-green)';
        }
        setTimeout(() => {
            t.style.opacity = '0'; t.style.transform = 'translateY(-4px)';
            setTimeout(() => { t.style.display='none'; t.style.opacity='1'; t.style.transform='none'; }, 300);
        }, 4000);
    }

    let _leaveGroupId = null;
    function openConfirmLeave(groupId) {
        _leaveGroupId = groupId;
        document.getElementById('confirm-modal-overlay').classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }
    function closeConfirmModal() {
        document.getElementById('confirm-modal-overlay').classList.remove('is-open');
        document.body.style.overflow = '';
        _leaveGroupId = null;
    }
    function confirmLeave() {
        const form = document.getElementById('leave-group-form');
        if (form) form.submit();
    }
    document.getElementById('confirm-modal-overlay').addEventListener('click', function(e) {
        if (e.target === this) closeConfirmModal();
    });

    @if(!$group)
    async function joinById() {
        const id  = document.getElementById('join-id-input').value.trim();
        const msg = document.getElementById('join-id-msg');
        msg.className = 'g-inline-msg';
        msg.style.display = 'none';
        if (!id) { showMsg(msg, 'Informe o ID do grupo.', 'err'); return; }
        try {
            const res = await fetch(`/plan-groups/${id}/join`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });
            if (res.status === 404) { showMsg(msg, 'Nenhum grupo encontrado com esse ID.', 'err'); return; }
            const data = await res.json();
            if (res.ok) {
                showMsg(msg, data.message, 'ok');
                setTimeout(() => location.reload(), 1200);
            } else {
                showMsg(msg, data.message || 'Grupo indisponível. Tente outro ID.', 'err');
            }
        } catch(e) {
            showMsg(msg, 'Erro de conexão. Tente novamente.', 'err');
        }
    }
    document.getElementById('join-id-input')?.addEventListener('keydown', e => { if (e.key === 'Enter') joinById(); });
    function showMsg(el, text, type) {
        el.textContent = text;
        el.className   = 'g-inline-msg g-inline-msg--' + type;
        el.style.display = 'block';
    }
    (async function loadGroups() {
        try {
            const res    = await fetch("{{ route('plan-groups.index') }}", {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json   = await res.json();
            const groups = (json.data ?? []).filter(g => g.has_vacancy);
            document.getElementById('groups-loading').style.display = 'none';
            if (!groups.length) { document.getElementById('groups-empty').style.display = 'block'; return; }
            const list = document.getElementById('groups-list');
            list.style.display = 'flex';
            groups.forEach(g => {
                const el = document.createElement('div');
                el.className = 'g-group-row';
                el.innerHTML = `
                    <div style="flex:1; min-width:0;">
                        <div class="g-group-row-name">${escHtml(g.name)}</div>
                        <div class="g-group-row-sub">#${g.id} · ${escHtml(g.plan)} · ${g.members}/5 · ${g.base_discount}% desc.</div>
                    </div>
                    <form action="/plan-groups/${g.id}/join" method="POST" style="margin:0;">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="g-btn-join">Entrar</button>
                    </form>`;
                list.appendChild(el);
            });
        } catch(e) {
            document.getElementById('groups-loading').textContent = 'Erro ao carregar grupos.';
        }
    })();
    @endif

    function escHtml(s) { const d = document.createElement('div'); d.textContent = s ?? ''; return d.innerHTML; }

    window.updateGamificationCard = function(data) {
        if (!data) return;
        const pts = data.total_points ?? 0;
        const ptsEl  = document.getElementById('points-display');
        const heroEl = document.getElementById('hero-points');
        if (ptsEl)  ptsEl.textContent = pts;
        if (heroEl) heroEl.textContent = pts;
        const cycle = pts % 100;
        const pct   = pts > 0 ? (cycle === 0 && pts >= 100 ? 100 : (cycle / 100) * 100) : 0;
        const barEl = document.getElementById('progress-bar');
        const pctEl = document.getElementById('pct-label');
        if (barEl) barEl.style.width = pct + '%';
        if (pctEl) pctEl.textContent = Math.round(pct) + '%';
        if (data.points_earned > 0)
            showGamiToast(`+${data.points_earned} pontos ganhos! Total: ${pts} pts ⭐`, 'success');
    };
    </script>
</x-app-layout>
