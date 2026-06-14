<x-app-layout>
    <div class="profile-wrapper">

        {{-- ── HEADER ── --}}
        <div class="profile-page-header">
            <div class="profile-page-header__left">
                <span class="profile-page-header__kicker">Conta</span>
                <h1 class="profile-page-header__title">Perfil</h1>
                <p class="profile-page-header__sub">Gerencie suas informações pessoais e segurança</p>
            </div>

            <div class="profile-page-header__user">
                <div class="profile-page-header__avatar">
                    {{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="profile-page-header__user-info">
                    <span class="profile-page-header__user-name">{{ Auth::user()->name }}</span>
                    <span class="profile-page-header__user-email">{{ Auth::user()->email }}</span>
                </div>
            </div>
        </div>

        {{-- ── INFORMAÇÕES DO PERFIL ── --}}
        <div class="profile-card">
            <div class="profile-card__title">Informações do perfil</div>
            <div class="profile-card__desc">Atualize seu nome e endereço de e-mail.</div>

            <form method="post" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="profile-field">
                    <label for="name">Nome</label>
                    <input id="name" type="text" name="name"
                           value="{{ old('name', $user->name) }}"
                           required autofocus autocomplete="name"
                           placeholder="Seu nome"
                           data-name-validation>
                    @error('name')
                        <p class="profile-field-error">{{ $message }}</p>
                    @enderror
                    <p class="profile-field-error" data-name-validation-message style="display:none;"></p>
                </div>

                <div class="profile-field">
                    <label for="email">E-mail</label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email', $user->email) }}"
                           required autocomplete="username"
                           placeholder="seu@email.com">
                    @error('email')
                        <p class="profile-field-error">{{ $message }}</p>
                    @enderror
                </div>

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div style="margin-bottom:16px;">
                        <p style="font-size:13px; color:var(--text-muted);">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification" style="background:none; border:none; color:#c1121f; cursor:pointer; font-size:13px; padding:0;">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>
                        @if (session('status') === 'verification-link-sent')
                            <p style="font-size:13px; color:var(--green-light); margin-top:6px;">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif

                <div class="profile-form-row">
                    <button type="submit" class="btn-save">Salvar</button>
                    @if (session('status') === 'profile-updated')
                        <p class="profile-saved">Salvo!</p>
                    @endif
                </div>
            </form>
        </div>

        {{-- ── OBJETIVO DO ALUNO ── --}}
        @php $student = $user->student; @endphp
        @if($student)
        <div class="profile-card">
            <div class="profile-card__title">Objetivo na Academia</div>
            <div class="profile-card__desc">Nos ajude a entender melhor seus objetivos de fitness.</div>

            <form method="post" action="{{ route('profile.update') }}">
                @csrf
                @method('patch')

                <div class="profile-field">
                    <label for="goal">Qual é seu objetivo?</label>
                    <select id="goal-select-profile" name="goal">
                        <option value="">Nenhum objetivo definido</option>
                        @php
                            $goalOptions = \App\Models\Student::getGoalOptions();
                            $currentGoal = old('goal', $student->goal);
                        @endphp
                        @foreach($goalOptions as $key => $label)
                            <option value="{{ $key }}" {{ $currentGoal === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('goal')
                        <p class="profile-field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="profile-field" id="custom-goal-field-profile" style="display: none;">
                    <label for="custom_goal_profile">Descreva seu objetivo</label>
                    <textarea
                        id="custom_goal_profile"
                        name="custom_goal"
                        class="profile-textarea"
                        placeholder="Ex: Melhorar meu condicionamento físico para correr uma meia maratona"
                        rows="4">{{ old('custom_goal', $student->custom_goal) }}</textarea>
                    @error('custom_goal')
                        <p class="profile-field-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-save">Atualizar objetivo</button>
            </form>
        </div>

        {{-- ── INSTRUTOR ATUAL ── --}}
        <div class="profile-card" id="instructor-card">
            <div class="profile-card__title">Meu Instrutor</div>
            <div class="profile-card__desc">Seu instrutor é atribuído conforme sua agenda de treino.</div>

            @if($student->instructor)
                <div class="instructor-current">
                    <div class="instructor-current__avatar">
                        {{ mb_strtoupper(mb_substr($student->instructor->user?->name ?? 'I', 0, 2)) }}
                    </div>
                    <div class="instructor-current__info">
                        <span class="instructor-current__name">{{ $student->instructor->user?->name }}</span>
                        @if($student->instructor->specialty)
                            <span class="instructor-current__specialty">{{ $student->instructor->specialty }}</span>
                        @endif
                    </div>
                    <button
                        type="button"
                        class="btn-cancel instructor-change-trigger"
                        id="open-instructor-modal"
                        style="margin-left:auto; white-space:nowrap;"
                    >
                        Trocar instrutor
                    </button>
                </div>

                @if(session('success') && str_contains(session('success'), 'Instrutor alterado'))
                    <p class="profile-saved" style="margin-top:10px;">{{ session('success') }}</p>
                @endif
            @else
                <div class="enrollment-empty" style="margin:0; padding:16px 0;">
                    Nenhum instrutor vinculado. Conclua sua matrícula para ser atribuído a um instrutor.
                </div>
            @endif
        </div>

        {{-- ── MODAL DE TROCA DE INSTRUTOR ── --}}
        {{-- Usa classe própria `instructor-modal-overlay` + `.is-open` para evitar conflito com `.plan-modal-overlay` do CSS global --}}
        <div id="instructor-modal-overlay" class="instructor-modal-overlay">
            <div class="plan-modal" style="max-width:500px; width:100%;">

                <button type="button" class="plan-modal__close" id="close-instructor-modal">×</button>

                <div class="plan-modal__top">
                    <p class="plan-modal__kicker">Troca de Instrutor</p>
                    <p class="plan-modal__name">Escolha um instrutor disponível</p>
                </div>

                <div class="plan-modal__body">

                    {{-- Estado: carregando --}}
                    <div id="instructor-modal-loading" style="text-align:center; padding:24px 0; color:var(--text-muted); font-size:13px;">
                        Buscando instrutores disponíveis…
                    </div>

                    {{-- Estado: erro / aviso --}}
                    <div id="instructor-modal-message" class="enrollment-errors" style="display:none;"></div>

                    {{-- Lista de instrutores --}}
                    <div id="instructor-modal-list" style="display:none;">
                        <p style="font-size:12px; color:var(--text-muted); margin-bottom:12px;">
                            Apenas instrutores com disponibilidade em todos os seus dias e turnos de treino são listados.
                        </p>
                        <ul id="instructor-options-list"></ul>
                    </div>

                    {{-- Motivo (opcional) --}}
                    <div id="instructor-modal-reason" style="display:none; margin-top:14px;">
                        <div class="profile-field" style="margin-bottom:0;">
                            <label for="instructor-change-reason" style="font-size:12px;">Motivo da troca <span style="color:var(--text-muted)">(opcional)</span></label>
                            <textarea
                                id="instructor-change-reason"
                                class="profile-textarea"
                                placeholder="Ex: Mudança de horário de treino"
                                rows="2"
                                style="font-size:13px; resize:none;"></textarea>
                        </div>
                    </div>

                    {{-- Feedback de erro inline (após seleção) --}}
                    <div id="instructor-change-error" style="display:none; margin-top:10px; font-size:13px; color:#c1121f;"></div>

                    {{-- Ações --}}
                    <div id="instructor-modal-actions" class="plan-modal__footer" style="display:none; margin-top:16px;">
                        <button type="button" class="btn-save" id="confirm-instructor-change" disabled>
                            Confirmar troca
                        </button>
                        <button type="button" class="btn-cancel" id="cancel-instructor-modal">
                            Cancelar
                        </button>
                    </div>

                </div>
            </div>
        </div>

        @endif {{-- end @if($student) --}}

        {{-- ── ALTERAR SENHA ── --}}
        <div class="profile-card">
            <div class="profile-card__title">Alterar senha</div>
            <div class="profile-card__desc">Use uma senha longa e aleatória para manter sua conta segura.</div>

            <form method="post" action="{{ route('password.update') }}">
                @csrf
                @method('put')

                <div class="profile-field">
                    <label for="current_password">Senha atual</label>
                    <input id="current_password" type="password" name="current_password"
                           autocomplete="current-password" placeholder="••••••••">
                    @error('current_password', 'updatePassword')
                        <p class="profile-field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="password">Nova senha</label>
                    <input id="password" type="password" name="password"
                           autocomplete="new-password" placeholder="••••••••">
                    @error('password', 'updatePassword')
                        <p class="profile-field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="password_confirmation">Confirmar nova senha</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           autocomplete="new-password" placeholder="••••••••">
                    @error('password_confirmation', 'updatePassword')
                        <p class="profile-field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="profile-form-row">
                    <button type="submit" class="btn-save">Atualizar senha</button>
                    @if (session('status') === 'password-updated')
                        <p class="profile-saved">Atualizado!</p>
                    @endif
                </div>
            </form>
        </div>

        {{-- ── DELETAR CONTA ── --}}
        <div class="profile-card profile-card--danger" x-data="{ confirming: false }">
            <div class="profile-card__title profile-card__title--danger">Deletar conta</div>
            <div class="profile-card__desc">Uma vez deletada, todos os dados serão permanentemente removidos.</div>

            <button class="btn-delete" @click="confirming = true">Deletar conta</button>

            <div x-show="confirming"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="delete-modal-overlay">

                <div x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="delete-modal-box">

                    <div class="delete-modal-header">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                        <span class="delete-modal-title">Deletar conta</span>
                        <span class="delete-modal-tag">— irreversível</span>
                    </div>

                    <p class="delete-modal-desc">
                        Todos os dados serão permanentemente removidos. Digite sua senha para confirmar.
                    </p>

                    <form method="post" action="{{ route('profile.destroy') }}" x-data="{ pwd: '' }">
                        @csrf
                        @method('delete')

                        <div class="profile-field" style="margin-bottom:12px;">
                            <input id="delete_password" type="password" name="password"
                                   x-model="pwd"
                                   placeholder="Sua senha"
                                   autofocus
                                   class="delete-modal-input">
                            @error('password', 'userDeletion')
                                <p class="profile-field-error" style="margin-top:6px;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="delete-modal-actions">
                            <button type="button" class="btn-cancel"
                                    style="font-size:12px; padding:7px 14px;"
                                    @click="confirming = false; pwd = ''">
                                Cancelar
                            </button>
                            <button type="submit" class="btn-delete"
                                    style="font-size:12px; padding:7px 14px;"
                                    :disabled="pwd.length === 0"
                                    :style="pwd.length === 0 ? 'opacity:0.4; cursor:not-allowed;' : ''">
                                Confirmar exclusão
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- ── ESTILOS DO INSTRUTOR ── --}}
    <style>
        /* ── Overlay do modal de instrutor ── */
        /* Usa opacity+pointer-events (igual ao .plan-modal-overlay do enrollment.css)
           para não conflitar com o display:flex que o CSS global aplica em .plan-modal-overlay */
        .instructor-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 1000;
            background: rgba(0, 0, 0, .72);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .22s ease;
        }
        .instructor-modal-overlay.is-open {
            opacity: 1;
            pointer-events: auto;
        }
        /* Anima o modal interno igual ao .plan-modal original */
        .instructor-modal-overlay .plan-modal {
            transform: translateY(16px) scale(0.97);
            opacity: 0;
            transition: transform .25s cubic-bezier(.22,.68,0,1.2), opacity .22s ease;
        }
        .instructor-modal-overlay.is-open .plan-modal {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        /* ── Card do instrutor atual ── */
        .instructor-current {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 0 4px;
        }

        /* Avatar: cor fixa igual ao avatar do header (não usa variável que muda no light mode) */
        .instructor-current__avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #c1121f;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            letter-spacing: .5px;
        }

        .instructor-current__info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        /* Nome: cor explícita herdada do profile-card para funcionar em ambos os temas */
        .instructor-current__name {
            font-size: 14px;
            font-weight: 600;
            color: inherit;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .instructor-current__specialty {
            font-size: 12px;
            color: var(--text-muted, #888);
        }

        /* ── Lista de opções no modal ── */
        #instructor-options-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .instructor-option {
            border: 1.5px solid var(--border, #333);
            border-radius: 10px;
            padding: 12px 14px;
            cursor: pointer;
            transition: border-color .15s, background .15s;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .instructor-option:hover {
            border-color: #c1121f;
            background: rgba(193, 18, 31, .06);
        }

        .instructor-option.is-selected {
            border-color: #c1121f;
            background: rgba(193, 18, 31, .08);
        }

        .instructor-option__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .instructor-option__name {
            font-size: 14px;
            font-weight: 600;
            color: inherit;
        }

        .instructor-option__specialty {
            font-size: 11px;
            color: var(--text-muted, #888);
        }

        .instructor-option__count {
            font-size: 11px;
            color: var(--text-muted, #888);
            white-space: nowrap;
        }

        .instructor-option__slots {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .instructor-slot-badge {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 20px;
            background: rgba(193, 18, 31, .10);
            color: #c1121f;
            white-space: nowrap;
            font-weight: 500;
        }
    </style>
</x-app-layout>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Validação de nome ──────────────────────────────────────────────────
    const nameInput   = document.querySelector('[data-name-validation]');
    const nameMessage = document.querySelector('[data-name-validation-message]');
    const nameForm    = nameInput?.closest('form');

    function nameError(value) {
        const trimmed = value.trim();
        if (!trimmed) return 'Informe o nome do aluno.';
        if (value.includes('@')) return 'O nome não pode conter @. Use apenas letras, espaços e acentos.';
        if (!/^[A-Za-zÀ-ÿ\s]+$/u.test(value)) return 'Nome inválido. Use apenas letras, espaços e acentos.';
        if ((value.match(/[A-Za-zÀ-ÿ]/gu) || []).length < 2) return 'O nome deve conter pelo menos 2 letras.';
        if (/\s{2,}/.test(value)) return 'O nome não pode ter espaços duplos.';
        return '';
    }

    function validateName() {
        if (!nameInput) return true;
        const error = nameError(nameInput.value);
        nameInput.setCustomValidity(error);
        if (nameMessage) {
            nameMessage.textContent = error;
            nameMessage.style.display = error ? 'block' : 'none';
        }
        return !error;
    }

    if (nameInput && nameForm) {
        nameInput.addEventListener('input', validateName);
        nameForm.addEventListener('submit', function (e) {
            if (!validateName()) { e.preventDefault(); nameInput.reportValidity(); nameInput.focus(); }
        });
    }

    // ── Objetivo personalizado ─────────────────────────────────────────────
    const goalSelect      = document.getElementById('goal-select-profile');
    const customGoalField = document.getElementById('custom-goal-field-profile');
    const customGoalTA    = document.getElementById('custom_goal_profile');

    function updateCustomGoalVisibility() {
        if (!goalSelect || !customGoalField) return;
        const isOther = goalSelect.value === 'other';
        customGoalField.style.display = isOther ? 'block' : 'none';
        if (customGoalTA) {
            isOther ? customGoalTA.setAttribute('required', 'required') : customGoalTA.removeAttribute('required');
        }
    }

    if (goalSelect) {
        goalSelect.addEventListener('change', updateCustomGoalVisibility);
        updateCustomGoalVisibility();
    }

    // ── Modal de troca de instrutor ────────────────────────────────────────
    const openBtn       = document.getElementById('open-instructor-modal');
    const overlay       = document.getElementById('instructor-modal-overlay');
    const closeBtn      = document.getElementById('close-instructor-modal');
    const cancelBtn     = document.getElementById('cancel-instructor-modal');
    const confirmBtn    = document.getElementById('confirm-instructor-change');
    const loadingEl     = document.getElementById('instructor-modal-loading');
    const messageEl     = document.getElementById('instructor-modal-message');
    const listEl        = document.getElementById('instructor-modal-list');
    const optionsListEl = document.getElementById('instructor-options-list');
    const reasonSection = document.getElementById('instructor-modal-reason');
    const reasonTA      = document.getElementById('instructor-change-reason');
    const actionsEl     = document.getElementById('instructor-modal-actions');
    const changeError   = document.getElementById('instructor-change-error');

    if (!openBtn || !overlay) return;

    let selectedInstructorId = null;

    function openModal() {
        overlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        loadInstructors();
    }

    function closeModal() {
        overlay.classList.remove('is-open');
        document.body.style.overflow = '';
        resetModal();
    }

    function resetModal() {
        selectedInstructorId = null;
        loadingEl.style.display     = 'block';
        messageEl.style.display     = 'none';
        listEl.style.display        = 'none';
        actionsEl.style.display     = 'none';
        reasonSection.style.display = 'none';
        changeError.style.display   = 'none';
        messageEl.textContent       = '';
        changeError.textContent     = '';
        optionsListEl.innerHTML     = '';
        if (confirmBtn) confirmBtn.disabled = true;
        if (reasonTA)   reasonTA.value = '';
    }

    function showMessage(text, isError = true) {
        loadingEl.style.display = 'none';
        listEl.style.display    = 'none';
        actionsEl.style.display = 'none';
        messageEl.style.display = 'block';
        messageEl.textContent   = text;
        messageEl.className     = isError ? 'enrollment-errors' : 'enrollment-info';
    }

    function escHtml(str) {
        return String(str || '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'})[c]);
    }

    function loadInstructors() {
        resetModal();

        fetch('{{ route('instructors.available') }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            loadingEl.style.display = 'none';

            if (data.message && (!data.available_instructors || data.available_instructors.length === 0)) {
                showMessage(data.message);
                return;
            }

            const instructors = data.available_instructors || [];

            if (instructors.length === 0) {
                showMessage('Nenhum instrutor disponível para os seus dias e turnos de treino no momento. Entre em contato com a recepção.');
                return;
            }

            optionsListEl.innerHTML = '';

            instructors.forEach(instructor => {
                const li = document.createElement('li');
                li.className = 'instructor-option';
                li.dataset.id = instructor.id;

                const slotsHtml = (instructor.slots || [])
                    .map(s => `<span class="instructor-slot-badge">${escHtml(s.week_day_label)} · ${escHtml(s.time_label)}</span>`)
                    .join('');

                li.innerHTML = `
                    <div class="instructor-option__head">
                        <div>
                            <div class="instructor-option__name">${escHtml(instructor.name)}</div>
                            ${instructor.specialty ? `<div class="instructor-option__specialty">${escHtml(instructor.specialty)}</div>` : ''}
                        </div>
                        <span class="instructor-option__count">${instructor.students_count} aluno(s)</span>
                    </div>
                    ${slotsHtml ? `<div class="instructor-option__slots">${slotsHtml}</div>` : ''}
                `;

                li.addEventListener('click', function () {
                    document.querySelectorAll('.instructor-option').forEach(el => el.classList.remove('is-selected'));
                    li.classList.add('is-selected');
                    selectedInstructorId = instructor.id;
                    confirmBtn.disabled = false;
                    changeError.style.display = 'none';
                });

                optionsListEl.appendChild(li);
            });

            listEl.style.display        = 'block';
            reasonSection.style.display = 'block';
            actionsEl.style.display     = 'flex';
        })
        .catch(() => {
            showMessage('Erro ao buscar instrutores. Tente novamente.');
        });
    }

    function confirmChange() {
        if (!selectedInstructorId) return;

        confirmBtn.disabled        = true;
        confirmBtn.textContent     = 'Aguarde…';
        changeError.style.display  = 'none';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        fetch('{{ route('instructor.change') }}', {
            method: 'POST',
            headers: {
                'Content-Type':     'application/json',
                'Accept':           'application/json',
                'X-CSRF-TOKEN':     csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                instructor_id: selectedInstructorId,
                reason:        reasonTA?.value?.trim() || null,
            }),
        })
        .then(async r => {
            const json = await r.json();
            if (!r.ok) {
                const errors = json.errors?.instructor_id || [json.message] || ['Erro ao trocar instrutor.'];
                changeError.textContent   = Array.isArray(errors) ? errors[0] : errors;
                changeError.style.display = 'block';
                confirmBtn.disabled       = false;
                confirmBtn.textContent    = 'Confirmar troca';
                return;
            }

            // Sucesso — atualiza o card sem reload
            closeModal();

            const nameEl        = document.querySelector('.instructor-current__name');
            const specialtyEl   = document.querySelector('.instructor-current__specialty');
            const avatarEl      = document.querySelector('.instructor-current__avatar');
            const newInstructor = json.new_instructor;

            if (nameEl && newInstructor) {
                nameEl.textContent = newInstructor.name;
                if (specialtyEl) specialtyEl.textContent = newInstructor.specialty || '';
                if (avatarEl)    avatarEl.textContent    = newInstructor.name.substring(0, 2).toUpperCase();
            }

            // Mensagem de sucesso temporária
            const successP = document.createElement('p');
            successP.className       = 'profile-saved';
            successP.style.marginTop = '10px';
            successP.textContent     = json.message;
            document.getElementById('instructor-card')?.appendChild(successP);
            setTimeout(() => successP.remove(), 5000);
        })
        .catch(() => {
            changeError.textContent   = 'Erro de conexão. Tente novamente.';
            changeError.style.display = 'block';
            confirmBtn.disabled       = false;
            confirmBtn.textContent    = 'Confirmar troca';
        });
    }

    openBtn.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    confirmBtn?.addEventListener('click', confirmChange);

    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.classList.contains('is-open')) closeModal();
    });
});
</script>