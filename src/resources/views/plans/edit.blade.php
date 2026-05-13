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
                    <h1 class="workout-form-title">Editar Plano</h1>
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

            {{-- FORMULÁRIO DE EDIÇÃO --}}
            <form action="{{ route('plans.update', $plan->id) }}" method="POST" id="editForm">
                @csrf
                @method('PUT')

                <div class="workout-form-card">

                    {{-- Nome --}}
                    <div class="profile-field">
                        <label for="name">Nome do Plano</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $plan->name) }}"
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
                        >{{ old('description', $plan->description) }}</textarea>
                    </div>

                    {{-- Preço e Duração --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:18px;">
                        <div class="profile-field">
                            <label for="price">Preço (R$)</label>
                            <input
                                type="number"
                                id="price"
                                name="price"
                                value="{{ old('price', $plan->price) }}"
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
                                value="{{ old('duration_days', $plan->duration_days) }}"
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
                            placeholder="Liste os benefícios separados por vírgula."
                            rows="3"
                        >{{ old('benefits', $plan->benefits) }}</textarea>
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
                            <option value="active"   {{ old('status', $plan->status) === 'active'   ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ old('status', $plan->status) === 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                        @if($plan->status === 'inactive')
                            <p style="font-size:11px; color:var(--red-light); margin-top:6px;">
                                Este plano está inativo e não aparece para novos alunos.
                            </p>
                        @endif
                    </div>

                    {{-- Info de alunos vinculados --}}
                    @php $enrolledCount = $plan->enrollments()->count(); @endphp
                    @if($enrolledCount > 0)
                        <div class="enrollment-info" style="margin-top:18px;">
                            <strong>{{ $enrolledCount }} aluno(s)</strong> matriculado(s) neste plano.
                            A inativação preserva o histórico sem cancelar matrículas existentes.
                        </div>
                    @endif

                    {{-- Ações do form de edição --}}
                    <div class="profile-form-row" style="margin-top:28px; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                        <div style="display:flex; gap:10px; flex-wrap:wrap;">
                            <button type="submit" class="btn-save" id="btnSave">
                                <svg width="13" height="13" viewBox="0 0 14 14" fill="none"
                                     style="stroke:#fff; stroke-width:2.5; stroke-linecap:round; stroke-linejoin:round;">
                                    <path d="M2 7l4 4 6-6"/>
                                </svg>
                                Salvar Alterações
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn-cancel">Cancelar</a>
                        </div>

                        {{-- Botão de inativar/restaurar que submete o form separado abaixo --}}
                        @if($plan->status === 'active')
                            <button
                                type="button"
                                class="btn-del"
                                onclick="document.getElementById('destroyForm').submit()"
                                onclickconfirm="return confirm('Inativar este plano?')"
                                onclick="if(confirm('Inativar este plano? O histórico será preservado.')) document.getElementById('destroyForm').submit()"
                            >
                                <svg width="13" height="15" viewBox="0 0 14 16" fill="none"
                                     style="stroke:#f87171; stroke-width:1.8; stroke-linecap:round;">
                                    <path d="M1 3.5h12M4.5 3.5V2a.5.5 0 01.5-.5h3a.5.5 0 01.5.5v1.5M5.5 7v5M8.5 7v5M2.5 3.5l.9 10a.5.5 0 00.5.5h6.2a.5.5 0 00.5-.5l.9-10"/>
                                </svg>
                                Inativar Plano
                            </button>
                        @else
                            <button
                                type="button"
                                class="btn-ghost"
                                style="font-size:12px; padding:9px 18px;"
                                onclick="document.getElementById('restoreForm').submit()"
                            >
                                Restaurar Plano
                            </button>
                        @endif
                    </div>

                </div>
            </form>

            {{-- FORM DE INATIVAR — separado, fora do form de edição --}}
            @if($plan->status === 'active')
                <form id="destroyForm" action="{{ route('plans.destroy', $plan->id) }}" method="POST" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>
            @else
                <form id="restoreForm" action="{{ route('plans.restore', $plan->id) }}" method="POST" style="display:none;">
                    @csrf
                </form>
            @endif

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