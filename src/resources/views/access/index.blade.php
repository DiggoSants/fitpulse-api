<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endpush

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(Auth::user()->isManager())

                {{-- HERO --}}
                <div class="dash-hero">
                    <div class="dash-hero__ring"></div>
                    <div class="dash-hero__inner">
                        <div>
                            <div class="dash-hero__eyebrow">Administração</div>
                            <h2 class="dash-hero__title">Controle de Acesso</h2>
                            <p class="dash-hero__sub">Gerencie o status de acesso dos alunos</p>
                        </div>
                        <div class="dash-hero__right">
                            <span class="dash-hero__pulse">
                                <span class="dash-hero__pulse-dot"></span>
                                GERENTE
                            </span>
                        </div>
                    </div>
                </div>

                {{-- TOAST FEEDBACK — canto inferior direito --}}
                <div id="access-toast" style="
                    display:none;
                    position:fixed;
                    bottom:28px;
                    right:28px;
                    z-index:9999;
                    padding:12px 20px;
                    border-radius:12px;
                    font-size:13px;
                    font-weight:700;
                     backdrop-filter:blur(8px);
                    font-family:'Montserrat',sans-serif;
                    box-shadow:0 8px 24px rgba(0,0,0,0.3);
                    transition:opacity .3s;
                    max-width:320px;
                "></div>

                {{-- MODAL ALTERAR STATUS --}}
                <div id="status-modal-overlay" style="
                    display:none;
                    position:fixed;
                    inset:0;
                    background:rgba(0,0,0,0.72);
                    backdrop-filter:blur(5px);
                    z-index:1000;
                    align-items:center;
                    justify-content:center;
                    padding:1rem;
                ">
                    <div style="
                        border-radius:20px;
                        width:100%;
                        max-width:380px;
                        overflow:hidden;
                        transform:translateY(16px) scale(0.97);
                        opacity:0;
                        transition:transform .25s cubic-bezier(.22,.68,0,1.2), opacity .22s ease;
                    " id="status-modal-box">
                        <div id="status-modal-header" style="
                            padding:24px 24px 20px;
                            position:relative;
                        ">
                            <div id="status-modal-kicker" style="font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;margin-bottom:4px;">Alterar Status</div>
                            <div style="font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:3px;" id="modal-student-name">—</div>
                            <button onclick="closeStatusModal()" id="status-modal-close" style="
                                position:absolute;top:14px;right:14px;
                                width:28px;height:28px;border-radius:50%;
                                display:flex;align-items:center;justify-content:center;
                                cursor:pointer;font-size:14px;line-height:1;
                            ">✕</button>
                        </div>
                        <div id="status-modal-body" style="padding:22px 24px 24px;display:flex;flex-direction:column;gap:10px;">
                            <p id="status-modal-label" style="font-size:12px;margin:0 0 6px;font-weight:500;">Selecione o novo status:</p>

                            <button onclick="confirmUpdateStatus('active')" class="access-status-btn access-status-btn--active">
                                <span class="mgr-badge-ok" style="pointer-events:none;">Ativo</span>
                                <span class="access-status-hint">Liberar acesso completo</span>
                            </button>

                            <button onclick="confirmUpdateStatus('blocked')" class="access-status-btn access-status-btn--blocked">
                                <span class="mgr-badge-bad" style="pointer-events:none;">Bloqueado</span>
                                <span class="access-status-hint">Bloquear acesso manualmente</span>
                            </button>

                            <button onclick="confirmUpdateStatus('delinquent')" class="access-status-btn access-status-btn--delinquent">
                                <span class="mgr-badge-neutral" style="pointer-events:none;background:rgba(251,191,36,.12);color:#fbbf24;border-color:rgba(251,191,36,.25);">Devendo</span>
                                <span class="access-status-hint">Bloqueio por inadimplência</span>
                            </button>

                            <button onclick="closeStatusModal()" class="btn-cancel" style="margin-top:4px;width:100%;justify-content:center;">Cancelar</button>
                        </div>
                    </div>
                </div>

                {{-- STATS --}}
                <div class="mgr-stats" id="access-stats">
                    <div class="mgr-stat-card mgr-stat-card--green">
                        <span class="mgr-stat-card__label">Ativos</span>
                        <strong class="mgr-stat-card__value" id="count-active">—</strong>
                        <span class="mgr-stat-card__sub">com acesso liberado</span>
                    </div>
                    <div class="mgr-stat-card">
                        <span class="mgr-stat-card__label">Bloqueados</span>
                        <strong class="mgr-stat-card__value" id="count-blocked" style="color:#f87171;">—</strong>
                        <span class="mgr-stat-card__sub">sem acesso</span>
                    </div>
                    <div class="mgr-stat-card">
                        <span class="mgr-stat-card__label">Devendo</span>
                        <strong class="mgr-stat-card__value" id="count-delinquent" style="color:#fbbf24;">—</strong>
                        <span class="mgr-stat-card__sub">pagamento pendente</span>
                    </div>
                </div>

                {{-- FILTROS + BUSCA --}}
                <div class="mgr-tabs" style="margin-bottom:0;border-bottom:none;padding-bottom:0;">
                    <button type="button" class="mgr-tab is-active" onclick="filterAccess('all', this)">
                        Todos
                        <span class="mgr-tab__count" id="tab-count-all">—</span>
                    </button>
                    <button type="button" class="mgr-tab" onclick="filterAccess('active', this)">
                        Ativos
                        <span class="mgr-tab__count" id="tab-count-active">—</span>
                    </button>
                    <button type="button" class="mgr-tab" onclick="filterAccess('blocked', this)">
                        Bloqueados
                        <span class="mgr-tab__count" id="tab-count-blocked">—</span>
                    </button>
                    <button type="button" class="mgr-tab" onclick="filterAccess('delinquent', this)">
                        devendo
                        <span class="mgr-tab__count" id="tab-count-delinquent">—</span>
                    </button>
                </div>

                {{-- TABELA --}}
                <div style="margin-top:16px;">
                    <div class="mgr-section-head">
                        <p class="section-label" style="margin-bottom:0;">ALUNOS</p>
                        <input
                            type="text"
                            id="accessSearch"
                            class="mgr-search"
                            placeholder="Buscar aluno..."
                            oninput="searchAccess()"
                            style="min-width:200px;"
                        >
                    </div>

                    <div class="mgr-table-wrap">
                        <table class="mgr-table">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Email</th>
                                    <th>Status de Acesso</th>
                                    <th>Pagamento</th>
                                    <th>Última Renovação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="access-table-body">
                                <tr id="access-loading-row">
                                    <td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);font-size:13px;">
                                        <div style="display:flex;align-items:center;justify-content:center;gap:10px;">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="animation:spin 1s linear infinite;">
                                                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                                            </svg>
                                            Carregando alunos...
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            @else
                {{-- VISÃO DO ALUNO --}}
                <div class="dash-hero">
                    <div class="dash-hero__ring"></div>
                    <div class="dash-hero__inner">
                        <div>
                            <div class="dash-hero__eyebrow">Minha Conta</div>
                            <h2 class="dash-hero__title">Status de Acesso</h2>
                            <p class="dash-hero__sub">Veja a situação atual do seu acesso</p>
                        </div>
                        <div class="dash-hero__right">
                            @php $student = Auth::user()->student; @endphp
                            @if($student)
                                @if($student->status === 'active')
                                    <span class="dash-hero__pulse">
                                        <span class="dash-hero__pulse-dot"></span>
                                        ATIVO
                                    </span>
                                @elseif($student->status === 'blocked')
                                    <span class="dash-hero__pulse" style="background:rgba(214,21,50,.14);border-color:rgba(214,21,50,.28);color:#f87171;">
                                        <span class="dash-hero__pulse-dot" style="background:#d61532;animation:none;"></span>
                                        BLOQUEADO
                                    </span>
                                @else
                                    <span class="dash-hero__pulse" style="background:rgba(251,191,36,.10);border-color:rgba(251,191,36,.25);color:#fbbf24;">
                                        <span class="dash-hero__pulse-dot" style="background:#fbbf24;animation:none;"></span>
                                        DEVENDO
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                @if($student)
                    <div style="display:flex;flex-direction:column;gap:14px;margin-top:4px;">
                        <div style="
                            padding:28px 28px 24px;
                            border-radius:20px;
                            border:1px solid;
                            {{ $student->status === 'active'
                                ? 'background:rgba(34,197,94,0.06);border-color:rgba(34,197,94,0.20);'
                                : ($student->status === 'blocked'
                                    ? 'background:rgba(214,21,50,0.06);border-color:rgba(214,21,50,0.20);'
                                    : 'background:rgba(251,191,36,0.06);border-color:rgba(251,191,36,0.20);') }}
                        ">
                            <div style="display:flex;align-items:center;gap:18px;flex-wrap:wrap;">
                                <div style="
                                    width:64px;height:64px;border-radius:50%;
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;
                                    {{ $student->status === 'active'
                                        ? 'background:rgba(34,197,94,0.14);border:2px solid rgba(34,197,94,0.30);'
                                        : ($student->status === 'blocked'
                                            ? 'background:rgba(214,21,50,0.14);border:2px solid rgba(214,21,50,0.30);'
                                            : 'background:rgba(251,191,36,0.14);border:2px solid rgba(251,191,36,0.30);') }}
                                ">
                                    @if($student->status === 'active')
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                        </svg>
                                    @elseif($student->status === 'blocked')
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                        </svg>
                                    @else
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                    @endif
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.10em;margin-bottom:6px;
                                        {{ $student->status === 'active' ? 'color:rgba(74,222,128,.60);' : ($student->status === 'blocked' ? 'color:rgba(248,113,113,.60);' : 'color:rgba(251,191,36,.60);') }}
                                    ">Situação atual</div>
                                    @if($student->status === 'active')
                                        <div style="font-family:'Bebas Neue',sans-serif;font-size:36px;letter-spacing:3px;color:#4ade80;line-height:1;">ACESSO ATIVO</div>
                                        <p style="font-size:13px;color:rgba(255,255,255,.55);margin:6px 0 0;">Seu acesso está liberado. Aproveite todos os recursos da plataforma.</p>
                                    @elseif($student->status === 'blocked')
                                        <div style="font-family:'Bebas Neue',sans-serif;font-size:36px;letter-spacing:3px;color:#f87171;line-height:1;">ACESSO BLOQUEADO</div>
                                        <p style="font-size:13px;color:rgba(255,255,255,.55);margin:6px 0 0;">Seu acesso foi bloqueado. Entre em contato com a administração para mais informações.</p>
                                    @else
                                        <div style="font-family:'Bebas Neue',sans-serif;font-size:36px;letter-spacing:3px;color:#fbbf24;line-height:1;">PAGAMENTO PENDENTE</div>
                                        <p style="font-size:13px;color:rgba(255,255,255,.55);margin:6px 0 0;">Existe um pagamento pendente. Regularize sua situação para reativar o acesso.</p>
                                    @endif
                                </div>
                            </div>
                            @if($student->status !== 'active')
                                <div style="margin-top:20px;padding-top:20px;border-top:1px solid rgba(255,255,255,.07);display:flex;gap:10px;flex-wrap:wrap;">
                                    @if($student->status === 'delinquent')
                                        <a href="{{ route('billing.index') }}" class="btn-save" style="text-decoration:none;">Regularizar Pagamento</a>
                                    @endif
                                    <a href="{{ route('dashboard') }}" class="btn-cancel" style="text-decoration:none;display:inline-flex;align-items:center;padding:10px 20px;">Voltar ao Painel</a>
                                </div>
                            @endif
                        </div>

                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;">
                            <div class="mgr-stat-card">
                                <span class="mgr-stat-card__label">Status</span>
                                <div style="margin-top:8px;">
                                    @if($student->status === 'active')
                                        <span class="mgr-badge-ok">Ativo</span>
                                    @elseif($student->status === 'blocked')
                                        <span class="mgr-badge-bad">Bloqueado</span>
                                    @else
                                        <span class="mgr-badge-neutral" style="background:rgba(251,191,36,.12);color:#fbbf24;border-color:rgba(251,191,36,.25);">Devendo</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mgr-stat-card">
                                <span class="mgr-stat-card__label">Pagamento</span>
                                <div style="margin-top:8px;">
                                    @if($student->paymentStatus() === 'paid')
                                        <span class="mgr-badge-ok">Em dia</span>
                                    @else
                                        <span class="mgr-badge-bad">Pendente</span>
                                    @endif
                                </div>
                            </div>
                            @if($student->renewed_at)
                                <div class="mgr-stat-card">
                                    <span class="mgr-stat-card__label">Última Renovação</span>
                                    <strong class="mgr-stat-card__value" style="font-size:18px;margin-top:6px;display:block;">
                                        {{ $student->renewed_at->format('d/m/Y') }}
                                    </strong>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="empty-state" style="padding:4rem 1rem;">
                        <p>Nenhum cadastro de aluno encontrado para sua conta.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Modal — modo escuro */
        #status-modal-box {
            background: #1a1a1a;
            border: 1px solid rgba(255,255,255,0.10);
        }
        #status-modal-header {
            background: linear-gradient(130deg,#1c0307 0%,#300a10 50%,#0e0203 100%);
            border-bottom: 1px solid rgba(214,21,50,0.20);
        }
        #status-modal-kicker { color: rgba(255,255,255,.35); }
        #modal-student-name  { color: #fff; }
        #status-modal-close  {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            color: rgba(255,255,255,0.50);
        }
        #status-modal-close:hover { background: rgba(255,255,255,0.16); color: #fff; }
        #status-modal-label  { color: rgba(255,255,255,0.45); }
        #status-modal-body   { background: #1a1a1a; }

        /* Modal — modo claro */
        [data-theme="light"] #status-modal-box {
            background: #fff;
            border-color: rgba(0,0,0,0.10);
            box-shadow: 0 20px 60px rgba(0,0,0,0.18);
        }
        [data-theme="light"] #status-modal-header {
            background: linear-gradient(130deg,#fce8eb 0%,#ffd6dc 50%,#f5f0f1 100%);
            border-bottom-color: rgba(214,21,50,0.14);
        }
        [data-theme="light"] #status-modal-kicker { color: rgba(0,0,0,0.35); }
        [data-theme="light"] #modal-student-name  { color: #111; }
        [data-theme="light"] #status-modal-close  {
            background: rgba(0,0,0,0.05);
            border-color: rgba(0,0,0,0.10);
            color: rgba(0,0,0,0.45);
        }
        [data-theme="light"] #status-modal-close:hover { background: rgba(0,0,0,0.10); color: #111; }
        [data-theme="light"] #status-modal-label  { color: rgba(0,0,0,0.50); }
        [data-theme="light"] #status-modal-body   { background: #fff; }

        /* Botões de status */
        .access-status-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
            text-align: left;
            width: 100%;
            transition: background .18s, border-color .18s, transform .15s;
        }
        .access-status-hint { font-size: 12px; }

        /* Escuro */
        .access-status-btn {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
        }
        .access-status-btn:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.18);
            transform: translateX(3px);
        }
        .access-status-hint { color: rgba(255,255,255,.55); }
        .access-status-btn--active:hover    { border-color: rgba(34,197,94,0.30)  !important; background: rgba(34,197,94,0.06)  !important; }
        .access-status-btn--blocked:hover   { border-color: rgba(214,21,50,0.30)  !important; background: rgba(214,21,50,0.06)  !important; }
        .access-status-btn--delinquent:hover{ border-color: rgba(251,191,36,0.30) !important; background: rgba(251,191,36,0.06) !important; }

        /* Claro */
        [data-theme="light"] .access-status-btn {
            background: #f5f5f5;
            border-color: rgba(0,0,0,0.10);
        }
        [data-theme="light"] .access-status-btn:hover {
            background: #efefef;
            border-color: rgba(0,0,0,0.20);
        }
        [data-theme="light"] .access-status-hint { color: rgba(0,0,0,0.45); }
        [data-theme="light"] .access-status-btn--active:hover    { border-color: rgba(22,163,74,0.35)   !important; background: rgba(22,163,74,0.07)   !important; }
        [data-theme="light"] .access-status-btn--blocked:hover   { border-color: rgba(214,21,50,0.30)   !important; background: rgba(214,21,50,0.06)   !important; }
        [data-theme="light"] .access-status-btn--delinquent:hover{ border-color: rgba(217,119,6,0.30)   !important; background: rgba(217,119,6,0.06)   !important; }
    </style>

    @if(Auth::user()->isManager())
    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        let allStudents = [];
        let currentFilter = 'all';

       function showToast(msg, type = 'success') {
    const t = document.getElementById('access-toast');
    const isOk = type === 'success';
    const isLight = document.documentElement.getAttribute('data-theme') === 'light';

    if (isLight) {
        t.style.background = isOk ? '#dcfce7' : '#fee2e2';
        t.style.border     = `1px solid ${isOk ? '#86efac' : '#fca5a5'}`;
        t.style.color      = isOk ? '#15803d' : '#b91c1c';
        t.style.boxShadow  = '0 8px 24px rgba(0,0,0,0.15)';
    } else {
        t.style.background = isOk ? 'rgba(34,197,94,0.18)'  : 'rgba(214,21,50,0.18)';
        t.style.border     = `1px solid ${isOk ? 'rgba(34,197,94,0.35)' : 'rgba(214,21,50,0.35)'}`;
        t.style.color      = isOk ? '#4ade80' : '#f87171';
        t.style.boxShadow  = '0 8px 24px rgba(0,0,0,0.3)';
    }

    t.textContent  = msg;
    t.style.display    = 'block';
    t.style.opacity    = '1';
    clearTimeout(t._timer);
    t._timer = setTimeout(() => {
        t.style.opacity = '0';
        setTimeout(() => t.style.display = 'none', 300);
    }, 3500);
}
        

        async function loadStudents() {
            try {
                const res  = await fetch('{{ route("access.students") }}');
                const json = await res.json();
                allStudents = json.data ?? [];
                renderTable(allStudents);
                updateCounts();
            } catch (e) {
                showToast('Erro ao carregar alunos.', 'error');
            }
        }

        function updateCounts() {
            const total      = allStudents.length;
            const active     = allStudents.filter(s => s.status === 'active').length;
            const blocked    = allStudents.filter(s => s.status === 'blocked').length;
            const delinquent = allStudents.filter(s => s.status === 'delinquent').length;
            document.getElementById('count-active').textContent      = active;
            document.getElementById('count-blocked').textContent     = blocked;
            document.getElementById('count-delinquent').textContent  = delinquent;
            document.getElementById('tab-count-all').textContent     = total;
            document.getElementById('tab-count-active').textContent  = active;
            document.getElementById('tab-count-blocked').textContent = blocked;
            document.getElementById('tab-count-delinquent').textContent = delinquent;
        }

        function renderTable(students) {
            const tbody = document.getElementById('access-table-body');
            if (!students.length) {
                tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--text-muted);font-size:13px;">Nenhum aluno encontrado.</td></tr>`;
                return;
            }
            tbody.innerHTML = students.map(s => {
                const initials = s.name.substring(0, 2).toUpperCase();
                return `
                <tr class="access-row" data-id="${s.id}" data-status="${s.status}" data-name="${s.name.toLowerCase()}" data-email="${s.email.toLowerCase()}">
                    <td>
                        <div class="mgr-student-cell">
                            <div class="mgr-student-cell__avatar">${initials}</div>
                            <div class="mgr-student-cell__content">
                                <span class="mgr-student-cell__name">${escHtml(s.name)}</span>
                            </div>
                        </div>
                    </td>
                    <td><span class="mgr-student-cell__email">${escHtml(s.email)}</span></td>
                    <td>${statusBadge(s.status)}</td>
                    <td>${paymentBadge(s.payment_status)}</td>
                    <td style="font-size:12px;color:var(--text-muted);">${s.renewed_at ?? '—'}</td>
                    <td>${actionsHtml(s)}</td>
                </tr>`;
            }).join('');
        }

        function statusBadge(status) {
            if (status === 'active')     return '<span class="mgr-badge-ok">Ativo</span>';
            if (status === 'blocked')    return '<span class="mgr-badge-bad">Bloqueado</span>';
            if (status === 'delinquent') return '<span class="mgr-badge-neutral" style="background:rgba(251,191,36,.12);color:#fbbf24;border-color:rgba(251,191,36,.25);">devendo</span>';
            return '<span class="mgr-badge-neutral">—</span>';
        }

        function paymentBadge(ps) {
            if (ps === 'paid')    return '<span class="mgr-badge-ok">Em dia</span>';
            if (ps === 'pending') return '<span class="mgr-badge-neutral" style="background:rgba(251,191,36,.12);color:#fbbf24;border-color:rgba(251,191,36,.25);">Pendente</span>';
            return '<span class="mgr-badge-neutral">—</span>';
        }

        function actionsHtml(s) {
            const isActive = s.status === 'active';
            let html = `<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">`;
            if (isActive) {
                html += `<button onclick="blockStudent(${s.id},'manual')" class="mgr-btn-sm" style="color:#f87171!important;border-color:rgba(214,21,50,.25)!important;background:rgba(214,21,50,.07)!important;">Bloquear</button>`;
            } else {
                html += `<button onclick="unblockStudent(${s.id})" class="mgr-btn-sm mgr-btn-edit-workout">Ativar</button>`;
            }
            html += `<button onclick="openStatusModal(${s.id},'${escAttr(s.name)}')" class="mgr-btn-sm">Alterar Status</button></div>`;
            return html;
        }

        function filterAccess(type, btn) {
            currentFilter = type;
            document.querySelectorAll('.mgr-tab').forEach(t => t.classList.remove('is-active'));
            if (btn) btn.classList.add('is-active');
            applyFilters();
        }

        function searchAccess() { applyFilters(); }

        function applyFilters() {
            const query = (document.getElementById('accessSearch')?.value ?? '').toLowerCase().trim();
            let filtered = allStudents;
            if (currentFilter !== 'all') filtered = filtered.filter(s => s.status === currentFilter);
            if (query) filtered = filtered.filter(s => s.name.toLowerCase().includes(query) || s.email.toLowerCase().includes(query));
            renderTable(filtered);
        }

        async function blockStudent(studentId, reason = 'manual') {
            const row = document.querySelector(`tr[data-id="${studentId}"]`);
            if (row) row.style.opacity = '.5';
            try {
                const res  = await fetch('{{ route("access.block") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ student_id: studentId, reason })
                });
                const json = await res.json();
                if (!res.ok) showToast(json.message ?? 'Erro ao bloquear.', 'error');
                else { showToast(json.message ?? 'Acesso bloqueado.'); updateStudentLocally(json.data); }
            } catch { showToast('Erro de conexão.', 'error'); }
            finally { if (row) row.style.opacity = '1'; }
        }

        async function unblockStudent(studentId) {
            const row = document.querySelector(`tr[data-id="${studentId}"]`);
            if (row) row.style.opacity = '.5';
            try {
                const res  = await fetch('{{ route("access.unblock") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ student_id: studentId })
                });
                const json = await res.json();
                if (!res.ok) showToast(json.message ?? 'Erro ao ativar.', 'error');
                else { showToast(json.message ?? 'Acesso ativado.'); updateStudentLocally(json.data); }
            } catch { showToast('Erro de conexão.', 'error'); }
            finally { if (row) row.style.opacity = '1'; }
        }

        let pendingStatusStudentId = null;

        function openStatusModal(studentId, name) {
            pendingStatusStudentId = studentId;
            document.getElementById('modal-student-name').textContent = name;
            const overlay = document.getElementById('status-modal-overlay');
            const box     = document.getElementById('status-modal-box');
            overlay.style.display = 'flex';
            requestAnimationFrame(() => {
                box.style.transform = 'translateY(0) scale(1)';
                box.style.opacity   = '1';
            });
        }

        function closeStatusModal() {
            const box = document.getElementById('status-modal-box');
            box.style.transform = 'translateY(16px) scale(0.97)';
            box.style.opacity   = '0';
            setTimeout(() => document.getElementById('status-modal-overlay').style.display = 'none', 250);
            pendingStatusStudentId = null;
        }

        async function confirmUpdateStatus(newStatus) {
            if (!pendingStatusStudentId) return;
            const studentId = pendingStatusStudentId;
            closeStatusModal();
            const row = document.querySelector(`tr[data-id="${studentId}"]`);
            if (row) row.style.opacity = '.5';
            try {
                const res  = await fetch('{{ route("access.status") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ student_id: studentId, status: newStatus })
                });
                const json = await res.json();
                if (!res.ok) showToast(json.message ?? 'Erro ao alterar status.', 'error');
                else { showToast(json.message ?? 'Status atualizado.'); updateStudentLocally(json.data); }
            } catch { showToast('Erro de conexão.', 'error'); }
            finally { if (row) row.style.opacity = '1'; }
        }

        function updateStudentLocally(data) {
            const idx = allStudents.findIndex(s => s.id === data.student_id);
            if (idx !== -1) {
                allStudents[idx].status       = data.status;
                allStudents[idx].is_defaulter = data.is_defaulter ?? (data.status !== 'active');
            }
            updateCounts();
            applyFilters();
        }

        function escHtml(str) {
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }
        function escAttr(str) { return String(str).replace(/'/g, "\\'"); }

        document.getElementById('status-modal-overlay').addEventListener('click', function(e) {
            if (e.target === this) closeStatusModal();
        });
         
        loadStudents();
    </script>
    @endif
</x-app-layout>