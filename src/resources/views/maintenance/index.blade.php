<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ── HERO ── --}}
            <div class="dash-hero" style="margin-bottom:1.25rem;">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Gerenciamento</div>
                        <h2 class="dash-hero__title">Manutenção</h2>
                        <p class="dash-hero__sub">Equipamentos e solicitações de reparo</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            GERENTE
                        </span>
                        <button
                            type="button"
                            class="btn-save"
                            style="font-size:12px; padding:9px 18px; display:inline-flex; align-items:center; gap:7px;"
                            onclick="openReportModal()"
                        >
                            <svg width="11" height="11" viewBox="0 0 12 12" fill="none"
                                 style="stroke:#fff; stroke-width:2.5; stroke-linecap:round;">
                                <line x1="6" y1="1" x2="6" y2="11"/>
                                <line x1="1" y1="6" x2="11" y2="6"/>
                            </svg>
                            Reportar Problema
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── TOAST ── --}}
            <div id="maint-toast" style="display:none; margin-bottom:16px; padding:12px 16px; border-radius:10px; font-size:13px; font-weight:600; transition: opacity .3s, transform .3s;"></div>

            {{-- ── CARDS RESUMO ── --}}
            <div id="maint-summary" class="mgr-stats" style="margin-bottom:1.25rem;">
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Em manutenção</span>
                    <strong class="mgr-stat-card__value" id="summary-maintenance">—</strong>
                    <span class="mgr-stat-card__sub">equipamentos</span>
                </div>
                <div class="mgr-stat-card mgr-stat-card--green">
                    <span class="mgr-stat-card__label">Solicitações abertas</span>
                    <strong class="mgr-stat-card__value" id="summary-open">—</strong>
                    <span class="mgr-stat-card__sub">aguardando resolução</span>
                </div>
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Resolvidas</span>
                    <strong class="mgr-stat-card__value" id="summary-resolved">—</strong>
                    <span class="mgr-stat-card__sub">total</span>
                </div>
            </div>

            {{-- ── ABAS ── --}}
            <div class="mgr-tabs" style="margin-bottom:1.25rem;">
                <button type="button" class="mgr-tab is-active" onclick="showMaintSection('requests-section', this)">
                    Solicitações
                </button>
                <button type="button" class="mgr-tab" onclick="showMaintSection('equipment-section', this)">
                    Equipamentos
                </button>
            </div>

            {{-- ══════════════════════════════════════════════════════════════
                 SEÇÃO: SOLICITAÇÕES
            ══════════════════════════════════════════════════════════════ --}}
            <div id="requests-section" class="mgr-section">
                <div class="mgr-section-head" style="margin-bottom:16px;">
                    <p class="section-label" style="margin:0;">SOLICITAÇÕES DE MANUTENÇÃO</p>
                    <div class="mgr-filters">
                        <button type="button" class="mgr-filter is-active" onclick="filterRequests('all', this)">Todas</button>
                        <button type="button" class="mgr-filter" onclick="filterRequests('aberto', this)">Abertas</button>
                        <button type="button" class="mgr-filter" onclick="filterRequests('resolvido', this)">Resolvidas</button>
                    </div>
                </div>

                <div id="requests-skeleton" style="display:flex; flex-direction:column; gap:10px;">
                    @for($i = 0; $i < 4; $i++)
                        <div class="sk" style="height:72px; border-radius:14px;"></div>
                    @endfor
                </div>

                <div id="requests-list" style="display:none; flex-direction:column; gap:10px;"></div>

                <div id="requests-empty" style="display:none; text-align:center; padding:48px 20px; color:var(--text-muted);">
                    <svg width="44" height="44" viewBox="0 0 24 24" fill="none"
                         style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 14px; display:block; opacity:.20;">
                        <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="1"/>
                    </svg>
                    <p style="font-size:14px;">Nenhuma solicitação encontrada.</p>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════════
                 SEÇÃO: EQUIPAMENTOS
            ══════════════════════════════════════════════════════════════ --}}
            <div id="equipment-section" class="mgr-section" style="display:none;">

                {{-- Formulário cadastrar equipamento --}}
                <div style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:24px; margin-bottom:20px;">
                    <h3 style="font-size:18px;" class="ev-section-title">Cadastrar Equipamento</h3>
                    <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">
                        <div style="flex:1; min-width:200px; display:grid; gap:6px;">
                            <label class="ev-label">Nome do equipamento</label>
                            <input
                                type="text"
                                id="eq-name-input"
                                class="ev-input"
                                placeholder="Ex: Esteira, Leg Press..."
                                maxlength="255"
                                onkeydown="if(event.key==='Enter') saveEquipment()"
                            >
                        </div>
                        <button
                            type="button"
                            id="eq-save-btn"
                            class="btn-save"
                            style="padding:12px 22px; font-size:13px; display:inline-flex; align-items:center; gap:7px; white-space:nowrap;"
                            onclick="saveEquipment()"
                        >
                            <svg width="11" height="11" viewBox="0 0 12 12" fill="none"
                                 style="stroke:#fff; stroke-width:2.5; stroke-linecap:round;">
                                <line x1="6" y1="1" x2="6" y2="11"/>
                                <line x1="1" y1="6" x2="11" y2="6"/>
                            </svg>
                            Cadastrar
                        </button>
                    </div>
                </div>

                {{-- Lista de equipamentos --}}
                <div style="background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08); border-radius:20px; padding:24px;">

                    {{-- Cabeçalho com busca e filtros --}}
                    <div style="display:flex; flex-direction:column; gap:14px; margin-bottom:20px;">
                        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                            <h3 style="font-size:18px; margin:0;" class="ev-section-title">Equipamentos Cadastrados</h3>
                            <span id="eq-count-label" style="font-size:12px; color:var(--text-muted);"></span>
                        </div>

                        {{-- Busca por nome ou código --}}
                        <div style="position:relative;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                 style="position:absolute; left:13px; top:50%; transform:translateY(-50%); stroke:var(--text-muted); stroke-width:2; stroke-linecap:round; pointer-events:none;">
                                <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                            </svg>
                            <input
                                type="text"
                                id="eq-search"
                                class="ev-input"
                                placeholder="Buscar por nome ou código..."
                                style="padding-left:36px; width:100%; box-sizing:border-box;"
                                oninput="searchEquipment()"
                            >
                        </div>

                        {{-- Filtros de status --}}
                        <div style="display:flex; gap:6px; flex-wrap:wrap;">
                            <button type="button" class="mgr-filter is-active" onclick="filterEquipment('all', this)">Todos</button>
                            <button type="button" class="mgr-filter" onclick="filterEquipment('ativo', this)">Ativos</button>
                            <button type="button" class="mgr-filter" onclick="filterEquipment('manutencao', this)">Em manutenção</button>
                            <button type="button" class="mgr-filter" onclick="filterEquipment('inativo', this)">Inativos</button>
                        </div>
                    </div>

                    {{-- Skeleton --}}
                    <div id="eq-skeleton" style="display:flex; flex-direction:column; gap:10px;">
                        @for($i = 0; $i < 4; $i++)
                            <div class="sk" style="height:56px; border-radius:12px;"></div>
                        @endfor
                    </div>

                    {{-- Lista --}}
                    <div id="eq-list" style="display:none; flex-direction:column; gap:8px;"></div>

                    {{-- Empty state --}}
                    <div id="eq-empty" style="display:none; text-align:center; padding:36px 20px; color:var(--text-muted);">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none"
                             style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 14px; display:block; opacity:.20;">
                            <rect x="2" y="10" width="3" height="4" rx="1"/>
                            <rect x="19" y="10" width="3" height="4" rx="1"/>
                            <rect x="5" y="8" width="3" height="8" rx="1"/>
                            <rect x="16" y="8" width="3" height="8" rx="1"/>
                            <rect x="8" y="11" width="8" height="2" rx="1"/>
                        </svg>
                        <p id="eq-empty-text" style="font-size:14px;">Nenhum equipamento cadastrado.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         MODAL: REPORTAR PROBLEMA
    ══════════════════════════════════════════════════════════════ --}}
    <div id="report-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center; padding:20px;">
        <div style="background:#161616; border:1px solid rgba(255,255,255,0.10); border-radius:20px; width:100%; max-width:440px; box-shadow:0 24px 60px rgba(0,0,0,0.50); animation:shopModalIn .22s ease;">

            <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 22px 16px; border-bottom:1px solid rgba(255,255,255,0.07);">
                <p style="font-size:15px; font-weight:800; color:#f5f5f5; margin:0;">⚙️ Reportar Problema</p>
                <button type="button" class="shop-modal__close" onclick="closeReportModal()">✕</button>
            </div>

            <div style="padding:20px 22px; display:flex; flex-direction:column; gap:16px;">
                <div style="display:grid; gap:6px;">
                    <label class="ev-label">Equipamento</label>
                    <select id="report-equipment-select" class="ev-input" style="appearance:none; cursor:pointer;">
                        <option value="">Selecione o equipamento...</option>
                    </select>
                </div>

                <div style="display:grid; gap:6px;">
                    <label class="ev-label">Descrição do problema</label>
                    <textarea
                        id="report-description"
                        class="ev-input ev-textarea"
                        rows="3"
                        placeholder="Descreva o problema encontrado..."
                        style="resize:vertical;"
                    ></textarea>
                </div>

                <div id="report-already-alert" style="display:none; padding:10px 14px; border-radius:10px; background:rgba(251,191,36,0.10); border:1px solid rgba(251,191,36,0.25); color:#fbbf24; font-size:12px; font-weight:600;">
                    ⚠️ Este equipamento já possui uma solicitação aberta.
                </div>
            </div>

            <div style="display:flex; gap:10px; padding:0 22px 20px;">
                <button type="button" class="shop-modal__btn-cancel" onclick="closeReportModal()">Cancelar</button>
                <button type="button" id="report-submit-btn" class="shop-modal__btn-confirm" onclick="submitReport()">
                    Registrar problema
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         MODAL: EDITAR EQUIPAMENTO
    ══════════════════════════════════════════════════════════════ --}}
    <div id="eq-edit-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center; padding:20px;">
        <div style="background:#161616; border:1px solid rgba(255,255,255,0.10); border-radius:20px; width:100%; max-width:440px; box-shadow:0 24px 60px rgba(0,0,0,0.50); animation:shopModalIn .22s ease; overflow:hidden;">

            <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 22px 16px; border-bottom:1px solid rgba(255,255,255,0.07);">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:34px; height:34px; border-radius:10px; background:rgba(59,130,246,0.12); border:1px solid rgba(59,130,246,0.25); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="16" height="16" viewBox="0 0 14 14" fill="none"
                             style="stroke:#93c5fd; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;">
                            <path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/>
                        </svg>
                    </div>
                    <p style="font-size:14px; font-weight:800; color:#f5f5f5; margin:0;">Editar Equipamento</p>
                </div>
                <button type="button" class="shop-modal__close" onclick="closeEditModal()">✕</button>
            </div>

            <div style="padding:20px 22px; display:flex; flex-direction:column; gap:16px;">

                {{-- Código (somente leitura) --}}
                <div style="display:grid; gap:6px;">
                    <label class="ev-label">Código de identificação</label>
                    <div id="eq-edit-code" style="
                        padding:10px 14px; border-radius:10px; font-size:13px; font-weight:700;
                        background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08);
                        color:var(--text-muted); letter-spacing:.04em; font-family: 'Courier New', monospace;
                    ">—</div>
                </div>

                {{-- Nome --}}
                <div style="display:grid; gap:6px;">
                    <label class="ev-label">Nome do equipamento</label>
                    <input
                        type="text"
                        id="eq-edit-name"
                        class="ev-input"
                        placeholder="Nome do equipamento"
                        maxlength="255"
                    >
                </div>

                {{-- Status --}}
                <div style="display:grid; gap:6px;">
                    <label class="ev-label">Status</label>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <label class="eq-status-radio" data-value="ativo">
                            <input type="radio" name="eq-edit-status" value="ativo" style="display:none;">
                            <span class="eq-status-pill eq-status-pill--ativo">● Ativo</span>
                        </label>
                        <label class="eq-status-radio" data-value="manutencao">
                            <input type="radio" name="eq-edit-status" value="manutencao" style="display:none;">
                            <span class="eq-status-pill eq-status-pill--manutencao">⚠ Em manutenção</span>
                        </label>
                        <label class="eq-status-radio" data-value="inativo">
                            <input type="radio" name="eq-edit-status" value="inativo" style="display:none;">
                            <span class="eq-status-pill eq-status-pill--inativo">○ Inativo</span>
                        </label>
                    </div>
                    <p id="eq-edit-status-hint" style="font-size:11px; color:var(--text-muted); margin:4px 0 0; display:none;"></p>
                </div>

            </div>

            <div style="display:flex; gap:10px; padding:0 22px 20px;">
                <button type="button" class="shop-modal__btn-cancel" onclick="closeEditModal()">Cancelar</button>
                <button type="button" id="eq-edit-confirm-btn" class="shop-modal__btn-confirm" onclick="confirmEdit()">
                    Salvar alterações
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         MODAL: CONFIRMAR INATIVAÇÃO
    ══════════════════════════════════════════════════════════════ --}}
    <div id="eq-inactivate-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center; padding:20px;">
        <div style="background:#161616; border:1px solid rgba(255,255,255,0.10); border-radius:20px; width:100%; max-width:380px; box-shadow:0 24px 60px rgba(0,0,0,0.50); animation:shopModalIn .22s ease; overflow:hidden;">

            <div style="padding:24px 24px 0;">
                <div style="width:44px; height:44px; border-radius:12px; background:rgba(214,21,50,0.12); border:1px solid rgba(214,21,50,0.25); display:flex; align-items:center; justify-content:center; margin-bottom:14px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                         style="stroke:#f87171; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;">
                        <circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/>
                    </svg>
                </div>
                <p style="font-size:16px; font-weight:800; color:#f5f5f5; margin:0 0 6px;">Inativar equipamento?</p>
                <p id="eq-inactivate-name" style="font-size:13px; color:rgba(255,255,255,.55); margin:0 0 10px;"></p>
                <p style="font-size:13px; color:rgba(255,255,255,.55); margin:0 0 22px; line-height:1.5;">
                    O equipamento não aparecerá mais como disponível para os alunos. O histórico de manutenção é mantido.
                </p>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end; padding:0 24px 22px;">
                <button type="button" class="btn-ghost" onclick="closeInactivateModal()">Cancelar</button>
                <button type="button" class="btn-del" id="eq-inactivate-confirm-btn" onclick="confirmInactivation()">Inativar</button>
            </div>
        </div>
    </div>

    <style>
        /* ── Status pills no modal de edição ─────────────────────────── */
        .eq-status-radio { cursor:pointer; }
        .eq-status-pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .05em;
            border: 1px solid rgba(255,255,255,0.12);
            color: var(--text-muted);
            background: rgba(255,255,255,0.04);
            transition: all .15s;
            user-select: none;
        }
        .eq-status-radio input:checked + .eq-status-pill.eq-status-pill--ativo {
            background: rgba(74,222,128,0.14);
            border-color: rgba(74,222,128,0.35);
            color: #4ade80;
        }
        .eq-status-radio input:checked + .eq-status-pill.eq-status-pill--manutencao {
            background: rgba(251,191,36,0.14);
            border-color: rgba(251,191,36,0.35);
            color: #fbbf24;
        }
        .eq-status-radio input:checked + .eq-status-pill.eq-status-pill--inativo {
            background: rgba(248,113,113,0.12);
            border-color: rgba(248,113,113,0.30);
            color: #f87171;
        }
        .eq-status-radio:hover .eq-status-pill { border-color: rgba(255,255,255,0.25); }

        /* ── Linhas da tabela de equipamentos ────────────────────────── */
        .eq-row {
            display: flex;
            align-items: center;
            padding: 12px 14px;
            border-radius: 12px;
            gap: 12px;
            transition: border-color .15s, background .15s;
        }
        .eq-row:hover { filter: brightness(1.08); }
        .eq-row--ativo      { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); }
        .eq-row--manutencao { background: rgba(251,191,36,0.06);  border: 1px solid rgba(251,191,36,0.20); }
        .eq-row--inativo    { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); opacity: .65; }

        /* ── Botões de ação inline ────────────────────────────────────── */
        .eq-btn-edit {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; border-radius: 8px; font-size: 11px; font-weight: 700;
            border: 1px solid rgba(147,197,253,0.25); color: #93c5fd;
            background: rgba(59,130,246,0.08);
            cursor: pointer; font-family: inherit; white-space: nowrap;
            transition: background .15s, border-color .15s;
        }
        .eq-btn-edit:hover { background: rgba(59,130,246,0.14); border-color: rgba(147,197,253,0.40); }

        .eq-btn-inactivate {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; border-radius: 8px; font-size: 11px; font-weight: 700;
            border: 1px solid rgba(248,113,113,0.25); color: #f87171;
            background: rgba(214,21,50,0.06);
            cursor: pointer; font-family: inherit; white-space: nowrap;
            transition: background .15s, border-color .15s;
        }
        .eq-btn-inactivate:hover { background: rgba(214,21,50,0.12); border-color: rgba(248,113,113,0.40); }

        .eq-btn-restore {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 12px; border-radius: 8px; font-size: 11px; font-weight: 700;
            border: 1px solid rgba(74,222,128,0.25); color: #4ade80;
            background: rgba(74,222,128,0.06);
            cursor: pointer; font-family: inherit; white-space: nowrap;
            transition: background .15s, border-color .15s;
        }
        .eq-btn-restore:hover { background: rgba(74,222,128,0.12); border-color: rgba(74,222,128,0.40); }

        /* ── Código de identificação na lista ────────────────────────── */
        .eq-code {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .05em;
            color: var(--text-muted);
            padding: 2px 7px;
            border-radius: 5px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* ── Solicitações ─────────────────────────────────────────────── */
        .maint-request-row {
            display: flex; align-items: center;
            padding: 14px 18px; border-radius: 14px;
            border: 1px solid rgba(255,255,255,0.07);
            background: rgba(255,255,255,0.03);
            transition: border-color .2s, background .2s;
            gap: 12px;
        }
        .maint-request-row:hover { border-color: rgba(255,255,255,0.13); background: rgba(255,255,255,0.05); }

        /* ── Mobile ───────────────────────────────────────────────────── */
        @media (max-width: 640px) {
            .eq-row { flex-wrap: wrap; }
            .eq-row__actions { width: 100%; justify-content: flex-end; margin-top: 4px; }
        }
    </style>

    <script>
        // ── Endpoints ─────────────────────────────────────────────────────────
        const ROUTE_ID    = '__ID__';
        const EP_RESOLVE  = "{{ route('maintenance.resolve', ['id' => '__ID__'], false) }}";
        const EP_EQ_UPD   = "{{ route('equipment.update',   ['equipment' => '__ID__'], false) }}";
        const CSRF        = document.querySelector('meta[name="csrf-token"]').content;
        const EP_MAINT    = "{{ route('maintenance.index', [], false) }}";
        const EP_EQ       = "{{ route('equipment.index',   [], false) }}";
        const EP_STORE    = "{{ route('maintenance.store',  [], false) }}";
        const EP_EQ_STORE = "{{ route('equipment.store',   [], false) }}";

        // ── Estado ────────────────────────────────────────────────────────────
        let allRequests     = [];
        let allEquipment    = [];
        let currentFilter   = 'all';
        let eqStatusFilter  = 'all';
        let equipmentSearch = '';
        let editingEqId     = null;
        let inactivatingEqId = null;

        // ── Helpers ───────────────────────────────────────────────────────────
        async function readJsonResponse(res) {
            try { return await res.json(); }
            catch (e) {
                if (res.status === 419) return { message: 'Sua sessão expirou. Atualize a página.' };
                if (res.status === 401 || res.status === 403) return { message: 'Sem permissão para esta ação.' };
                return { message: 'Resposta inesperada do servidor.' };
            }
        }

        function routeWithId(template, id) {
            return template.replace(ROUTE_ID, encodeURIComponent(id));
        }

        function escHtml(str) {
            const d = document.createElement('div');
            d.textContent = str ?? '';
            return d.innerHTML;
        }

        function normalizeSearch(value) {
            return String(value ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
        }

        function sortEquipment(items) {
            const order = { manutencao: 0, ativo: 1, inativo: 2 };
            return [...items].sort((a, b) => {
                const diff = (order[a.status] ?? 3) - (order[b.status] ?? 3);
                if (diff !== 0) return diff;
                return String(a.name ?? '').localeCompare(String(b.name ?? ''), 'pt-BR', { sensitivity: 'base', numeric: true });
            });
        }

        // ── Init ──────────────────────────────────────────────────────────────
        async function init() {
            await Promise.all([loadMaintenance(), loadEquipment()]);
        }

        // ── Carregar manutenção ───────────────────────────────────────────────
        async function loadMaintenance() {
            try {
                const res  = await fetch(EP_MAINT, { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) throw new Error();
                const json = await res.json();
                document.getElementById('summary-maintenance').textContent = json.summary?.total_in_maintenance ?? 0;
                document.getElementById('summary-open').textContent        = json.summary?.total_open ?? 0;
                document.getElementById('summary-resolved').textContent    = json.summary?.total_resolved ?? 0;
                allRequests = json.data ?? [];
                renderRequests();
            } catch (e) {
                document.getElementById('requests-skeleton').style.display = 'none';
                document.getElementById('requests-empty').style.display    = 'block';
                ['summary-maintenance','summary-open','summary-resolved'].forEach(id => {
                    document.getElementById(id).textContent = '0';
                });
                showToast('Não foi possível carregar as solicitações.', 'error');
            }
        }

        // ── Carregar equipamentos ─────────────────────────────────────────────
        async function loadEquipment() {
            try {
                const res  = await fetch(EP_EQ, { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) throw new Error();
                const json = await res.json();
                allEquipment = sortEquipment(json.data ?? json ?? []);
                renderEquipment();
                populateEquipmentSelect();
            } catch (e) {
                allEquipment = [];
                document.getElementById('eq-skeleton').style.display = 'none';
                document.getElementById('eq-empty').style.display    = 'block';
                populateEquipmentSelect();
                showToast('Não foi possível carregar os equipamentos.', 'error');
            }
        }

        // ── Renderizar solicitações ───────────────────────────────────────────
        function renderRequests() {
            document.getElementById('requests-skeleton').style.display = 'none';
            const filtered = currentFilter === 'all' ? allRequests : allRequests.filter(r => r.status === currentFilter);
            const list  = document.getElementById('requests-list');
            const empty = document.getElementById('requests-empty');
            list.innerHTML = '';

            if (!filtered.length) { list.style.display = 'none'; empty.style.display = 'block'; return; }
            empty.style.display = 'none';
            list.style.display  = 'flex';

            filtered.forEach(req => {
                const isOpen = req.status === 'aberto';
                const card   = document.createElement('div');
                card.className        = 'maint-request-row';
                card.dataset.status   = req.status;
                card.innerHTML = `
                    <div style="display:flex; align-items:center; gap:14px; flex:1; min-width:0; flex-wrap:wrap;">
                        <div style="width:38px; height:38px; border-radius:10px; flex-shrink:0; display:flex; align-items:center; justify-content:center;
                            background:${isOpen ? 'rgba(251,191,36,0.12)' : 'rgba(74,222,128,0.10)'};
                            border:1px solid ${isOpen ? 'rgba(251,191,36,0.25)' : 'rgba(74,222,128,0.20)'};">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                 style="stroke:${isOpen ? '#fbbf24' : '#4ade80'}; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;">
                                ${isOpen
                                    ? '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>'
                                    : '<polyline points="20 6 9 17 4 12"/>'
                                }
                            </svg>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:14px; font-weight:700; color:var(--text-white); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                ${escHtml(req.equipment)}
                            </div>
                            <div style="font-size:12px; color:var(--text-muted); margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                ${escHtml(req.description)}
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:10px; flex-shrink:0; flex-wrap:wrap;">
                            <span style="font-size:11px; color:var(--text-muted);">${req.created_at}</span>
                            ${isOpen
                                ? `<span class="mgr-badge-bad" style="background:rgba(251,191,36,0.12); border-color:rgba(251,191,36,0.25); color:#fbbf24;">Aberto</span>`
                                : `<span class="mgr-badge-ok">Resolvido</span>`
                            }
                            ${isOpen
                                ? `<button type="button" onclick="resolveRequest(${req.id}, this)"
                                        class="btn-save" style="font-size:11px; padding:6px 14px; white-space:nowrap;">
                                        ✓ Marcar resolvido
                                   </button>`
                                : ''
                            }
                        </div>
                    </div>`;
                list.appendChild(card);
            });
        }

        // ── Renderizar equipamentos ───────────────────────────────────────────
        function renderEquipment() {
            document.getElementById('eq-skeleton').style.display = 'none';
            const list  = document.getElementById('eq-list');
            const empty = document.getElementById('eq-empty');
            const query = normalizeSearch(equipmentSearch);

            // Aplica filtro de status e busca
            const filtered = allEquipment.filter(eq => {
                const statusOk = eqStatusFilter === 'all' || eq.status === eqStatusFilter;
                const searchOk = !query
                    || normalizeSearch(eq.name).includes(query)
                    || normalizeSearch(eq.unique_code ?? '').includes(query);
                return statusOk && searchOk;
            });

            list.innerHTML = '';

            // Atualiza contador
            const countLabel = document.getElementById('eq-count-label');
            if (countLabel) {
                const total = allEquipment.length;
                countLabel.textContent = filtered.length < total
                    ? `${filtered.length} de ${total} equipamento${total !== 1 ? 's' : ''}`
                    : `${total} equipamento${total !== 1 ? 's' : ''}`;
            }

            if (!allEquipment.length) {
                list.style.display  = 'none';
                empty.style.display = 'block';
                document.getElementById('eq-empty-text').textContent = 'Nenhum equipamento cadastrado.';
                return;
            }

            if (!filtered.length) {
                list.style.display  = 'none';
                empty.style.display = 'block';
                document.getElementById('eq-empty-text').textContent = 'Nenhum equipamento encontrado para os filtros aplicados.';
                return;
            }

            empty.style.display = 'none';
            list.style.display  = 'flex';

            filtered.forEach(eq => {
                const inMaint   = eq.status === 'manutencao';
                const isInativo = eq.status === 'inativo';

                // Cores e labels por status
                const dotColor   = inMaint ? '#fbbf24' : isInativo ? 'rgba(255,255,255,0.25)' : '#4ade80';
                const dotShadow  = inMaint ? 'rgba(251,191,36,0.20)' : isInativo ? 'transparent' : 'rgba(74,222,128,0.18)';
                const badgeBg    = inMaint ? 'rgba(251,191,36,0.12)'  : isInativo ? 'rgba(255,255,255,0.05)'  : 'rgba(74,222,128,0.10)';
                const badgeBord  = inMaint ? 'rgba(251,191,36,0.25)'  : isInativo ? 'rgba(255,255,255,0.10)'  : 'rgba(74,222,128,0.20)';
                const badgeColor = inMaint ? '#fbbf24'                 : isInativo ? 'rgba(255,255,255,0.35)'  : '#4ade80';
                const badgeText  = inMaint ? '⚠ Em manutenção'        : isInativo ? '○ Inativo'               : '● Ativo';

                const row = document.createElement('div');
                row.className = `eq-row eq-row--${eq.status}`;

                row.innerHTML = `
                    <div style="width:8px; height:8px; border-radius:50%; flex-shrink:0;
                                background:${dotColor}; box-shadow: 0 0 0 3px ${dotShadow};"></div>

                    <div style="flex:1; min-width:0; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                        <span style="font-size:14px; font-weight:600; color:var(--text-white);
                                     white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            ${escHtml(eq.name)}
                        </span>
                        ${eq.unique_code
                            ? `<span class="eq-code">${escHtml(eq.unique_code)}</span>`
                            : ''
                        }
                    </div>

                    <div class="eq-row__actions" style="display:flex; align-items:center; gap:8px; flex-shrink:0; flex-wrap:wrap;">
                        <span style="font-size:10px; font-weight:800; letter-spacing:.07em; text-transform:uppercase;
                                     padding:3px 10px; border-radius:99px; white-space:nowrap;
                                     background:${badgeBg}; border:1px solid ${badgeBord}; color:${badgeColor};">
                            ${badgeText}
                        </span>

                        <button type="button" class="eq-btn-edit" onclick="openEditModal(${eq.id})">
                            <svg width="11" height="11" viewBox="0 0 14 14" fill="none"
                                 style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;">
                                <path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/>
                            </svg>
                            Editar
                        </button>

                        ${!isInativo
                            ? `<button type="button" class="eq-btn-inactivate" onclick="openInactivateModal(${eq.id}, '${escHtml(eq.name).replace(/'/g, "\\'")}')">
                                   <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                                        style="stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;">
                                       <circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/>
                                   </svg>
                                   Inativar
                               </button>`
                            : `<button type="button" class="eq-btn-restore" onclick="restoreEquipment(${eq.id})">
                                   <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                                        style="stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;">
                                       <path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 1 0 .49-4.95"/>
                                   </svg>
                                   Restaurar
                               </button>`
                        }
                    </div>
                `;
                list.appendChild(row);
            });
        }

        // ── Filtros de equipamentos ───────────────────────────────────────────
        function filterEquipment(status, btn) {
            document.querySelectorAll('#equipment-section .mgr-filter').forEach(f => f.classList.remove('is-active'));
            if (btn) btn.classList.add('is-active');
            eqStatusFilter = status;
            renderEquipment();
        }

        function searchEquipment() {
            equipmentSearch = document.getElementById('eq-search')?.value ?? '';
            renderEquipment();
        }

        // ── Popular select do modal de reportar ───────────────────────────────
        function populateEquipmentSelect() {
            const sel = document.getElementById('report-equipment-select');
            sel.innerHTML = '<option value="">Selecione o equipamento...</option>';
            // Só mostra ativos e em manutenção (inativos não podem receber chamado)
            sortEquipment(allEquipment)
                .filter(eq => eq.status !== 'inativo')
                .forEach(eq => {
                    const opt = document.createElement('option');
                    opt.value       = eq.id;
                    opt.textContent = eq.name + (eq.status === 'manutencao' ? ' ⚠ (em manutenção)' : '');
                    sel.appendChild(opt);
                });
        }

        // ── Modal de edição ───────────────────────────────────────────────────
        function openEditModal(id) {
            const eq = allEquipment.find(e => e.id === id);
            if (!eq) return;
            editingEqId = id;

            document.getElementById('eq-edit-code').textContent = eq.unique_code ?? '—';
            document.getElementById('eq-edit-name').value       = eq.name ?? '';

            // Marca o radio correto
            document.querySelectorAll('input[name="eq-edit-status"]').forEach(radio => {
                radio.checked = radio.value === eq.status;
            });

            updateStatusHint(eq.status);

            document.getElementById('eq-edit-modal-overlay').style.display = 'flex';
            document.body.style.overflow = 'hidden';
            setTimeout(() => document.getElementById('eq-edit-name').focus(), 100);
        }

        function closeEditModal() {
            document.getElementById('eq-edit-modal-overlay').style.display = 'none';
            document.body.style.overflow = '';
            editingEqId = null;
        }

        function updateStatusHint(status) {
            const hint = document.getElementById('eq-edit-status-hint');
            const hints = {
                ativo:      '',
                manutencao: 'Equipamento marcado como em manutenção ficará indisponível para uso.',
                inativo:    'Equipamento inativo não aparecerá como disponível para os alunos.',
            };
            const text = hints[status] ?? '';
            hint.textContent   = text;
            hint.style.display = text ? 'block' : 'none';
        }

        // Atualiza hint ao mudar radio
        document.querySelectorAll('input[name="eq-edit-status"]').forEach(radio => {
            radio.addEventListener('change', () => updateStatusHint(radio.value));
        });

        async function confirmEdit() {
            const name   = document.getElementById('eq-edit-name').value.trim();
            const status = document.querySelector('input[name="eq-edit-status"]:checked')?.value;

            if (!name)   { showToast('Informe o nome do equipamento.', 'error'); return; }
            if (!status) { showToast('Selecione um status.', 'error'); return; }

            const btn = document.getElementById('eq-edit-confirm-btn');
            btn.disabled    = true;
            btn.textContent = 'Salvando...';

            try {
                const url = routeWithId(EP_EQ_UPD, editingEqId);
                const res = await fetch(url, {
                    method: 'PUT',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type':     'application/json',
                        'Accept':           'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     CSRF,
                    },
                    body: JSON.stringify({ name, status }),
                });

                const data = await readJsonResponse(res);

                if (res.ok) {
                    closeEditModal();
                    showToast('✓ Equipamento atualizado.', 'success');
                    await loadEquipment();
                } else {
                    showToast(data.errors?.name?.[0] || data.message || 'Erro ao atualizar.', 'error');
                }
            } catch (e) {
                showToast('Erro de conexão. Tente novamente.', 'error');
            } finally {
                btn.disabled    = false;
                btn.textContent = 'Salvar alterações';
            }
        }

        // ── Modal de inativação ───────────────────────────────────────────────
        function openInactivateModal(id, name) {
            inactivatingEqId = id;
            document.getElementById('eq-inactivate-name').textContent = name;
            document.getElementById('eq-inactivate-modal-overlay').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeInactivateModal() {
            document.getElementById('eq-inactivate-modal-overlay').style.display = 'none';
            document.body.style.overflow = '';
            inactivatingEqId = null;
        }

        async function confirmInactivation() {
            const btn = document.getElementById('eq-inactivate-confirm-btn');
            btn.disabled    = true;
            btn.textContent = 'Inativando...';

            try {
                const url = routeWithId(EP_EQ_UPD, inactivatingEqId);
                const res = await fetch(url, {
                    method: 'PUT',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type':     'application/json',
                        'Accept':           'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     CSRF,
                    },
                    body: JSON.stringify({ status: 'inativo' }),
                });

                const data = await readJsonResponse(res);

                if (res.ok) {
                    closeInactivateModal();
                    showToast('✓ Equipamento inativado.', 'success');
                    await loadEquipment();
                } else {
                    showToast(data.message || 'Erro ao inativar.', 'error');
                }
            } catch (e) {
                showToast('Erro de conexão. Tente novamente.', 'error');
            } finally {
                btn.disabled    = false;
                btn.textContent = 'Inativar';
            }
        }

        // ── Restaurar equipamento inativo ─────────────────────────────────────
        async function restoreEquipment(id) {
            try {
                const url = routeWithId(EP_EQ_UPD, id);
                const res = await fetch(url, {
                    method: 'PUT',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type':     'application/json',
                        'Accept':           'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':     CSRF,
                    },
                    body: JSON.stringify({ status: 'ativo' }),
                });

                const data = await readJsonResponse(res);

                if (res.ok) {
                    showToast('✓ Equipamento restaurado.', 'success');
                    await loadEquipment();
                } else {
                    showToast(data.message || 'Erro ao restaurar.', 'error');
                }
            } catch (e) {
                showToast('Erro de conexão. Tente novamente.', 'error');
            }
        }

        // ── Filtro de solicitações ────────────────────────────────────────────
        function filterRequests(type, btn) {
            document.querySelectorAll('#requests-section .mgr-filter').forEach(f => f.classList.remove('is-active'));
            if (btn) btn.classList.add('is-active');
            currentFilter = type;
            renderRequests();
        }

        // ── Abas ──────────────────────────────────────────────────────────────
        function showMaintSection(id, btn) {
            document.querySelectorAll('.mgr-section').forEach(s => s.style.display = 'none');
            const target = document.getElementById(id);
            if (target) target.style.display = 'block';
            document.querySelectorAll('.mgr-tab').forEach(t => t.classList.remove('is-active'));
            if (btn) btn.classList.add('is-active');
        }

        // ── Modal: Reportar ───────────────────────────────────────────────────
        function openReportModal() {
            document.getElementById('report-equipment-select').value      = '';
            document.getElementById('report-description').value           = '';
            document.getElementById('report-already-alert').style.display = 'none';
            document.getElementById('report-modal-overlay').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeReportModal() {
            document.getElementById('report-modal-overlay').style.display = 'none';
            document.body.style.overflow = '';
        }

        document.getElementById('report-equipment-select').addEventListener('change', function () {
            const eq    = allEquipment.find(e => e.id == this.value);
            const alert = document.getElementById('report-already-alert');
            alert.style.display = (eq && eq.status === 'manutencao') ? 'block' : 'none';
        });

        async function submitReport() {
            const equipmentId = document.getElementById('report-equipment-select').value;
            const description = document.getElementById('report-description').value.trim();

            if (!equipmentId || !description) { showToast('Preencha todos os campos.', 'error'); return; }

            const btn = document.getElementById('report-submit-btn');
            btn.disabled    = true;
            btn.textContent = 'Registrando...';

            try {
                const res  = await fetch(EP_STORE, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ equipment_id: equipmentId, description }),
                });
                const data = await readJsonResponse(res);
                if (res.ok) {
                    closeReportModal();
                    showToast('✓ ' + data.message, 'success');
                    await Promise.all([loadMaintenance(), loadEquipment()]);
                } else {
                    showToast(data.message || 'Erro ao registrar.', 'error');
                }
            } catch (e) {
                showToast('Erro de conexão. Tente novamente.', 'error');
            } finally {
                btn.disabled    = false;
                btn.textContent = 'Registrar problema';
            }
        }

        // ── Resolver solicitação ──────────────────────────────────────────────
        async function resolveRequest(id, btn) {
            const original  = btn.innerHTML;
            btn.disabled    = true;
            btn.textContent = 'Resolvendo...';

            try {
                const res  = await fetch(routeWithId(EP_RESOLVE, id), {
                    method: 'PUT',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({}),
                });
                const data = await readJsonResponse(res);
                if (res.ok) {
                    showToast('✓ ' + data.message, 'success');
                    await Promise.all([loadMaintenance(), loadEquipment()]);
                } else {
                    showToast(data.message || 'Erro ao resolver.', 'error');
                    btn.disabled  = false;
                    btn.innerHTML = original;
                }
            } catch (e) {
                showToast('Erro de conexão. Tente novamente.', 'error');
                btn.disabled  = false;
                btn.innerHTML = original;
            }
        }

        // ── Cadastrar equipamento ─────────────────────────────────────────────
        async function saveEquipment() {
            const name = document.getElementById('eq-name-input').value.trim();
            if (!name) { showToast('Informe o nome do equipamento.', 'error'); return; }

            const btn = document.getElementById('eq-save-btn');
            btn.disabled    = true;
            btn.textContent = 'Salvando...';

            try {
                const res  = await fetch(EP_EQ_STORE, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ name }),
                });
                const data = await readJsonResponse(res);
                if (res.ok) {
                    document.getElementById('eq-name-input').value = '';
                    showToast('✓ ' + data.message, 'success');
                    await loadEquipment();
                } else {
                    showToast(data.errors?.name?.[0] || data.message || 'Erro ao cadastrar.', 'error');
                }
            } catch (e) {
                showToast('Erro de conexão. Tente novamente.', 'error');
            } finally {
                btn.disabled  = false;
                btn.innerHTML = `
                    <svg width="11" height="11" viewBox="0 0 12 12" fill="none"
                         style="stroke:#fff; stroke-width:2.5; stroke-linecap:round;">
                        <line x1="6" y1="1" x2="6" y2="11"/>
                        <line x1="1" y1="6" x2="11" y2="6"/>
                    </svg>
                    Cadastrar`;
            }
        }

        // ── Toast ─────────────────────────────────────────────────────────────
        function showToast(msg, type) {
            const toast = document.getElementById('maint-toast');
            toast.textContent       = msg;
            toast.style.display     = 'block';
            toast.style.opacity     = '1';
            toast.style.transform   = 'none';
            const ok = type === 'success';
            toast.style.background  = ok ? 'rgba(74,222,128,0.08)'  : 'rgba(214,21,50,0.08)';
            toast.style.border      = `1px solid ${ok ? 'rgba(74,222,128,0.20)' : 'rgba(214,21,50,0.22)'}`;
            toast.style.color       = ok ? '#4ade80' : '#f87171';
            setTimeout(() => {
                toast.style.opacity   = '0';
                toast.style.transform = 'translateY(-6px)';
                setTimeout(() => { toast.style.display = 'none'; }, 300);
            }, 3500);
        }

        // ── Fechar modais com Escape ──────────────────────────────────────────
        document.addEventListener('keydown', e => {
            if (e.key !== 'Escape') return;
            if (document.getElementById('eq-edit-modal-overlay').style.display       !== 'none') { closeEditModal();       return; }
            if (document.getElementById('eq-inactivate-modal-overlay').style.display !== 'none') { closeInactivateModal(); return; }
            if (document.getElementById('report-modal-overlay').style.display        !== 'none') { closeReportModal();     return; }
        });

        // ── Start ─────────────────────────────────────────────────────────────
        init();
    </script>
</x-app-layout>
