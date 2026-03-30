<x-app-layout>
<div class="py-6">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

    @if(session('success'))
    <div style="margin-bottom:16px; padding:12px 16px; background:rgba(74,222,128,0.08); border:1px solid rgba(74,222,128,0.2); border-radius:10px; color:#4ade80; font-size:13px; font-weight:600;">
        {{ session('success') }}
    </div>
    @endif

    {{-- VISÃO DO GERENTE --}}
    @if(Auth::user()->isManager())

        <div class="dash-hero">
            <div class="dash-hero__ring"></div>
            <div class="dash-hero__inner">
                <div>
                    <div class="dash-hero__eyebrow">Gerenciamento</div>
                    <h2 class="dash-hero__title">Painel Geral</h2>
                    <p class="dash-hero__sub">Visão completa de instrutores e alunos</p>
                </div>
                <div class="dash-hero__right">
                    <span class="dash-hero__pulse">
                        <span class="dash-hero__pulse-dot"></span>
                        GERENTE
                    </span>
                    <a href="{{ route('instructors.create') }}" class="btn-save"
                        style="text-decoration:none; display:inline-flex; align-items:center; gap:7px;">
                        + Novo Instrutor
                    </a>
                </div>
            </div>
        </div>

        @forelse($instructors as $instructor)
        <div class="inst-section">
            <div class="inst-section__header">
                <div class="inst-avatar-sm">{{ strtoupper(substr($instructor->user->name, 0, 2)) }}</div>
                <h3 class="inst-section__name">{{ $instructor->user->name }}</h3>
                <span class="inst-section__count">{{ $instructor->students->count() }} aluno(s)</span>
                <a href="{{ route('instructors.edit', $instructor->id) }}" class="btn-ghost" style="font-size:11px; padding:5px 14px;">✏️ Editar</a>
            </div>

            <div class="students-grid">
                @forelse($instructor->students as $student)
                <div class="student-card {{ $student->is_defaulter ? 'student-card--bad' : 'student-card--ok' }}">

                    <div class="student-card__header" style="padding:20px 20px 16px; gap:14px;">
                        <div class="student-avatar">{{ strtoupper(substr($student->user->name, 0, 2)) }}</div>
                        <div style="flex:1; min-width:0;">
                            <p class="student-card__name">{{ $student->user->name }}</p>
                            <p class="student-card__email">{{ $student->user->email }}</p>
                        </div>
                        <span class="badge-devedor {{ $student->is_defaulter ? 'badge-devedor--sim' : 'badge-devedor--nao' }}">
                            {{ $student->is_defaulter ? 'Devedor' : 'Em dia' }}
                        </span>
                    </div>

                    <div style="padding:0 20px 20px; display:flex; justify-content:flex-start; gap:10px; margin-top:12px;">
                        <a href="{{ route('workouts.create', ['student_id' => $student->id]) }}"
                           class="btn-save" style="text-decoration:none; font-size:11px; padding:7px 14px;">
                            + Criar treino
                        </a>
                        @if($student->workouts->count() > 0)
                        <button
                            type="button"
                            class="btn-ghost btn-toggle-workouts"
                            style="font-size:11px; padding:7px 14px; cursor:pointer; display:inline-flex; align-items:center; gap:5px;"
                            onclick="toggleWorkouts(this)"
                            data-open="0">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="toggle-chevron" style="transition:transform .25s;">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                            Ver treinos
                        </button>
                        @endif
                    </div>

                    <div class="student-card__workouts" style="display:none;">
                        @forelse($student->workouts as $workout)
                        <div class="workout-block">
                            <p class="workout-block__name">
                                {{ $workout->name }}
                                <span>{{ $workout->workoutExercises->count() }} exerc.</span>
                            </p>

                            <div style="display:flex; gap:6px; margin-bottom:10px;">
                                <a href="{{ route('workouts.edit', [$workout->id, 'student_id' => $student->id]) }}"
                                   class="btn-workout-action btn-workout-action--edit">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    Editar
                                </a>
                                <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                    <button type="submit" class="btn-workout-action btn-workout-action--del">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                        Deletar
                                    </button>
                                </form>
                            </div>

                            <div class="ex-table">
                                <div class="ex-table__head">
                                    <span>Exercício</span>
                                    <span>Grupo</span>
                                    <span>Séries</span>
                                    <span>Reps</span>
                                    <span>Desc.</span>
                                </div>
                                @foreach($workout->workoutExercises as $we)
                                <div class="ex-table__row">
                                    <span class="ex-table__name">{{ $we->exercise->name }}</span>
                                    <span class="ex-table__group">{{ $we->exercise->muscle_group ?? '—' }}</span>
                                    <span><span class="chip-num">{{ $we->sets }}</span></span>
                                    <span><span class="chip-num">{{ $we->reps }}</span></span>
                                    <span class="ex-table__group">{{ $we->rest_time ? $we->rest_time.'s' : '—' }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @empty
                        <div class="workout-empty">Nenhum treino cadastrado para este aluno.</div>
                        @endforelse
                    </div>

                </div>
                @empty
                <div class="inst-empty">Nenhum aluno vinculado a este instrutor.</div>
                @endforelse
            </div>
        </div>
        @empty
        <div class="inst-empty">Nenhum instrutor cadastrado.</div>
        @endforelse

    {{--VISÃO DO INSTRUTOR--}}
    @else

        <div class="dash-hero">
            <div class="dash-hero__ring"></div>
            <div class="dash-hero__inner">
                <div>
                    <div class="dash-hero__eyebrow">Bem-vindo de volta</div>
                    <h2 class="dash-hero__title">Meus Alunos</h2>
                    <p class="dash-hero__sub">{{ $instructor->specialty ?? 'Instrutor' }}</p>
                </div>
                <div class="dash-hero__right">
                    <span class="dash-hero__pulse">
                        <span class="dash-hero__pulse-dot"></span>
                        INSTRUTOR
                    </span>
                </div>
            </div>
        </div>

        <p class="my-students-title">Meus alunos</p>

        {{-- SKELETON: exibido enquanto a página carrega --}}
        <div class="students-grid" id="skeleton-grid">
            @for($i = 0; $i < 3; $i++)
            <div class="student-card student-card--skeleton student-card--ok">
                <div class="student-card__header" style="padding:20px 20px 16px; gap:14px; border-bottom:1px solid rgba(255,255,255,.06);">
                    <div class="sk sk-avatar" style="flex-shrink:0;"></div>
                    <div style="flex:1; display:flex; flex-direction:column; gap:8px; min-width:0;">
                        <div class="sk sk-name"></div>
                        <div class="sk sk-text-sm"></div>
                    </div>
                    <div class="sk sk-badge"></div>
                </div>
                <div style="padding:12px 20px 20px; display:flex; gap:10px;">
                    <div class="sk" style="height:32px; width:110px; border-radius:999px;"></div>
                    <div class="sk" style="height:32px; width:110px; border-radius:999px;"></div>
                </div>
            </div>
            @endfor
        </div>

        {{-- CONTEÚDO REAL: revelado após breve delay --}}
        <div class="students-grid" id="real-grid" style="display:none;">
            @forelse($instructor->students as $student)
            <div class="student-card {{ $student->is_defaulter ? 'student-card--bad' : 'student-card--ok' }}">

                <div class="student-card__header" style="padding:20px 20px 16px; gap:14px;">
                    <div class="student-avatar">{{ strtoupper(substr($student->user->name, 0, 2)) }}</div>
                    <div style="flex:1; min-width:0;">
                        <p class="student-card__name">{{ $student->user->name }}</p>
                        <p class="student-card__email">{{ $student->user->email }}</p>
                    </div>
                    <span class="badge-devedor {{ $student->is_defaulter ? 'badge-devedor--sim' : 'badge-devedor--nao' }}">
                        {{ $student->is_defaulter ? 'Devedor' : 'Em dia' }}
                    </span>
                </div>

                <div style="padding:0 20px 20px; display:flex; justify-content:flex-start; gap:10px; margin-top:12px;">
                    <a href="{{ route('workouts.create', ['student_id' => $student->id]) }}"
                       class="btn-save" style="text-decoration:none; font-size:11px; padding:7px 14px;">
                        + Criar treino
                    </a>
                    @if($student->workouts->count() > 0)
                    <button
                        type="button"
                        class="btn-ghost btn-toggle-workouts"
                        style="font-size:11px; padding:7px 14px; cursor:pointer; display:inline-flex; align-items:center; gap:5px;"
                        onclick="toggleWorkouts(this)"
                        data-open="0">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="toggle-chevron" style="transition:transform .25s;">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                        Ver treinos
                    </button>
                    @endif
                </div>

                <div class="student-card__workouts" style="display:none;">
                    @forelse($student->workouts as $workout)
                    <div class="workout-block">
                        <p class="workout-block__name">
                            {{ $workout->name }}
                            <span>{{ $workout->workoutExercises->count() }} exerc.</span>
                        </p>

                        <div style="display:flex; gap:6px; margin-bottom:10px;">
                            <a href="{{ route('workouts.edit', [$workout->id, 'student_id' => $student->id]) }}"
                               class="btn-workout-action btn-workout-action--edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Editar
                            </a>
                            <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                <button type="submit" class="btn-workout-action btn-workout-action--del">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="12" height="12"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                    Deletar
                                </button>
                            </form>
                        </div>

                        <div class="ex-table">
                            <div class="ex-table__head">
                                <span>Exercício</span>
                                <span>Grupo</span>
                                <span>Séries</span>
                                <span>Reps</span>
                                <span>Desc.</span>
                            </div>
                            @foreach($workout->workoutExercises as $we)
                            <div class="ex-table__row">
                                <span class="ex-table__name">{{ $we->exercise->name }}</span>
                                <span class="ex-table__group">{{ $we->exercise->muscle_group ?? '—' }}</span>
                                <span><span class="chip-num">{{ $we->sets }}</span></span>
                                <span><span class="chip-num">{{ $we->reps }}</span></span>
                                <span class="ex-table__group">{{ $we->rest_time ? $we->rest_time.'s' : '—' }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div class="workout-empty">Nenhum treino cadastrado para este aluno.</div>
                    @endforelse
                </div>

            </div>
            @empty
            <div class="inst-empty">Nenhum aluno vinculado a você.</div>
            @endforelse
        </div>

    @endif

</div>
</div>

<script>
function toggleWorkouts(btn) {
    const card = btn.closest('.student-card');
    const panel = card.querySelector('.student-card__workouts');
    const chevron = btn.querySelector('.toggle-chevron');
    const isOpen = btn.dataset.open === '1';

    if (isOpen) {
        panel.style.display = 'none';
        btn.dataset.open = '0';
        btn.lastChild.textContent = ' Ver treinos';
        chevron.style.transform = 'rotate(0deg)';
    } else {
        panel.style.display = 'block';
        btn.dataset.open = '1';
        btn.lastChild.textContent = ' Ocultar treinos';
        chevron.style.transform = 'rotate(180deg)';
    }
}

// Skeleton
(function() {
    var sk = document.getElementById('skeleton-grid');
    var real = document.getElementById('real-grid');
    if (!sk || !real) return;

    // Simula tempo de carregamento 
    setTimeout(function() {
        sk.style.transition = 'opacity .3s';
        sk.style.opacity = '0';
        setTimeout(function() {
            sk.style.display = 'none';
            real.style.display = 'grid';
            real.style.opacity = '0';
            real.style.transition = 'opacity .3s';
            setTimeout(function() { real.style.opacity = '1'; }, 20);
        }, 300);
    }, 600);
})();
</script>

</x-app-layout>