<x-app-layout>
<div class="enrollment-wrap">

    {{-- Cabeçalho --}}
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
        <form action="{{ route('enrollment.store') }}" method="POST">
            @csrf

            <div class="profile-field">
                <p class="enrollment-section-label">Código do Instrutor</p>
                <input
                    type="text"
                    name="invite_code"
                    value="{{ old('invite_code') }}"
                    placeholder="Ex: A3BX92KL"
                    maxlength="8"
                    style="text-transform:uppercase;"
                >
                @error('invite_code')
                    <span class="profile-field-error">{{ $message }}</span>
                @enderror
            </div>

            <p class="enrollment-section-label">Escolha seu Plano</p>

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
                <p class="enrollment-section-label">Qual é seu objetivo?</p>
                <select id="goal-select-enrollment" name="goal" required>
                    <option value="" disabled selected>Selecione seu objetivo</option>
                    @php
                        $goalOptions = \App\Models\Student::getGoalOptions();
                    @endphp
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

            <p class="enrollment-section-label">Forma de pagamento</p>
            <div class="payment-method-grid">
                <label class="payment-method-option">
                    <input type="radio" name="payment_method" value="pix" {{ old('payment_method', 'pix') === 'pix' ? 'checked' : '' }}>
                    <span>
                        <strong>Pix</strong>
                        <small>Confirmacao imediata</small>
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
                <div class="enrollment-actions" style="margin-top: 8px;">
                    <button type="submit" class="btn-save">Confirmar Matrícula</button>
                </div>
            @endif

        </form>
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     MODAIS DE DETALHES DOS PLANOS
     Usa as classes .plan-modal-overlay / .plan-modal
     já definidas no app.css do projeto
════════════════════════════════════════════════ --}}
@foreach($plans as $plan)
    <div
        id="modal-{{ $plan->id }}"
        class="plan-modal-overlay"
        onclick="closePlanModalOutside(event, 'modal-{{ $plan->id }}')"
    >
        <div class="plan-modal">

            {{-- Botão fechar --}}
            <button
                type="button"
                class="plan-modal__close"
                onclick="closePlanModal('modal-{{ $plan->id }}')"
            >×</button>

            {{-- Topo com gradiente vermelho (igual ao CSS .plan-modal__top) --}}
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

            {{-- Corpo --}}
            <div class="plan-modal__body">

                {{-- Descrição --}}
                @if($plan->description)
                    <p style="font-size:13px; color:var(--text-muted); line-height:1.6; margin:0;">
                        {{ $plan->description }}
                    </p>
                @endif

                {{-- Benefícios --}}
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

                {{-- Ações --}}
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
    // Fecha só se clicou no backdrop, não dentro do .plan-modal
    if (event.target === document.getElementById(id)) {
        closePlanModal(id);
    }
}

function selectPlanAndClose(radioId, modalId) {
    const radio = document.getElementById(radioId);
    if (radio) radio.checked = true;
    closePlanModal(modalId);
}

// Fecha com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.plan-modal-overlay.is-open').forEach(function(m) {
            m.classList.remove('is-open');
        });
        document.body.style.overflow = '';
    }
});

// Controlar visibilidade do campo de objetivo customizado
document.addEventListener('DOMContentLoaded', function() {
    const goalSelect = document.getElementById('goal-select-enrollment');
    const customGoalField = document.getElementById('custom-goal-field');
    const customGoalTextarea = document.getElementById('custom_goal');
    
    function updateCustomGoalVisibility() {
        if (goalSelect.value === 'other') {
            customGoalField.style.display = 'block';
            customGoalTextarea.setAttribute('required', 'required');
        } else {
            customGoalField.style.display = 'none';
            customGoalTextarea.removeAttribute('required');
        }
    }
    
    if (goalSelect) {
        goalSelect.addEventListener('change', updateCustomGoalVisibility);
        // Verificar ao carregar página
        updateCustomGoalVisibility();
    }
});
</script>

</x-app-layout>
