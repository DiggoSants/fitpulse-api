<x-app-layout>
<div class="enrollment-wrap">

    <div class="enrollment-header">
        <div>
            <p class="enrollment-kicker">FitPulse</p>
            <h1 class="enrollment-title">Matrícula</h1>
        </div>
        <a href="{{ route('dashboard') }}" class="enrollment-back">← Voltar</a>
    </div>

    @if(session('info'))
        <div class="enrollment-info">{{ session('info') }}</div>
    @endif

    @if($errors->any())
        <div class="enrollment-errors">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="enrollment-card">
        <form action="{{ route('enrollment.store') }}" method="POST" id="enrollment-form" data-min-days="{{ \App\Models\StudentSchedule::MIN_DAYS }}">
            @csrf

            @php
                $checkedScheduleDays = old('days', $selectedScheduleDays ?? []);
                $checkedScheduleDays = is_array($checkedScheduleDays) ? $checkedScheduleDays : [];
                $checkedScheduleShifts = old('shifts', $selectedScheduleShifts ?? []);
                $checkedScheduleShifts = is_array($checkedScheduleShifts) ? $checkedScheduleShifts : [];
                $availableShiftLabels = $shiftLabels ?? \App\Models\StudentSchedule::shiftLabels();
                $goalOptions = \App\Models\Student::getGoalOptions();
            @endphp

            <div class="enrollment-progress" aria-hidden="true">
                <span class="enrollment-progress__item is-active" data-progress-step="0">1</span>
                <span class="enrollment-progress__line"></span>
                <span class="enrollment-progress__item" data-progress-step="1">2</span>
                <span class="enrollment-progress__line"></span>
                <span class="enrollment-progress__item" data-progress-step="2">3</span>
            </div>

            <div class="enrollment-wizard-viewport">
                <div class="enrollment-wizard-track" id="enrollment-wizard-track">
                    <section class="enrollment-step" data-step="0">
                        <p class="enrollment-section-label">Escolha seu plano</p>

                        <ul class="plan-list">
                            @forelse($plans as $plan)
                                <li class="plan-option">
                                    <input
                                        type="radio"
                                        name="plan_id"
                                        value="{{ $plan->id }}"
                                        id="plan_{{ $plan->id }}"
                                        {{ old('plan_id') == $plan->id ? 'checked' : '' }}
                                    >
                                    <label for="plan_{{ $plan->id }}">
                                        <div class="plan-option__info">
                                            <p class="plan-option__name">{{ $plan->name }}</p>
                                            <p class="plan-option__meta">{{ $plan->duration_days }} dias</p>
                                        </div>
                                        <span class="plan-option__price">
                                            R$ {{ number_format($plan->price, 2, ',', '.') }}
                                        </span>
                                    </label>
                                    <button
                                        type="button"
                                        class="plan-option__details-btn"
                                        onclick="openPlanModal('modal-{{ $plan->id }}')"
                                    >
                                        Ver detalhes
                                    </button>
                                </li>
                            @empty
                                <li class="enrollment-empty">Nenhum plano disponível no momento.</li>
                            @endforelse
                        </ul>

                        @error('plan_id')
                            <span class="profile-field-error">{{ $message }}</span>
                        @enderror

                        <div class="profile-field">
                            <p class="enrollment-section-label">Objetivo na academia</p>
                            <select id="goal-select-enrollment" name="goal" required>
                                <option value="" disabled selected>Selecione seu objetivo</option>
                                @foreach($goalOptions as $key => $label)
                                    <option value="{{ $key }}" {{ old('goal') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('goal')
                                <span class="profile-field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="profile-field" id="custom-goal-field" style="display: none;">
                            <label for="custom_goal">Descreva seu objetivo</label>
                            <textarea
                                id="custom_goal"
                                name="custom_goal"
                                class="profile-textarea"
                                placeholder="Ex: Melhorar meu condicionamento físico para correr uma meia maratona"
                                rows="4">{{ old('custom_goal') }}</textarea>
                            @error('custom_goal')
                                <span class="profile-field-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="wizard-inline-feedback" data-step-message="0"></div>

                        <div class="enrollment-actions">
                            <button type="button" class="btn-save" data-next-step>Continuar</button>
                        </div>
                    </section>

                    <section class="enrollment-step" data-step="1">
                        <p class="enrollment-section-label">Dias e turnos de treino</p>

                        <div class="schedule-days-grid schedule-days-grid--detailed">
                            @foreach($weekDays as $dayKey => $dayLabel)
                                @php
                                    $dayIsChecked = in_array($dayKey, $checkedScheduleDays, true);
                                    $selectedShift = $checkedScheduleShifts[$dayKey] ?? '';
                                @endphp
                                <div class="schedule-day-card" data-day-card data-day="{{ $dayKey }}">
                                    <label class="schedule-day-option">
                                        <input
                                            type="checkbox"
                                            name="days[]"
                                            value="{{ $dayKey }}"
                                            @checked($dayIsChecked)
                                        >
                                        <span>{{ $dayLabel }}</span>
                                    </label>

                                    <div class="schedule-shift-options" data-shift-options>
                                        @foreach($availableShiftLabels as $shiftKey => $shiftLabel)
                                            <label class="schedule-shift-option" data-shift-option="{{ $shiftKey }}">
                                                <input
                                                    type="radio"
                                                    name="shifts[{{ $dayKey }}]"
                                                    value="{{ $shiftKey }}"
                                                    @checked($selectedShift === $shiftKey)
                                                    @disabled(!$dayIsChecked)
                                                >
                                                <span>{{ $shiftLabel }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                    <p class="schedule-day-note" data-day-note></p>
                                </div>
                            @endforeach
                        </div>

                        <div class="enrollment-empty" id="schedule-empty" hidden>
                            Nenhum horário livre no momento.
                        </div>

                        @error('days')
                            <span class="profile-field-error">{{ $message }}</span>
                        @enderror
                        @error('days.*')
                            <span class="profile-field-error">{{ $message }}</span>
                        @enderror
                        @error('shifts')
                            <span class="profile-field-error">{{ $message }}</span>
                        @enderror
                        @error('shifts.*')
                            <span class="profile-field-error">{{ $message }}</span>
                        @enderror

                        <div class="wizard-inline-feedback" id="schedule-feedback" data-step-message="1"></div>

                        <div class="enrollment-actions">
                            <button type="button" class="btn-cancel" data-prev-step>Voltar</button>
                            <button type="button" class="btn-save" data-next-step>Ver instrutor</button>
                        </div>
                    </section>

                    <section class="enrollment-step" data-step="2">
                        <p class="enrollment-section-label">Instrutor disponível</p>
                        <div class="available-instructor-panel" id="available-instructor-panel"></div>

                        @error('instructor')
                            <span class="profile-field-error">{{ $message }}</span>
                        @enderror

                        <p class="enrollment-section-label">Forma de pagamento</p>
                        <div class="payment-method-grid">
                            <label class="payment-method-option">
                                <input type="radio" name="payment_method" value="pix" {{ old('payment_method', 'pix') === 'pix' ? 'checked' : '' }}>
                                <span>
                                    <strong>Pix</strong>
                                    <small>Confirmação imediata</small>
                                </span>
                            </label>
                            <label class="payment-method-option">
                                <input type="radio" name="payment_method" value="debit_card" {{ old('payment_method') === 'debit_card' ? 'checked' : '' }}>
                                <span>
                                    <strong>Débito</strong>
                                    <small>Confirmação imediata</small>
                                </span>
                            </label>
                            <label class="payment-method-option">
                                <input type="radio" name="payment_method" value="credit_card" {{ old('payment_method') === 'credit_card' ? 'checked' : '' }}>
                                <span>
                                    <strong>Crédito</strong>
                                    <small>Aprovação automática</small>
                                </span>
                            </label>
                        </div>

                        @error('payment_method')
                            <span class="profile-field-error">{{ $message }}</span>
                        @enderror

                        @if($plans->count())
                            <div class="enrollment-actions">
                                <button type="button" class="btn-cancel" data-prev-step>Voltar</button>
                                <button type="submit" class="btn-save" id="confirm-enrollment-btn">Confirmar Matrícula</button>
                            </div>
                        @endif
                    </section>
                </div>
            </div>
        </form>
    </div>

</div>

@foreach($plans as $plan)
    <div
        id="modal-{{ $plan->id }}"
        class="plan-modal-overlay"
        onclick="closePlanModalOutside(event, 'modal-{{ $plan->id }}')"
    >
        <div class="plan-modal">
            <button
                type="button"
                class="plan-modal__close"
                onclick="closePlanModal('modal-{{ $plan->id }}')"
            >×</button>

            <div class="plan-modal__top">
                <p class="plan-modal__kicker">Detalhes do Plano</p>
                <p class="plan-modal__name">{{ $plan->name }}</p>
                <div class="plan-modal__price-row">
                    <span class="plan-modal__price">
                        R$ {{ number_format($plan->price, 2, ',', '.') }}
                    </span>
                    <span class="plan-modal__price-period">por mensalidade</span>
                    <span class="plan-modal__duration-badge">{{ $plan->duration_days }} dias</span>
                </div>
            </div>

            <div class="plan-modal__body">
                @if($plan->description)
                    <p style="font-size:13px; color:var(--text-muted); line-height:1.6; margin:0;">
                        {{ $plan->description }}
                    </p>
                @endif

                @if($plan->benefits)
                    <div>
                        <p class="plan-modal__features-label">O que está incluso</p>
                        <ul class="plan-modal__features">
                            @foreach(explode(',', $plan->benefits) as $benefit)
                                <li class="plan-modal__feature">
                                    <span class="plan-modal__feature-icon plan-modal__feature-icon--yes">
                                        <svg viewBox="0 0 12 12" fill="none">
                                            <path d="M2 6l3 3 5-5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    {{ trim($benefit) }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <p style="font-size:13px; color:var(--text-muted); margin:0;">
                        Nenhum benefício listado para este plano.
                    </p>
                @endif

                <div class="plan-modal__footer">
                    <button
                        type="button"
                        class="btn-save"
                        style="flex:1; justify-content:center;"
                        onclick="selectPlanAndClose('plan_{{ $plan->id }}', 'modal-{{ $plan->id }}')"
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
@endforeach

<script>
const ENROLLMENT_INSTRUCTORS = @json($instructorOptions);
const ENROLLMENT_WEEK_DAYS = @json($weekDays);
const ENROLLMENT_SHIFT_LABELS = @json($availableShiftLabels);

function openPlanModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }
}

function closePlanModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('is-open');
        document.body.style.overflow = '';
    }
}

