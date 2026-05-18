<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="dash-hero" style="margin-bottom:20px;">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Recepção</div>
                        <h2 class="dash-hero__title">Matrículas</h2>
                        <p class="dash-hero__sub">Gerencie matrículas de alunos pendentes</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            RECEPCIONISTA
                        </span>
                    </div>
                </div>
            </div>

            <div class="rec-stats">
                <div class="rec-stat-card">
                    <span class="rec-stat-card__label">Pendentes</span>
                    <strong class="rec-stat-card__value" id="stat-pending">—</strong>
                    <span class="rec-stat-card__sub">sem matrícula ativa</span>
                </div>
                <div class="rec-stat-card rec-stat-card--blue">
                    <span class="rec-stat-card__label">Instrutores</span>
                    <strong class="rec-stat-card__value" id="stat-instructors">—</strong>
                    <span class="rec-stat-card__sub">disponíveis</span>
                </div>
            </div>

            <div id="rec-toast" class="rec-toast" style="display:none;"></div>

            <div class="rec-section">
                <div class="rec-section-head">
                    <p class="section-label" style="margin-bottom:0;">ALUNOS PENDENTES DE MATRÍCULA</p>
                    <input type="text" id="rec-search" class="mgr-search" placeholder="Buscar aluno..." oninput="filterPendingStudents()">
                </div>

                {{-- Skeleton --}}
                <div id="rec-skeleton" class="rec-table-wrap">
                    <table class="mgr-table">
                        <thead><tr><th>Nome</th><th>Email</th><th>Status</th><th>Ação</th></tr></thead>
                        <tbody>
                            @for($i = 0; $i < 4; $i++)
                                <tr>
                                    <td><div class="sk" style="height:14px; width:140px; border-radius:6px;"></div></td>
                                    <td><div class="sk" style="height:14px; width:180px; border-radius:6px;"></div></td>
                                    <td><div class="sk" style="height:22px; width:90px; border-radius:99px;"></div></td>
                                    <td><div class="sk" style="height:30px; width:100px; border-radius:99px;"></div></td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                {{-- Tabela real --}}
                <div id="rec-table-wrap" class="rec-table-wrap" style="display:none;">
                    <table class="mgr-table">
                        <thead><tr><th>Nome</th><th>Email</th><th>Status</th><th>Ação</th></tr></thead>
                        <tbody id="rec-tbody"></tbody>
                    </table>
                    <div id="rec-empty" class="rec-empty-state" style="display:none;">
                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 14px; display:block; opacity:.20;">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <p>Nenhum aluno pendente encontrado.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL DE MATRÍCULA --}}
    <div id="enroll-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center; padding:20px;">
        <div class="rec-modal">
            <div class="rec-modal__header">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div class="rec-modal__icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="stroke:#f87171; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <line x1="19" y1="8" x2="19" y2="14"/>
                            <line x1="22" y1="11" x2="16" y2="11"/>
                        </svg>
                    </div>
                 <div>
                        <p class="rec-modal__title">Nova Matrícula</p>
                        <p class="rec-modal__subtitle" id="modal-student-name">—</p>
                    </div>
                </div>
                <button type="button" class="shop-modal__close" onclick="closeEnrollModal()">✕</button>
            </div>

            <div class="rec-modal__body">
                <div class="rec-field">
                    <label class="rec-label">Aluno selecionado</label>
                    <div class="rec-student-display">
                        <div class="rec-student-display__avatar" id="modal-student-avatar"></div>
                        <div>
                            <p class="rec-student-display__name" id="modal-student-display-name"></p>
                            <p class="rec-student-display__email" id="modal-student-display-email"></p>
                        </div>
                    </div>
                </div>

                <div class="rec-field">
                    <label class="rec-label" for="select-plan">Plano</label>
                    <div class="rec-select-wrap">
                        <select id="select-plan" class="rec-select" onchange="updatePlanPreview()">
                            <option value="">Selecione um plano...</option>
                        </select>
                        <svg class="rec-select-arrow" width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 5l5 5 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div id="plan-preview" class="rec-plan-preview" style="display:none;">
                        <div class="rec-plan-preview__row">
                            <span class="rec-plan-preview__label">Preço</span>
                            <span class="rec-plan-preview__value" id="plan-preview-price">—</span>
                        </div>
                        <div class="rec-plan-preview__row">
                            <span class="rec-plan-preview__label">Duração</span>
                            <span class="rec-plan-preview__value" id="plan-preview-duration">—</span>
                        </div>
                    </div>
                </div>

                <div class="rec-field">
                    <label class="rec-label" for="input-instructor-code">Instrutor</label>
                    <div class="rec-instructor-list" id="instructor-list">
                        <p class="rec-instructor-list__empty">Carregando instrutores...</p>
                    </div>
                    <div class="rec-code-field">
                        <input type="text" id="input-instructor-code" class="rec-code-input" placeholder="Digite o código do instrutor" autocomplete="off" oninput="updateInstructorPreview()">
                        <span class="rec-code-field__hint">Código</span>
                    </div>
                    <p id="instructor-code-error" class="rec-field-error" style="display:none;">Código de instrutor inválido.</p>
                    <div id="instructor-preview" class="rec-instructor-preview" style="display:none;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div class="rec-instructor-preview__avatar" id="inst-preview-avatar"></div>
                            <div>
                                <p class="rec-instructor-preview__name" id="inst-preview-name"></p>
                                <p class="rec-instructor-preview__specialty" id="inst-preview-specialty"></p>
                            </div>
                        </div>
                        <div class="rec-invite-code-wrap">
                            <span class="rec-invite-code-label">Código de convite</span>
                            <span class="rec-invite-code" id="inst-preview-code">—</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rec-modal__footer">
                <button type="button" class="shop-modal__btn-cancel" onclick="closeEnrollModal()">Cancelar</button>
                <button type="button" id="btn-confirm-enroll" class="rec-btn-confirm" onclick="confirmEnroll()">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:currentColor; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round;">
                        <polyline points="2,7 6,11 12,3"/>
                    </svg>
                    Confirmar Matrícula
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL DE SUCESSO --}}
    <div id="success-modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); backdrop-filter:blur(4px); z-index:9999; align-items:center; justify-content:center; padding:20px;">
        <div class="rec-modal" style="max-width:420px;">
            <div style="padding:32px 28px; text-align:center;">
                <div class="rec-success-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" style="stroke:#4ade80; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round;">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <h3 style="font-family:'Bebas Neue',sans-serif; font-size:28px; letter-spacing:2px; color:var(--text-white); margin:16px 0 6px;">Matrícula Realizada!</h3>
                <p style="font-size:13px; color:var(--text-muted); margin:0 0 20px;" id="success-msg"></p>
                <div class="rec-success-details" id="success-details"></div>
                <button type="button" class="rec-btn-confirm" style="width:100%; margin-top:20px; justify-content:center;" onclick="closeSuccessModal()">Fechar</button>
            </div>
        </div>
    </div>

    <script>
        const CSRF            = document.querySelector('meta[name="csrf-token"]').content;
        const URL_PENDING     = "{{ route('reception.pending.data', [], false) }}";
        const URL_INSTRUCTORS = "{{ route('reception.instructors', [], false) }}";
        const URL_ENROLL      = "{{ route('reception.enroll', [], false) }}";
        const URL_PLANS       = "{{ route('reception.plans', [], false) }}";  {{-- ✅ rota dedicada --}}

        let allStudents    = [];
        let allInstructors = [];
        let allPlans       = [];
        let selectedStudentId = null;
        let selectedInstructorId = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadPendingStudents();
            loadInstructors();
            loadPlans();
        });

        async function loadPendingStudents() {
            try {
                const res  = await fetch(URL_PENDING, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) throw new Error('Falha ao carregar alunos pendentes.');
                const json = await res.json();
                allStudents = json.data ?? [];
                document.getElementById('stat-pending').textContent    = allStudents.length;
                document.getElementById('rec-skeleton').style.display  = 'none';
                document.getElementById('rec-table-wrap').style.display = 'block';
                renderStudentsTable(allStudents);
            } catch (e) {
                document.getElementById('rec-skeleton').style.display  = 'none';
                document.getElementById('rec-table-wrap').style.display = 'block';
                document.getElementById('stat-pending').textContent = '0';
                renderStudentsTable([]);
                showToast('Não foi possível carregar os alunos pendentes agora.', 'error');
            }
        }

        async function loadInstructors() {
            try {
                const res  = await fetch(URL_INSTRUCTORS, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const json = await res.json();
                allInstructors = json.data ?? [];
                document.getElementById('stat-instructors').textContent = allInstructors.length;
                renderInstructorsList();
            } catch (e) {
                console.error('Erro ao carregar instrutores:', e);
            }
        }

        function renderInstructorsList() {
            const list = document.getElementById('instructor-list');
            list.innerHTML = '';
            if (!allInstructors.length) {
                list.innerHTML = '<p class="rec-instructor-list__empty">Nenhum instrutor disponível.</p>';
                return;
            }

            allInstructors.forEach(inst => {
                const item = document.createElement('div');
                item.className = 'rec-instructor-list__item';
                item.innerHTML = `
                    <span class="rec-instructor-list__name">${escHtml(inst.name)}</span>
                    <span class="rec-instructor-list__code">${escHtml(inst.invite_code ?? '—')}</span>`;
                list.appendChild(item);
            });
        }

        async function loadPlans() {
            try {
                {{-- ✅ Usa rota própria — sem middleware enrolled --}}
                const res  = await fetch(URL_PLANS, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const json = await res.json();
                allPlans   = json.data ?? [];
                const sel  = document.getElementById('select-plan');
                allPlans.forEach(plan => {
                    const opt = document.createElement('option');
                    opt.value = plan.id;
                    opt.textContent = `${plan.name} — R$ ${parseFloat(plan.price).toFixed(2).replace('.', ',')} / ${plan.duration_days} dias`;
                    sel.appendChild(opt);
                });
            } catch (e) {
                console.error('Erro ao carregar planos:', e);
            }
        }

        function renderStudentsTable(students) {
            const tbody = document.getElementById('rec-tbody');
            const empty = document.getElementById('rec-empty');
            tbody.innerHTML = '';
            if (!students.length) { empty.style.display = 'block'; return; }
            empty.style.display = 'none';
            students.forEach(s => {
                const initials    = s.name.substring(0, 2).toUpperCase();
                const statusLabel = s.status === 'inadimplente' ? 'Inadimplente' : 'Sem matrícula';
                const statusClass = s.status === 'inadimplente' ? 'mgr-badge-bad' : 'mgr-badge-neutral';
                const tr = document.createElement('tr');
                tr.className      = 'rec-student-row';
                tr.dataset.name   = s.name.toLowerCase();
                tr.dataset.email  = s.email.toLowerCase();
                tr.innerHTML = `
                    <td>
                        <div class="mgr-student-cell">
                            <div class="mgr-student-cell__avatar">${initials}</div>
                            <div class="mgr-student-cell__content">
                                <span class="mgr-student-cell__name">${escHtml(s.name)}</span>
                            </div>
                        </div>
                    </td>
                    <td><span class="mgr-student-cell__email">${escHtml(s.email)}</span></td>
                    <td><span class="${statusClass}">${statusLabel}</span></td>
                    <td>
                        <button type="button" class="rec-btn-matricular"
                            onclick="openEnrollModal(${s.id}, '${escHtml(s.name)}', '${escHtml(s.email)}')">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" style="stroke:currentColor; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round;">
                                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                            Matricular
                        </button>
                    </td>`;
                tbody.appendChild(tr);
            });
        }

        function filterPendingStudents() {
            const query = document.getElementById('rec-search').value.toLowerCase().trim();
            let visible = 0;
            document.querySelectorAll('.rec-student-row').forEach(row => {
                const match = row.dataset.name.includes(query) || row.dataset.email.includes(query);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            document.getElementById('rec-empty').style.display = visible === 0 ? 'block' : 'none';
        }

        function openEnrollModal(id, name, email) {
            selectedStudentId = id;
            document.getElementById('modal-student-name').textContent         = name;
            document.getElementById('modal-student-display-name').textContent = name;
            document.getElementById('modal-student-display-email').textContent = email;
            document.getElementById('modal-student-avatar').textContent       = name.substring(0, 2).toUpperCase();
            document.getElementById('select-plan').value       = '';
            document.getElementById('input-instructor-code').value = '';
            document.getElementById('input-instructor-code').classList.remove('rec-code-input--error');
            document.getElementById('instructor-code-error').style.display = 'none';
            document.getElementById('plan-preview').style.display       = 'none';
            document.getElementById('instructor-preview').style.display = 'none';
            selectedInstructorId = null;
            document.getElementById('enroll-modal-overlay').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeEnrollModal() {
            document.getElementById('enroll-modal-overlay').style.display = 'none';
            document.body.style.overflow = '';
            selectedStudentId = null;
            selectedInstructorId = null;
        }

        function updatePlanPreview() {
            const planId = parseInt(document.getElementById('select-plan').value);
            const plan   = allPlans.find(p => p.id === planId);
            const prev   = document.getElementById('plan-preview');
            if (!plan) { prev.style.display = 'none'; return; }
            document.getElementById('plan-preview-price').textContent    = `R$ ${parseFloat(plan.price).toFixed(2).replace('.', ',')}`;
            document.getElementById('plan-preview-duration').textContent = `${plan.duration_days} dias`;
            prev.style.display = 'flex';
        }

        function updateInstructorPreview() {
            const codeInput = document.getElementById('input-instructor-code');
            const error = document.getElementById('instructor-code-error');
            const typedCode = codeInput.value.trim().toUpperCase();
            const inst = allInstructors.find(i => (i.invite_code ?? '').toUpperCase() === typedCode);
            const prev = document.getElementById('instructor-preview');

            selectedInstructorId = inst ? inst.id : null;
            error.style.display = typedCode && !inst ? 'block' : 'none';
            codeInput.classList.toggle('rec-code-input--error', Boolean(typedCode && !inst));

            if (!inst) { prev.style.display = 'none'; return; }
            document.getElementById('inst-preview-avatar').textContent    = inst.name.substring(0, 2).toUpperCase();
            document.getElementById('inst-preview-name').textContent      = inst.name;
            document.getElementById('inst-preview-specialty').textContent = inst.specialty ?? 'Personal Trainer';
            document.getElementById('inst-preview-code').textContent      = inst.invite_code ?? '—';
            prev.style.display = 'block';
        }

        async function confirmEnroll() {
            const planId = document.getElementById('select-plan').value;
            const instId = selectedInstructorId;
            if (!selectedStudentId || !planId || !instId) {
                showToast('Selecione aluno, plano e digite um código de instrutor válido para continuar.', 'error');
                return;
            }
            const btn = document.getElementById('btn-confirm-enroll');
            btn.disabled = true;
            btn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" style="stroke:currentColor;stroke-width:2.5;stroke-linecap:round;" class="rec-spin"><circle cx="12" cy="12" r="10" stroke-dasharray="31.4" stroke-dashoffset="10"/></svg> Processando...`;
            try {
                const res  = await fetch(URL_ENROLL, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({ student_id: selectedStudentId, plan_id: planId, instructor_id: instId }),
                });
                const data = await res.json();
                if (res.ok) {
                    closeEnrollModal();
                    showSuccessModal(data);
                    allStudents = allStudents.filter(s => s.id !== selectedStudentId);
                    renderStudentsTable(allStudents);
                    document.getElementById('stat-pending').textContent = allStudents.length;
                } else {
                    showToast(data.message ?? 'Erro ao realizar matrícula.', 'error');
                }
            } catch (e) {
                showToast('Erro de conexão. Tente novamente.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `<svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:currentColor;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round;"><polyline points="2,7 6,11 12,3"/></svg> Confirmar Matrícula`;
            }
        }

        function showSuccessModal(data) {
            const d = data.data ?? {};
            document.getElementById('success-msg').textContent = `${d.student ?? 'Aluno'} foi matriculado com sucesso!`;
            document.getElementById('success-details').innerHTML = `
                <div class="rec-success-detail-row"><span class="rec-success-detail-label">Plano</span><span class="rec-success-detail-value">${escHtml(d.plan ?? '—')}</span></div>
                <div class="rec-success-detail-row"><span class="rec-success-detail-label">Instrutor</span><span class="rec-success-detail-value">${escHtml(d.instructor ?? '—')}</span></div>
                <div class="rec-success-detail-row"><span class="rec-success-detail-label">Início</span><span class="rec-success-detail-value">${escHtml(d.start_date ?? '—')}</span></div>
                <div class="rec-success-detail-row"><span class="rec-success-detail-label">Vencimento</span><span class="rec-success-detail-value">${escHtml(d.end_date ?? '—')}</span></div>
                <div class="rec-success-detail-row"><span class="rec-success-detail-label">Recepcionista</span><span class="rec-success-detail-value">${escHtml(d.receptionist ?? '—')}</span></div>`;
            document.getElementById('success-modal-overlay').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeSuccessModal() {
            document.getElementById('success-modal-overlay').style.display = 'none';
            document.body.style.overflow = '';
        }

        function showToast(msg, type) {
            const toast = document.getElementById('rec-toast');
            toast.textContent   = msg;
            toast.style.display = 'flex';
            toast.className     = 'rec-toast' + (type === 'error' ? ' rec-toast--error' : '');
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => { toast.style.display = 'none'; toast.style.opacity = '1'; }, 300);
            }, 4000);
        }

        function escHtml(str) {
            const d = document.createElement('div');
            d.textContent = str ?? '';
            return d.innerHTML;
        }
    </script>
</x-app-layout>
