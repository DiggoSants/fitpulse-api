<x-app-layout>
    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endpush

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div style="margin-bottom:16px; padding:12px 16px; background:rgba(74,222,128,0.08); border:1px solid rgba(74,222,128,0.2); border-radius:10px; color:#4ade80; font-size:13px; font-weight:600;">
                {{ session('success') }}
            </div>
            @endif

            {{-- ══════════════════════════════════════════════════════════════
                 VISÃO DO GERENTE
            ══════════════════════════════════════════════════════════════ --}}
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
                <div style="margin-bottom:32px;">
                    <div class="exercises-header">
                        <div class="exercises-header__left">
                            <span class="exercises-header__tag">Instrutor</span>
                            <h3 class="exercises-header__name">{{ $instructor->user->name }}</h3>
                            <span class="exercises-header__badge">{{ $instructor->students->count() }} aluno(s)</span>
                        </div>
                        <div style="display:flex; gap:8px;">
                            <a href="{{ route('instructors.edit', $instructor->id) }}" class="btn-ghost">✏️ Editar</a>
                        </div>
                    </div>

                    @forelse($instructor->students as $student)
                    <div style="margin-bottom:24px; padding-left:16px; border-left:2px solid var(--border);">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                            <div>
                                <span style="font-size:14px; font-weight:700; color:var(--text-white);">{{ $student->user->name }}</span>
                                <span style="font-size:12px; color:var(--text-muted); margin-left:8px;">{{ $student->user->email }}</span>
                                @if($student->is_defaulter)
                                    <span style="font-size:11px; font-weight:700; color:#ff4d6a; padding:2px 8px; border:1px solid rgba(255,77,106,.3); border-radius:20px; margin-left:8px;">DEVEDOR</span>
                                @endif
                            </div>
                            <a href="{{ route('workouts.create', ['student_id' => $student->id]) }}" class="btn-save" style="text-decoration:none; font-size:12px; padding:6px 12px;">
                                + Criar treino
                            </a>
                        </div>

                        @forelse($student->workouts as $workout)
                        <div style="margin-bottom:12px;">
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
                                <span style="font-size:13px; font-weight:600; color:var(--text-white);">{{ $workout->name }}</span>
                                <div style="display:flex; gap:6px;">
                                    <a href="{{ route('workouts.edit', [$workout->id, 'student_id' => $student->id]) }}" class="btn-ghost" style="font-size:11px; padding:4px 10px;">✏️ Editar</a>
                                    <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                                        <button type="submit" class="btn-del" style="font-size:11px; padding:4px 10px;">🗑</button>
                                    </form>
                                </div>
                            </div>
                            <ul class="exercise-grid" style="margin:0;">
                                @foreach($workout->workoutExercises as $we)
                                <li class="exercise-grid-card">
                                    <div class="exercise-grid-card__body">
                                        <div class="exercise-grid-card__name">{{ $we->exercise->name }}</div>
                                        <div class="chips">
                                            <span class="chip chip--series">{{ $we->sets }} séries</span>
                                            <span class="chip chip--reps">{{ $we->reps }} reps</span>
                                            <span class="chip chip--rest">{{ $we->rest_time ?? 0 }}s</span>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @empty
                            <p style="font-size:13px; color:var(--text-muted); opacity:.6;">Nenhum treino cadastrado.</p>
                        @endforelse
                    </div>
                    @empty
                        <p style="font-size:13px; color:var(--text-muted); opacity:.6; padding-left:16px;">Nenhum aluno vinculado.</p>
                    @endforelse
                </div>
                @empty
                    <div class="empty-state">
                        <p>Nenhum instrutor cadastrado.</p>
                    </div>
                @endforelse

            {{-- ══════════════════════════════════════════════════════════════
                 VISÃO DO INSTRUTOR
            ══════════════════════════════════════════════════════════════ --}}
            @elseif(Auth::user()->isInstructor())

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

                @forelse($instructor->students as $student)
                <div style="margin-bottom:24px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                        <div>
                            <span style="font-size:14px; font-weight:700; color:var(--text-white);">{{ $student->user->name }}</span>
                            <span style="font-size:12px; color:var(--text-muted); margin-left:8px;">{{ $student->user->email }}</span>
                            @if($student->is_defaulter)
                                <span style="font-size:11px; font-weight:700; color:#ff4d6a; padding:2px 8px; border:1px solid rgba(255,77,106,.3); border-radius:20px; margin-left:8px;">DEVEDOR</span>
                            @endif
                        </div>
                        <a href="{{ route('workouts.create', ['student_id' => $student->id]) }}" class="btn-save" style="text-decoration:none; font-size:12px; padding:6px 12px;">
                            + Criar treino
                        </a>
                    </div>

                    @forelse($student->workouts as $workout)
                    <div style="margin-bottom:12px; padding-left:16px; border-left:2px solid var(--border);">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
                            <span style="font-size:13px; font-weight:600; color:var(--text-white);">{{ $workout->name }}</span>
                            <div style="display:flex; gap:6px;">
                                <a href="{{ route('workouts.edit', [$workout->id, 'student_id' => $student->id]) }}" class="btn-ghost" style="font-size:11px; padding:4px 10px;">✏️ Editar</a>
                                <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                                    <button type="submit" class="btn-del" style="font-size:11px; padding:4px 10px;">🗑</button>
                                </form>
                            </div>
                        </div>
                        <ul class="exercise-grid" style="margin:0;">
                            @foreach($workout->workoutExercises as $we)
                            <li class="exercise-grid-card">
                                <div class="exercise-grid-card__body">
                                    <div class="exercise-grid-card__name">{{ $we->exercise->name }}</div>
                                    <div class="chips">
                                        <span class="chip chip--series">{{ $we->sets }} séries</span>
                                        <span class="chip chip--reps">{{ $we->reps }} reps</span>
                                        <span class="chip chip--rest">{{ $we->rest_time ?? 0 }}s</span>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @empty
                        <p style="font-size:13px; color:var(--text-muted); opacity:.6;">Nenhum treino cadastrado.</p>
                    @endforelse
                </div>
                @empty
                    <div class="empty-state">
                        <p>Nenhum aluno vinculado a você.</p>
                    </div>
                @endforelse

            {{-- ══════════════════════════════════════════════════════════════
                 VISÃO DO ALUNO
            ══════════════════════════════════════════════════════════════ --}}
            @else

                <div class="dash-hero">
                    <div class="dash-hero__ring"></div>
                    <div class="dash-hero__inner">
                        <div>
                            <div class="dash-hero__eyebrow">Bem-vindo de volta</div>
                            <h2 class="dash-hero__title">Seu Treino</h2>
                            <p class="dash-hero__sub">Pronto para mais um dia?</p>
                        </div>
                        <div class="dash-hero__right">
                            <span class="dash-hero__pulse">
                                <span class="dash-hero__pulse-dot"></span>
                                FITPULSE ATIVO
                            </span>
                            <a href="{{ route('workouts.create') }}" class="btn-save"
                                style="text-decoration:none; display:inline-flex; align-items:center; gap:7px;">
                                <svg width="11" height="11" viewBox="0 0 12 12" fill="none"
                                    style="stroke:#fff; stroke-width:2.5; stroke-linecap:round;">
                                    <line x1="6" y1="1" x2="6" y2="11" />
                                    <line x1="1" y1="6" x2="11" y2="6" />
                                </svg>
                                Criar Treino
                            </a>
                        </div>
                    </div>
                </div>

                @if(isset($workout))

                <div class="dash-stats">
                    <div class="dash-stat dash-stat--red">
                        <div class="dash-stat__bg-icon">⚡</div>
                        <div class="dash-stat__header">
                            <span class="dash-stat__dot"></span>
                            <span class="dash-stat__label">Séries totais</span>
                        </div>
                        <div class="dash-stat__value">{{ $exercises->sum(fn($e) => (int) $e->sets) }}</div>
                    </div>
                    <div class="dash-stat dash-stat--blue">
                        <div class="dash-stat__bg-icon">🔁</div>
                        <div class="dash-stat__header">
                            <span class="dash-stat__dot"></span>
                            <span class="dash-stat__label">Reps totais</span>
                        </div>
                        <div class="dash-stat__value">{{ $exercises->sum(fn($e) => (int) $e->reps) }}</div>
                    </div>
                    <div class="dash-stat dash-stat--green">
                        <div class="dash-stat__bg-icon">🏋️</div>
                        <div class="dash-stat__header">
                            <span class="dash-stat__dot"></span>
                            <span class="dash-stat__label">Descanso</span>
                        </div>
                        <div class="dash-stat__value">{{ $exercises->sum(fn($e) => (int) $e->rest_time) }}</div>
                    </div>
                </div>

                <div class="exercises-header">
                    <div class="exercises-header__left">
                        <span class="exercises-header__tag">Treino atual</span>
                        <h3 class="exercises-header__name">{{ $workout->name }}</h3>
                        <span class="exercises-header__badge">{{ $exercises->count() }} exerc.</span>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <a href="{{ route('workouts.edit', $workout->id) }}" class="btn-ghost">
                            <svg viewBox="0 0 14 14" fill="none"><path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z" /></svg>
                            Editar
                        </a>
                        <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-del">
                                <svg viewBox="0 0 14 16" fill="none">
                                    <path d="M1 3.5h12M4.5 3.5V2a.5.5 0 01.5-.5h3a.5.5 0 01.5.5v1.5M5.5 7v5M8.5 7v5M2.5 3.5l.9 10a.5.5 0 00.5.5h6.2a.5.5 0 00.5-.5l.9-10" />
                                </svg>
                                Deletar
                            </button>
                        </form>
                    </div>
                </div>

                @if($exercises->count())
                <ul class="exercise-grid">
                    @foreach($exercises as $item)
                    <li class="exercise-grid-card">
                        <div class="exercise-grid-card__thumb">
                            @if(!empty($item->exercise->image_url))
                                <img src="{{ $item->exercise->image_url }}" alt="{{ $item->exercise->name }}">
                            @else
                                <div class="exercise-grid-card__thumb-placeholder">
                                    <svg viewBox="0 0 24 24">
                                        <rect x="2" y="9" width="4" height="6" rx="1" />
                                        <rect x="18" y="9" width="4" height="6" rx="1" />
                                        <rect x="7" y="11" width="10" height="2" rx="1" />
                                    </svg>
                                    <span>{{ $item->exercise->muscle_group ?? 'Exercício' }}</span>
                                </div>
                            @endif
                            <span class="exercise-grid-card__num">{{ $loop->iteration }}</span>
                        </div>
                        <div class="exercise-grid-card__body">
                            <div class="exercise-grid-card__name">{{ $item->exercise->name }}</div>
                            <div class="chips">
                                <span class="chip chip--series">{{ $item->sets }} séries</span>
                                <span class="chip chip--reps">{{ $item->reps }} reps</span>
                                <span class="chip chip--rest">{{ $item->rest_time ?? 0 }}s</span>
                            </div>
                        </div>
                        <div class="exercise-grid-card__footer">
                            <span style="font-size:11px; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.07em;">
                                {{ $item->exercise->muscle_group ?? '' }}
                            </span>
                            <button class="btn-play" title="Iniciar">
                                <svg viewBox="0 0 10 12"><polygon points="0,0 10,6 0,12" /></svg>
                            </button>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="empty-state">
                    <p>Nenhum exercício encontrado.</p>
                </div>
                @endif

                @else
                <div class="empty-state" style="padding:5rem 1rem;">
                    <svg width="56" height="56" viewBox="0 0 24 24" fill="none"
                        style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 18px; display:block; opacity:.20;">
                        <rect x="2" y="9" width="4" height="6" rx="1" />
                        <rect x="18" y="9" width="4" height="6" rx="1" />
                        <rect x="7" y="11" width="10" height="2" rx="1" />
                    </svg>
                    <p>Nenhum treino disponível.</p>
                    <p style="font-size:13px; margin-top:6px; opacity:.45;">Crie seu primeiro treino para começar.</p>
                </div>
                @endif

            @endif {{-- fim @if isManager / elseif isInstructor / else --}}

        </div>
    </div>
</x-app-layout>