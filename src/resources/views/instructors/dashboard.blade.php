<x-app-layout>
<div class="py-6">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

    @if(session('success'))
    <div style="margin-bottom:16px; padding:12px 16px; background:rgba(74,222,128,0.08); border:1px solid rgba(74,222,128,0.2); border-radius:10px; color:#4ade80; font-size:13px; font-weight:600;">
        {{ session('success') }}
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         VISÃO DO GERENTE
    ══════════════════════════════════════════════════ --}}
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

                    <div class="student-card__header">
                        <div class="student-avatar">{{ strtoupper(substr($student->user->name, 0, 2)) }}</div>
                        <div style="flex:1; min-width:0;">
                            <p class="student-card__name">{{ $student->user->name }}</p>
                            <p class="student-card__email">{{ $student->user->email }}</p>
                        </div>
                        <span class="badge-devedor {{ $student->is_defaulter ? 'badge-devedor--sim' : 'badge-devedor--nao' }}">
                            {{ $student->is_defaulter ? 'Devedor' : 'Em dia' }}
                        </span>
                    </div>

                    <div style="padding:10px 16px 0; display:flex; justify-content:flex-end;">
                        <a href="{{ route('workouts.create', ['student_id' => $student->id]) }}"
                           class="btn-save" style="text-decoration:none; font-size:11px; padding:5px 12px;">
                            + Criar treino
                        </a>
                    </div>

                    <div class="student-card__workouts">
                        @forelse($student->workouts as $workout)
                        <div class="workout-block">
                            <p class="workout-block__name">
                                {{ $workout->name }}
                                <span>{{ $workout->workoutExercises->count() }} exerc.</span>
                            </p>

                            <div style="display:flex; gap:6px; margin-bottom:10px;">
                                <a href="{{ route('workouts.edit', [$workout->id, 'student_id' => $student->id]) }}"
                                   class="btn-ghost" style="font-size:10px; padding:3px 10px;">✏️ Editar</a>
                                <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                    <button type="submit" class="btn-del" style="font-size:10px; padding:3px 10px;">
                                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                        Deletar
                                    </button>
                                </form>
                            </div>

                            <div class="workout-table-wrap">
                                <table class="exercise-table">
                                    <thead>
                                        <tr>
                                            <th>Exercício</th>
                                            <th>Grupo muscular</th>
                                            <th>Séries</th>
                                            <th>Reps</th>
                                            <th>Descanso (s)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($workout->workoutExercises as $we)
                                        <tr>
                                            <td>{{ $we->exercise->name }}</td>
                                            <td>{{ $we->exercise->muscle_group ?? '—' }}</td>
                                            <td><span class="chip-num">{{ $we->sets }}</span></td>
                                            <td><span class="chip-num">{{ $we->reps }}</span></td>
                                            <td>{{ $we->rest_time ?? '—' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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

    {{-- ══════════════════════════════════════════════════
         VISÃO DO INSTRUTOR
    ══════════════════════════════════════════════════ --}}
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

        <p class="my-students-title">Meus Alunos</p>

        <div class="students-grid">
            @forelse($instructor->students as $student)
            <div class="student-card {{ $student->is_defaulter ? 'student-card--bad' : 'student-card--ok' }}">

                <div class="student-card__header">
                    <div class="student-avatar">{{ strtoupper(substr($student->user->name, 0, 2)) }}</div>
                    <div style="flex:1; min-width:0;">
                        <p class="student-card__name">{{ $student->user->name }}</p>
                        <p class="student-card__email">{{ $student->user->email }}</p>
                    </div>
                    <span class="badge-devedor {{ $student->is_defaulter ? 'badge-devedor--sim' : 'badge-devedor--nao' }}">
                        {{ $student->is_defaulter ? 'Devedor' : 'Em dia' }}
                    </span>
                </div>

                <div style="padding:10px 16px 0; display:flex; justify-content:flex-end;">
                    <a href="{{ route('workouts.create', ['student_id' => $student->id]) }}"
                       class="btn-save" style="text-decoration:none; font-size:11px; padding:5px 12px;">
                        + Criar treino
                    </a>
                </div>

                <div class="student-card__workouts">
                    @forelse($student->workouts as $workout)
                    <div class="workout-block">
                        <p class="workout-block__name">
                            {{ $workout->name }}
                            <span>{{ $workout->workoutExercises->count() }} exerc.</span>
                        </p>

                        <div style="display:flex; gap:6px; margin-bottom:10px;">
                            <a href="{{ route('workouts.edit', [$workout->id, 'student_id' => $student->id]) }}"
                               class="btn-ghost" style="font-size:10px; padding:3px 10px;">✏️ Editar</a>
                            <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                <button type="submit" class="btn-del" style="font-size:10px; padding:3px 10px;">
                                    <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                    Deletar
                                </button>
                            </form>
                        </div>

                        <div class="workout-table-wrap">
                            <table class="exercise-table">
                                <thead>
                                    <tr>
                                        <th>Exercício</th>
                                        <th>Grupo muscular</th>
                                        <th>Séries</th>
                                        <th>Reps</th>
                                        <th>Descanso (s)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($workout->workoutExercises as $we)
                                    <tr>
                                        <td>{{ $we->exercise->name }}</td>
                                        <td>{{ $we->exercise->muscle_group ?? '—' }}</td>
                                        <td><span class="chip-num">{{ $we->sets }}</span></td>
                                        <td><span class="chip-num">{{ $we->reps }}</span></td>
                                        <td>{{ $we->rest_time ?? '—' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
</x-app-layout>