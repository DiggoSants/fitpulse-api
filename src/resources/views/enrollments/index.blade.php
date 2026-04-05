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

    {{-- Card do formulário --}}
    <div class="enrollment-card">
        <form action="{{ route('enrollment.store') }}" method="POST">
            @csrf

            {{-- Código do instrutor --}}
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
                <span>{{ $message }}</span>
            @enderror

            {{-- Escolha do plano --}}
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

            @if($plans->count())
                <div class="enrollment-actions" style="margin-top: 8px;">
                    <button type="submit" class="btn-save">Confirmar Matrícula</button>
                </div>
            @endif

        </form>
    </div>

</div>