function closePlanModalOutside(event, id) {
    if (event.target === document.getElementById(id)) {
        closePlanModal(id);
    }
}

function selectPlanAndClose(radioId, modalId) {
    const radio = document.getElementById(radioId);
    if (radio) {
        radio.checked = true;
        radio.dispatchEvent(new Event('change', { bubbles: true }));
    }
    closePlanModal(modalId);
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.plan-modal-overlay.is-open').forEach(function(m) {
            m.classList.remove('is-open');
        });
        document.body.style.overflow = '';
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('enrollment-form');
    const viewport = document.querySelector('.enrollment-wizard-viewport');
    const track = document.getElementById('enrollment-wizard-track');
    const steps = Array.from(document.querySelectorAll('.enrollment-step'));
    const progressItems = Array.from(document.querySelectorAll('[data-progress-step]'));
    const minDays = Number(form.dataset.minDays || 2);
    const goalSelect = document.getElementById('goal-select-enrollment');
    const customGoalField = document.getElementById('custom-goal-field');
    const customGoalTextarea = document.getElementById('custom_goal');
    const scheduleFeedback = document.getElementById('schedule-feedback');
    const instructorPanel = document.getElementById('available-instructor-panel');
    const confirmButton = document.getElementById('confirm-enrollment-btn');
    const dayCards = Array.from(document.querySelectorAll('[data-day-card]'));
    const scheduleEmpty = document.getElementById('schedule-empty');
    const shiftOrder = Object.keys(ENROLLMENT_SHIFT_LABELS);
    let currentStep = 0;
    let currentInstructor = null;

    function escapeHtml(value) {
        return String(value || '').replace(/[&<>"']/g, function(char) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
        });
    }

    function selectedPlan() {
        return form.querySelector('input[name="plan_id"]:checked');
    }

    function selectedDays() {
        return dayCards
            .filter(card => {
                const checkbox = card.querySelector('input[name="days[]"]');
                return checkbox && checkbox.checked && !checkbox.disabled && !card.hidden;
            })
            .map(card => card.dataset.day);
    }

    function selectedShiftFor(card) {
        const input = card.querySelector('[data-shift-option] input:checked');
        return input && !input.disabled ? input.value : null;
    }

    function selectedSchedule() {
        return dayCards
            .filter(card => {
                const checkbox = card.querySelector('input[name="days[]"]');
                return checkbox && checkbox.checked && !checkbox.disabled && !card.hidden;
            })
            .map(card => ({
                day: card.dataset.day,
                shift: selectedShiftFor(card),
            }))
            .filter(item => Boolean(item.shift));
    }

    function slotIsFree(slot) {
        return slot && slot.occupied !== true;
    }

    function freeSlotsForDay(day) {
        const slotsByShift = new Map();

        ENROLLMENT_INSTRUCTORS.forEach(instructor => {
            (instructor.availability || []).forEach(slot => {
                if (slot.week_day === day && slotIsFree(slot) && !slotsByShift.has(slot.shift)) {
                    slotsByShift.set(slot.shift, slot);
                }
            });
        });

        return Array.from(slotsByShift.values()).sort((a, b) => {
            const first = shiftOrder.indexOf(a.shift);
            const second = shiftOrder.indexOf(b.shift);
            return (first === -1 ? 99 : first) - (second === -1 ? 99 : second);
        });
    }

    function syncDayAvailability() {
        let visibleDays = 0;

        dayCards.forEach(card => {
            const day = card.dataset.day;
            const checkbox = card.querySelector('input[name="days[]"]');
            const note = card.querySelector('[data-day-note]');
            const freeSlots = freeSlotsForDay(day);
            const freeShifts = freeSlots.map(slot => slot.shift);
            const hasFreeSlot = freeSlots.length > 0;

            visibleDays += hasFreeSlot ? 1 : 0;
            card.hidden = !hasFreeSlot;
            card.classList.toggle('is-selected', Boolean(checkbox?.checked && hasFreeSlot));

            if (checkbox) {
                checkbox.disabled = !hasFreeSlot;
                if (!hasFreeSlot) {
                    checkbox.checked = false;
                }
            }

            let firstAvailableInput = null;

            card.querySelectorAll('[data-shift-option]').forEach(label => {
                const input = label.querySelector('input');
                const available = input && freeShifts.includes(input.value);

                label.hidden = !available;

                if (input) {
                    input.disabled = !checkbox?.checked || !available;

                    if (!available && input.checked) {
                        input.checked = false;
                    }

                    if (available && !firstAvailableInput) {
                        firstAvailableInput = input;
                    }
                }
            });

            if (checkbox?.checked && !selectedShiftFor(card) && firstAvailableInput) {
                firstAvailableInput.checked = true;
                firstAvailableInput.disabled = false;
            }

            if (note) {
                note.textContent = hasFreeSlot
                    ? `${freeSlots.length} turno(s) livre(s)`
                    : '';
            }
        });

        if (scheduleEmpty) {
            scheduleEmpty.hidden = visibleDays > 0;
        }

        adjustWizardHeight();
    }

    function adjustWizardHeight() {
        const activeStep = steps[currentStep];
        if (!viewport || !activeStep) return;

        requestAnimationFrame(() => {
            viewport.style.height = `${activeStep.scrollHeight}px`;
        });
    }

    function setStep(step) {
        currentStep = Math.max(0, Math.min(step, steps.length - 1));
        track.style.transform = `translateX(-${currentStep * 100}%)`;
        steps.forEach((item, index) => item.classList.toggle('is-active', index === currentStep));
        progressItems.forEach((item, index) => {
            item.classList.toggle('is-active', index <= currentStep);
        });
        adjustWizardHeight();
    }

    function setMessage(step, message, type = 'error') {
        const target = document.querySelector(`[data-step-message="${step}"]`);
        if (!target) return;
        target.textContent = message || '';
        target.classList.toggle('is-error', type === 'error' && Boolean(message));
        target.classList.toggle('is-ok', type === 'ok' && Boolean(message));
        adjustWizardHeight();
    }

    function updateCustomGoalVisibility() {
        if (goalSelect.value === 'other') {
            customGoalField.style.display = 'block';
            customGoalTextarea.setAttribute('required', 'required');
        } else {
            customGoalField.style.display = 'none';
            customGoalTextarea.removeAttribute('required');
        }
        adjustWizardHeight();
    }

    function validateFirstStep() {
        if (!selectedPlan()) {
            setMessage(0, 'Selecione um plano.');
            return false;
        }

        if (!goalSelect.value) {
            setMessage(0, 'Selecione seu objetivo.');
            return false;
        }

        if (goalSelect.value === 'other' && !customGoalTextarea.value.trim()) {
            setMessage(0, 'Descreva seu objetivo personalizado.');
            return false;
        }

        setMessage(0, '');
        return true;
    }

    function matchingInstructors(schedule) {
        if (schedule.length < minDays) return [];

        return ENROLLMENT_INSTRUCTORS
            .filter(instructor => schedule.every(item => {
                return (instructor.availability || []).some(slot => {
                    return slot.week_day === item.day
                        && slot.shift === item.shift
                        && slotIsFree(slot);
                });
            }))
            .sort((a, b) => {
                const load = Number(a.students_count || 0) - Number(b.students_count || 0);
                return load !== 0 ? load : String(a.name || '').localeCompare(String(b.name || ''));
            });
    }

    function renderInstructorLegacy() {
        const days = selectedDays();
        const dayLabels = days.map(day => ENROLLMENT_WEEK_DAYS[day] || day);
        const matches = matchingInstructors(days);
        currentInstructor = matches[0] || null;

        if (days.length < minDays) {
            instructorPanel.className = 'available-instructor-panel is-empty';
            instructorPanel.innerHTML = `<strong>Selecione pelo menos ${minDays} dias.</strong>`;
            if (confirmButton) confirmButton.disabled = true;
            scheduleFeedback.textContent = '';
            scheduleFeedback.className = 'wizard-inline-feedback';
            adjustWizardHeight();
            return false;
        }

        if (!currentInstructor) {
            instructorPanel.className = 'available-instructor-panel is-empty';
            instructorPanel.innerHTML = '<strong>Nenhum instrutor disponível para esses dias.</strong>';
            if (confirmButton) confirmButton.disabled = true;
            scheduleFeedback.textContent = 'Nenhum instrutor disponível para os dias escolhidos.';
            scheduleFeedback.className = 'wizard-inline-feedback is-error';
            adjustWizardHeight();
            return false;
        }

        const dayBadges = dayLabels.map(label => `<span>${escapeHtml(label)}</span>`).join('');
        const shifts = Array.from(new Set((currentInstructor.availability || [])
            .filter(slot => days.includes(slot.week_day))
            .map(slot => slot.shift_label || slot.shift)
        ));

        instructorPanel.className = 'available-instructor-panel';
        instructorPanel.innerHTML = `
            <div class="available-instructor-panel__head">
                <div>
                    <strong>${escapeHtml(currentInstructor.name)}</strong>
                    <small>${escapeHtml(currentInstructor.specialty || 'Instrutor FitPulse')}</small>
                </div>
                <span>${Number(currentInstructor.students_count || 0)} alunos</span>
            </div>
            <div class="available-instructor-panel__days">${dayBadges}</div>
            <p>${escapeHtml(shifts.join(' • ') || 'Disponibilidade ativa')}</p>
        `;

        if (confirmButton) confirmButton.disabled = false;
        scheduleFeedback.textContent = `${matches.length} instrutor(es) disponível(is).`;
        scheduleFeedback.className = 'wizard-inline-feedback is-ok';
        adjustWizardHeight();
        return true;
    }

    function validateDaysStepLegacy() {
        const days = selectedDays();

        if (days.length < minDays) {
            setMessage(1, `Selecione pelo menos ${minDays} dias de treino.`);
            renderInstructor();
            return false;
        }

        if (!renderInstructor()) {
            setMessage(1, 'Escolha outros dias para encontrar um instrutor disponível.');
            return false;
        }

        setMessage(1, '');
        return true;
    }

    function renderInstructor() {
        const days = selectedDays();
        const schedule = selectedSchedule();
        const matches = matchingInstructors(schedule);
        currentInstructor = matches[0] || null;

        if (days.length < minDays) {
            instructorPanel.className = 'available-instructor-panel is-empty';
            instructorPanel.innerHTML = `<strong>Selecione pelo menos ${minDays} dias com turno livre.</strong>`;
            if (confirmButton) confirmButton.disabled = true;
            scheduleFeedback.textContent = '';
            scheduleFeedback.className = 'wizard-inline-feedback';
            adjustWizardHeight();
            return false;
        }

        if (schedule.length !== days.length) {
            instructorPanel.className = 'available-instructor-panel is-empty';
            instructorPanel.innerHTML = '<strong>Escolha um turno para cada dia selecionado.</strong>';
            if (confirmButton) confirmButton.disabled = true;
            scheduleFeedback.textContent = 'Informe o turno em todos os dias escolhidos.';
            scheduleFeedback.className = 'wizard-inline-feedback is-error';
            adjustWizardHeight();
            return false;
        }

        if (!currentInstructor) {
            instructorPanel.className = 'available-instructor-panel is-empty';
            instructorPanel.innerHTML = '<strong>Nenhum instrutor disponível para esses dias e turnos.</strong>';
            if (confirmButton) confirmButton.disabled = true;
            scheduleFeedback.textContent = 'Nenhum instrutor disponível para a combinação escolhida.';
            scheduleFeedback.className = 'wizard-inline-feedback is-error';
            adjustWizardHeight();
            return false;
        }

        const dayBadges = schedule.map(item => {
            const dayLabel = ENROLLMENT_WEEK_DAYS[item.day] || item.day;
            const shiftLabel = ENROLLMENT_SHIFT_LABELS[item.shift] || item.shift;
            return `<span>${escapeHtml(dayLabel)} - ${escapeHtml(shiftLabel)}</span>`;
        }).join('');

        const timeLabels = schedule.map(item => {
            const slot = (currentInstructor.availability || []).find(availability => {
                return availability.week_day === item.day && availability.shift === item.shift;
            });

            return slot?.time_label || slot?.shift_label || ENROLLMENT_SHIFT_LABELS[item.shift] || item.shift;
        });

        instructorPanel.className = 'available-instructor-panel';
        instructorPanel.innerHTML = `
            <div class="available-instructor-panel__head">
                <div>
                    <strong>${escapeHtml(currentInstructor.name)}</strong>
                    <small>${escapeHtml(currentInstructor.specialty || 'Instrutor FitPulse')}</small>
                </div>
                <span>${Number(currentInstructor.students_count || 0)} alunos</span>
            </div>
            <div class="available-instructor-panel__days">${dayBadges}</div>
            <p>${escapeHtml(Array.from(new Set(timeLabels)).join(' - ') || 'Disponibilidade ativa')}</p>
        `;

        if (confirmButton) confirmButton.disabled = false;
        scheduleFeedback.textContent = `${matches.length} instrutor(es) disponível(is).`;
        scheduleFeedback.className = 'wizard-inline-feedback is-ok';
        adjustWizardHeight();
        return true;
    }

    function validateDaysStep() {
        const days = selectedDays();

        if (days.length < minDays) {
            setMessage(1, `Selecione pelo menos ${minDays} dias de treino com turno.`);
            renderInstructor();
            return false;
        }

        if (!renderInstructor()) {
            setMessage(1, 'Escolha outros dias ou turnos para encontrar um instrutor disponível.');
            return false;
        }

        setMessage(1, '');
        return true;
    }

    document.querySelectorAll('[data-next-step]').forEach(button => {
        button.addEventListener('click', function() {
            if (currentStep === 0 && !validateFirstStep()) return;
            if (currentStep === 1 && !validateDaysStep()) return;
            setStep(currentStep + 1);
        });
    });

    document.querySelectorAll('[data-prev-step]').forEach(button => {
        button.addEventListener('click', function() {
            setStep(currentStep - 1);
        });
    });

    form.querySelectorAll('input[name="plan_id"]').forEach(input => {
        input.addEventListener('change', () => setMessage(0, ''));
    });

    form.querySelectorAll('input[name="days[]"]').forEach(input => {
        input.addEventListener('change', function() {
            syncDayAvailability();
            setMessage(1, '');
            renderInstructor();
        });
    });

    form.querySelectorAll('[data-shift-option] input').forEach(input => {
        input.addEventListener('change', function() {
            setMessage(1, '');
            renderInstructor();
        });
    });

    goalSelect.addEventListener('change', function() {
        updateCustomGoalVisibility();
        setMessage(0, '');
    });

    customGoalTextarea.addEventListener('input', () => setMessage(0, ''));
    window.addEventListener('resize', adjustWizardHeight);

    form.addEventListener('submit', function(event) {
        if (!validateFirstStep()) {
            event.preventDefault();
            setStep(0);
            return;
        }

        if (!validateDaysStep()) {
            event.preventDefault();
            setStep(1);
        }
    });

    updateCustomGoalVisibility();
    syncDayAvailability();
    renderInstructor();

    if (selectedPlan() && goalSelect.value && selectedSchedule().length >= minDays) {
        setStep(2);
    } else if (selectedPlan() && goalSelect.value) {
        setStep(1);
    } else {
        setStep(0);
    }
});
</script>

</x-app-layout>
