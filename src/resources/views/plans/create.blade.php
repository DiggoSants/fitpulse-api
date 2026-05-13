<x-app-layout>
    <div class="py-6 form-page">
        <div class="form-watermark" aria-hidden="true">
            <span>PLANO</span>
        </div>

        <div class="form-content workout-form-wrap">

            {{-- CABEÇALHO --}}
            <div class="workout-form-header">
                <div>
                    <div class="workout-form-kicker">Gerenciamento</div>
                    <h1 class="workout-form-title">Novo Plano</h1>
                </div>
                <a href="{{ route('dashboard') }}" class="workout-form-back">
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                         style="stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;">
                        <path d="M7.5 2L3.5 6l4 4"/>
                    </svg>
                    Voltar
                </a>
            </div>

            {{-- ERROS --}}
            @if($errors->any())
                <div class="enrollment-errors" style="margin-bottom:20px;">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- FORMULÁRIO --}}
            <form action="{{ route('plans.store') }}" method="POST">
                @csrf

                <div class="workout-form-card">

                    {{-- Nome --}}
                    <div class="profile-field">
                        <label for="name">Nome do Plano</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Ex: Plano Mensal Premium"
                            required
                        >
                    </div>

                    {{-- Descrição --}}
                    <div class="profile-field" style="margin-top:18px;">
                        <label for="description">Descrição</label>
                        <textarea
                            id="description"
                            name="description"
                            class="profile-textarea"
                            placeholder="Descreva o plano brevemente..."
                            rows="3"
                        >{{ old('description') }}</textarea>
                    </div>

                    {{-- Preço e Duração --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:18px;">
                        <div class="profile-field">
                            <label for="price">Preço (R$)</label>
                            <input
                                type="number"
                                id="price"
                                name="price"
                                value="{{ old('price') }}"
                                placeholder="0.00"
                                step="0.01"
                                min="0"
                                required
                            >
                        </div>

                        <div class="profile-field">
                            <label for="duration_days">Duração (dias)</label>
                            <input
                                type="number"
                                id="duration_days"
                                name="duration_days"
                                value="{{ old('duration_days') }}"
                                placeholder="30"
                                min="1"
                                required
                            >
                        </div>
                    </div>

                    {{-- Benefícios --}}
                    <div class="profile-field" style="margin-top:18px;">
                        <label for="benefits">Benefícios</label>
                        <textarea
                            id="benefits"
                            name="benefits"
                            class="profile-textarea"
                            placeholder="Liste os benefícios separados por vírgula. Ex: Acesso à musculação, Aulas de spinning, Avaliação física"
                            rows="3"
                        >{{ old('benefits') }}</textarea>
                        <p style="font-size:11px; color:var(--text-muted); margin-top:6px; opacity:.7;">
                            Separe cada benefício por vírgula.
                        </p>
                    </div>

                    {{-- Status --}}
                    <div class="profile-field" style="margin-top:18px;">
                        <label for="status">Status</label>
                        <select
                            id="status"
                            name="status"
                            class="workout-select"
                        >
                            <option value="active"   {{ old('status', 'active') === 'active'   ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>

                    {{-- Ações --}}
                    <div class="profile-form-row" style="margin-top:28px;">
                        <button type="submit" class="btn-save" id="btnSave">
                            <svg width="13" height="13" viewBox="0 0 14 14" fill="none"
                                 style="stroke:#fff; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round;">
                                <path d="M2 7l4 4 6-6"/>
                            </svg>
                            Salvar Plano
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn-cancel">Cancelar</a>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <script>
        document.getElementById('btnSave').addEventListener('click', function () {
            const btn = this;
            btn.innerHTML = `
                <svg width="13" height="13" viewBox="0 0 14 14" fill="none"
                     class="btn-save__check"
                     style="stroke:#fff; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round;">
                    <path d="M2 7l4 4 6-6"/>
                </svg>
                Salvando...
            `;
            btn.classList.add('btn-save--saved');
        });
    </script>
</x-app-layout>