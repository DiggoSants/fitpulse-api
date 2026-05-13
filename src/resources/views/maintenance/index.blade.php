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

                {{-- Skeleton --}}
                <div id="requests-skeleton" style="display:flex; flex-direction:column; gap:10px;">
                    @for($i = 0; $i < 4; $i++)
                        <div class="sk" style="height:72px; border-radius:14px;"></div>
                    @endfor
                </div>

                {{-- Lista real --}}
                <div id="requests-list" style="display:none; flex-direction:column; gap:10px;"></div>

                {{-- Empty --}}
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
                    <h3 style="font-size:18px;" class="ev-section-title">Equipamentos Cadastrados</h3>

                    <div id="eq-skeleton" style="display:flex; flex-direction:column; gap:10px;">
                        @for($i = 0; $i < 4; $i++)
                            <div class="sk" style="height:56px; border-radius:12px;"></div>
                        @endfor
                    </div>

                    <div id="eq-list" style="display:none; flex-direction:column; gap:8px;"></div>

                    <div id="eq-empty" style="display:none; text-align:center; padding:28px; color:var(--text-muted); font-size:13px;">
                        Nenhum equipamento cadastrado.
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

                {{-- Alerta de equipamento já em manutenção --}}
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

    <script>
        // ── Estado global ─────────────────────────────────────────────────────
        const EP_RESOLVE = "{{ route('maintenance.resolve', ':id') }}";
        const CSRF     = document.querySelector('meta[name="csrf-token"]').content;
        const EP_MAINT = "{{ route('maintenance.index') }}";
        const EP_EQ    = "{{ route('equipment.index') }}";
        const EP_STORE = "{{ route('maintenance.store') }}";
        const EP_EQ_STORE = "{{ route('equipment.store') }}";

        let allRequests  = [];
        let allEquipment = [];
        let currentFilter = 'all';

        // ── Inicialização ─────────────────────────────────────────────────────
        async function init() {
            await Promise.all([loadMaintenance(), loadEquipment()]);
        }

        // ── Carregar manutenção (resumo + solicitações) ───────────────────────
        async function loadMaintenance() {
            try {
                const res  = await fetch(EP_MAINT, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const json = await res.json();

                // Resumo
                document.getElementById('summary-maintenance').textContent = json.summary?.total_in_maintenance ?? 0;
                document.getElementById('summary-open').textContent        = json.summary?.total_open ?? 0;
                document.getElementById('summary-resolved').textContent    = json.summary?.total_resolved ?? 0;

                allRequests = json.data ?? [];
                renderRequests();

            } catch (e) {
                console.error('Maint error:', e);
            }
        }

        // ── Carregar equipamentos ─────────────────────────────────────────────
        async function loadEquipment() {
            try {
                const res  = await fetch(EP_EQ, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const json = await res.json();
                allEquipment = json.data ?? [];
                renderEquipment();
                populateEquipmentSelect();
            } catch (e) {
                console.error('Equipment error:', e);
            }
        }

        // ── Renderizar solicitações ───────────────────────────────────────────
        function renderRequests() {
            document.getElementById('requests-skeleton').style.display = 'none';

            const filtered = currentFilter === 'all'
                ? allRequests
                : allRequests.filter(r => r.status === currentFilter);

            const list  = document.getElementById('requests-list');
            const empty = document.getElementById('requests-empty');

            list.innerHTML = '';

            if (!filtered.length) {
                list.style.display  = 'none';
                empty.style.display = 'block';
                return;
            }

            empty.style.display = 'none';
            list.style.display  = 'flex';

            filtered.forEach(req => {
                const isOpen = req.status === 'aberto';
                const card = document.createElement('div');
                card.className    = 'maint-request-row';
                card.dataset.status = req.status;

                card.innerHTML = `
                    <div style="display:flex; align-items:center; gap:14px; flex:1; min-width:0; flex-wrap:wrap;">
                        <div style="
                            width:38px; height:38px; border-radius:10px; flex-shrink:0;
                            display:flex; align-items:center; justify-content:center;
                            background:${isOpen ? 'rgba(251,191,36,0.12)' : 'rgba(74,222,128,0.10)'};
                            border:1px solid ${isOpen ? 'rgba(251,191,36,0.25)' : 'rgba(74,222,128,0.20)'};
                        ">
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
                            <div class="maint-eq-name" style="font-size:14px; font-weight:700; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
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
                                        class="btn-save"
                                        style="font-size:11px; padding:6px 14px; white-space:nowrap;">
                                        ✓ Marcar resolvido
                                   </button>`
                                : ''
                            }
                        </div>
                    </div>
                `;

                list.appendChild(card);
            });
        }

        // ── Renderizar equipamentos ───────────────────────────────────────────
        function renderEquipment() {
            document.getElementById('eq-skeleton').style.display = 'none';
            const list  = document.getElementById('eq-list');
            const empty = document.getElementById('eq-empty');

            list.innerHTML = '';

            if (!allEquipment.length) {
                list.style.display  = 'none';
                empty.style.display = 'block';
                return;
            }

            empty.style.display = 'none';
            list.style.display  = 'flex';

            allEquipment.forEach(eq => {
                const inMaint = eq.status === 'manutencao';
                const row = document.createElement('div');
                row.style.cssText = `
                    display:flex; align-items:center; justify-content:space-between;
                    padding:12px 16px; border-radius:12px; gap:12px; flex-wrap:wrap;
                    background:${inMaint ? 'rgba(251,191,36,0.06)' : 'rgba(255,255,255,0.03)'};
                    border:1px solid ${inMaint ? 'rgba(251,191,36,0.20)' : 'rgba(255,255,255,0.07)'};
                    transition: border-color .2s;
                `;

                row.innerHTML = `
                    <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:0;">
                        <div style="
                            width:8px; height:8px; border-radius:50%; flex-shrink:0;
                            background:${inMaint ? '#fbbf24' : '#4ade80'};
                            box-shadow: 0 0 0 3px ${inMaint ? 'rgba(251,191,36,0.20)' : 'rgba(74,222,128,0.18)'};
                        "></div>
                        <span class="eq-item-name" style="font-size:14px; font-weight:600;">
                            ${escHtml(eq.name)}
                        </span>
                    </div>
                    <span style="
                        font-size:10px; font-weight:800; letter-spacing:.07em; text-transform:uppercase;
                        padding:3px 10px; border-radius:99px; white-space:nowrap;
                        background:${inMaint ? 'rgba(251,191,36,0.12)' : 'rgba(74,222,128,0.10)'};
                        border:1px solid ${inMaint ? 'rgba(251,191,36,0.25)' : 'rgba(74,222,128,0.20)'};
                        color:${inMaint ? '#fbbf24' : '#4ade80'};
                    ">
                        ${inMaint ? '⚠ Em manutenção' : '● Ativo'}
                    </span>
                `;

                list.appendChild(row);
            });
        }

        // ── Popular select do modal ───────────────────────────────────────────
        function populateEquipmentSelect() {
            const sel = document.getElementById('report-equipment-select');
            sel.innerHTML = '<option value="">Selecione o equipamento...</option>';
            allEquipment.forEach(eq => {
                const opt = document.createElement('option');
                opt.value       = eq.id;
                opt.textContent = eq.name + (eq.status === 'manutencao' ? ' ⚠ (em manutenção)' : '');
                sel.appendChild(opt);
            });
        }

        // ── Filtro de solicitações ────────────────────────────────────────────
        function filterRequests(type, btn) {
            document.querySelectorAll('.mgr-filter').forEach(f => f.classList.remove('is-active'));
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

        // ── Modal Reportar ────────────────────────────────────────────────────
        function openReportModal() {
            document.getElementById('report-equipment-select').value = '';
            document.getElementById('report-description').value      = '';
            document.getElementById('report-already-alert').style.display = 'none';
            document.getElementById('report-modal-overlay').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeReportModal() {
            document.getElementById('report-modal-overlay').style.display = 'none';
            document.body.style.overflow = '';
        }

        // Detecta se equipamento já está em manutenção
        document.addEventListener('DOMContentLoaded', () => {
            const sel = document.getElementById('report-equipment-select');
            if (sel) {
                sel.addEventListener('change', function () {
                    const eq = allEquipment.find(e => e.id == this.value);
                    const alert = document.getElementById('report-already-alert');
                    alert.style.display = (eq && eq.status === 'manutencao') ? 'block' : 'none';
                });
            }
        });

        async function submitReport() {
            const equipmentId = document.getElementById('report-equipment-select').value;
            const description = document.getElementById('report-description').value.trim();

            if (!equipmentId || !description) {
                showToast('Preencha todos os campos.', 'error');
                return;
            }

            const btn = document.getElementById('report-submit-btn');
            btn.disabled    = true;
            btn.textContent = 'Registrando...';

            try {
                const res = await fetch(EP_STORE, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({ equipment_id: equipmentId, description }),
                });

                const data = await res.json();

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
                const res = await fetch(EP_RESOLVE.replace(':id', id), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({}),
                });

                const data = await res.json();

                if (res.ok) {
                    showToast('✓ ' + data.message, 'success');
                    await Promise.all([loadMaintenance(), loadEquipment()]);
                } else {
                    showToast(data.message || 'Erro ao resolver.', 'error');
                    btn.disabled = false;
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
            if (!name) {
                showToast('Informe o nome do equipamento.', 'error');
                return;
            }

            const btn = document.getElementById('eq-save-btn');
            btn.disabled    = true;
            btn.textContent = 'Salvando...';

            try {
                const res = await fetch(EP_EQ_STORE, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({ name }),
                });

                const data = await res.json();

                if (res.ok) {
                    document.getElementById('eq-name-input').value = '';
                    showToast('✓ ' + data.message, 'success');
                    await loadEquipment();
                    populateEquipmentSelect();
                } else {
                    showToast(data.errors?.name?.[0] || data.message || 'Erro ao cadastrar.', 'error');
                }
            } catch (e) {
                showToast('Erro de conexão. Tente novamente.', 'error');
            } finally {
                btn.disabled    = false;
                btn.textContent = 'Cadastrar';

                // reinsere o ícone
                btn.innerHTML = `
                    <svg width="11" height="11" viewBox="0 0 12 12" fill="none"
                         style="stroke:#fff; stroke-width:2.5; stroke-linecap:round;">
                        <line x1="6" y1="1" x2="6" y2="11"/>
                        <line x1="1" y1="6" x2="11" y2="6"/>
                    </svg>
                    Cadastrar
                `;
            }
        }

        // ── Toast ─────────────────────────────────────────────────────────────
        function showToast(msg, type) {
            const toast = document.getElementById('maint-toast');
            toast.textContent   = msg;
            toast.style.display = 'block';

            const isSuccess = type === 'success';
            toast.style.background = isSuccess ? 'rgba(74,222,128,0.08)'   : 'rgba(214,21,50,0.08)';
            toast.style.border     = `1px solid ${isSuccess ? 'rgba(74,222,128,0.20)' : 'rgba(214,21,50,0.22)'}`;
            toast.style.color      = isSuccess ? '#4ade80' : '#f87171';
            toast.style.opacity    = '1';
            toast.style.transform  = 'none';

            setTimeout(() => {
                toast.style.opacity   = '0';
                toast.style.transform = 'translateY(-6px)';
                setTimeout(() => { toast.style.display = 'none'; }, 300);
            }, 3500);
        }

        // ── Helper XSS ────────────────────────────────────────────────────────
        function escHtml(str) {
            const d = document.createElement('div');
            d.textContent = str ?? '';
            return d.innerHTML;
        }

        // ── Estilos das linhas de solicitação ─────────────────────────────────
        const style = document.createElement('style');
        style.textContent = `
            .maint-request-row {
                display: flex;
                align-items: center;
                padding: 14px 18px;
                border-radius: 14px;
                border: 1px solid rgba(255,255,255,0.07);
                background: rgba(255,255,255,0.03);
                transition: border-color .2s, background .2s;
                gap: 12px;
            }
            .maint-request-row:hover {
                border-color: rgba(255,255,255,0.13);
                background: rgba(255,255,255,0.05);
            }
            [data-theme="light"] .maint-request-row {
                background: #fff;
                border-color: rgba(0,0,0,0.07);
                box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            }
            [data-theme="light"] .maint-request-row:hover {
                border-color: rgba(0,0,0,0.14);
            }
        `;
        document.head.appendChild(style);

        // ── Inicializar ───────────────────────────────────────────────────────
        init();
    </script>
</x-app-layout>