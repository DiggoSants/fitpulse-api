<x-app-layout>
<div class="inst-wrap">

    <div class="dash-hero" style="margin-bottom: 28px;">
        <div class="dash-hero__ring"></div>
        <div class="dash-hero__inner">
            <div>
                <div class="dash-hero__eyebrow">Gerenciamento</div>
                <h1 class="dash-hero__title">Instrutores</h1>
                <p class="dash-hero__sub">Lista completa de instrutores cadastrados</p>
            </div>
            <div class="dash-hero__right">
                <span class="dash-hero__pulse">
                    <span class="dash-hero__pulse-dot"></span>
                    GERENTE
                </span>
                <a href="{{ route('instructors.create') }}" class="btn-save">+ Novo Instrutor</a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="enrollment-info" style="margin-bottom: 20px;">{{ session('success') }}</div>
    @endif

    <div class="students-grid">
        @forelse($instructors as $instructor)
        <div class="student-card student-card--ok">

            <div class="student-card__header">
                <div class="student-avatar">{{ strtoupper(substr($instructor->user->name, 0, 2)) }}</div>
                <div style="flex:1; min-width:0;">
                    <p class="student-card__name">{{ $instructor->user->name }}</p>
                    <p class="student-card__email">{{ $instructor->user->email }}</p>
                </div>
                <span class="chip-num">{{ $instructor->students->count() }} aluno(s)</span>
            </div>

            <div style="padding:14px 20px 16px; display:flex; flex-direction:column; gap:12px;">

                @if($instructor->specialty)
                <div style="display:flex; align-items:center; gap:8px;">
                 <span class="exercises-header__tag">Especialidade</span>
                 <span class="inst-specialty-val">{{ $instructor->specialty }}</span>
               </div>
                @endif

                <div style="display:flex; gap:8px;">
                    <a href="{{ route('instructors.edit', $instructor->id) }}" class="btn-workout-action" style="flex:1; justify-content:center;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Editar
                    </a>
                    <button
                        type="button"
                        class="btn-del"
                        style="flex:1; justify-content:center;"
                        onclick="openDeleteModal({{ $instructor->id }}, '{{ addslashes($instructor->user->name) }}')"
                    >
                       <svg width="12" height="12" viewBox="0 0 24 24" style="stroke:currentColor; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round;">
                       <polyline points="3 6 5 6 21 6"/>
                       <path d="M19 6l-1 14H6L5 6"/>
                       <path d="M10 11v6M14 11v6"/>
                       <path d="M9 6V4h6v2"/>
                      </svg>
                        Deletar
                    </button>
                </div>
            </div>

        </div>
        @empty
        <div class="inst-empty" style="grid-column:1/-1;">Nenhum instrutor cadastrado.</div>
        @endforelse
    </div>

</div>

<div id="delete-modal" class="plan-modal-overlay" onclick="if(event.target===this)closeDeleteModal()">
    <div class="plan-modal" style="max-width:380px;">
        <div class="plan-modal__top" style="padding:22px 24px 18px;">
            <p class="plan-modal__kicker">Atenção</p>
            <h2 class="plan-modal__name" style="font-size:28px; margin-bottom:8px;">Deletar Instrutor</h2>
           <p class="delete-modal-text">
             Tem certeza que deseja remover <strong id="delete-modal-name"></strong>? Esta ação não pode ser desfeita.
        </p>
            <button class="plan-modal__close" onclick="closeDeleteModal()">✕</button>
        </div>
        <div class="delete-modal-actions">
           <form id="delete-form" method="POST" style="margin:0;">
               @csrf
              @method('DELETE')
             <button type="submit" class="btn-delete">Sim, deletar</button>
          </form>
         <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancelar</button>
       </div>
    </div>
</div>

<script>
function openDeleteModal(id, name) {
    document.getElementById('delete-modal-name').textContent = name;
    document.getElementById('delete-form').action = '/instructors/' + id;
    document.getElementById('delete-modal').classList.add('is-open');
    document.body.style.overflow = 'hidden';
}
function closeDeleteModal() {
    document.getElementById('delete-modal').classList.remove('is-open');
    document.body.style.overflow = '';
}
</script>
</x-app-layout>