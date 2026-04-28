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

            {{-- HERO --}}
            <div class="dash-hero" style="margin-bottom:1.25rem;">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Área do Aluno</div>
                        <h2 class="dash-hero__title">Meus Treinos</h2>
                        <p class="dash-hero__sub">
                            {{ $allWorkouts->count() }} treino{{ $allWorkouts->count() !== 1 ? 's' : '' }} cadastrado{{ $allWorkouts->count() !== 1 ? 's' : '' }}
                        </p>
                    </div>
                    <div class="dash-hero__right">
                        @php $st = Auth::user()->student?->status ?? 'active'; @endphp
                        @if($st === 'active')
                            <span class="dash-hero__pulse">
                                <span class="dash-hero__pulse-dot"></span>
                                FITPULSE ATIVO
                            </span>
                        @elseif($st === 'blocked')
                            <span class="dash-hero__pulse" style="background:rgba(214,21,50,.14);border-color:rgba(214,21,50,.28);color:#f87171;">
                                <span class="dash-hero__pulse-dot" style="background:#d61532;animation:none;"></span>
                                ACESSO BLOQUEADO
                            </span>
                        @else
                            <span class="dash-hero__pulse" style="background:rgba(251,191,36,.10);border-color:rgba(251,191,36,.25);color:#fbbf24;">
                                <span class="dash-hero__pulse-dot" style="background:#fbbf24;animation:none;"></span>
                                PAGAMENTO PENDENTE
                            </span>
                        @endif
                        <a href="{{ route('workouts.create') }}" class="btn-save"
                           style="text-decoration:none; display:inline-flex; align-items:center; gap:7px;">
                            <svg width="11" height="11" viewBox="0 0 12 12" fill="none"
                                 style="stroke:#fff; stroke-width:2.5; stroke-linecap:round;">
                                <line x1="6" y1="1" x2="6" y2="11"/>
                                <line x1="1" y1="6" x2="11" y2="6"/>
                            </svg>
                            Criar Treino
                        </a>
                    </div>
                </div>
            </div>

            @if($allWorkouts->isEmpty())
                {{-- EMPTY STATE --}}
                <div class="empty-state" style="padding:5rem 1rem;">
                    <svg width="56" height="56" viewBox="0 0 24 24" fill="none"
                         style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 18px; display:block; opacity:.20;">
                        <rect x="2" y="9" width="4" height="6" rx="1"/>
                        <rect x="18" y="9" width="4" height="6" rx="1"/>
                        <rect x="7" y="11" width="10" height="2" rx="1"/>
                    </svg>
                    <p>Nenhum treino disponível.</p>
                    <p style="font-size:13px; margin-top:6px; opacity:.45;">Crie seu primeiro treino para começar.</p>
                    <a href="{{ route('workouts.create') }}" class="btn-save"
                       style="text-decoration:none; display:inline-block; margin-top:20px;">
                        + Criar Primeiro Treino
                    </a>
                </div>
            @else

                {{-- SELETOR DE TREINOS --}}
                <div class="wkt-selector">
                    @foreach($allWorkouts as $w)
                        <a href="{{ route('workouts.index', ['workout_id' => $w->id]) }}"
                           class="wkt-selector__pill {{ (isset($workout) && $workout->id === $w->id) ? 'is-active' : '' }}">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                 style="stroke:currentColor; stroke-width:2; stroke-linecap:round;">
                                <rect x="2" y="9" width="4" height="6" rx="1"/>
                                <rect x="18" y="9" width="4" height="6" rx="1"/>
                                <rect x="7" y="11" width="10" height="2" rx="1"/>
                            </svg>
                            {{ $w->name }}
                            <span class="wkt-selector__count">{{ $w->workoutExercises->count() }}</span>
                        </a>
                    @endforeach
                </div>

                @if(isset($workout))
                    {{-- STATS DO TREINO SELECIONADO --}}
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
                                <span class="dash-stat__label">Descanso (s)</span>
                            </div>
                            <div class="dash-stat__value">{{ $exercises->sum(fn($e) => (int) $e->rest_time) }}</div>
                        </div>
                    </div>

                    {{-- HEADER DO TREINO --}}
                    <div class="exercises-header">
                        <div class="exercises-header__left">
                            <span class="exercises-header__tag">Treino selecionado</span>
                            <h3 class="exercises-header__name">{{ $workout->name }}</h3>
                            <span class="exercises-header__badge">{{ $exercises->count() }} exerc.</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <a href="{{ route('workouts.edit', $workout->id) }}" class="btn-ghost">
                                <svg viewBox="0 0 14 14" fill="none"
                                     style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; width:12px; height:12px;">
                                    <path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/>
                                </svg>
                                Editar
                            </a>
                            <form action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-del"
                                        onclick="return confirm('Deletar este treino?')">
                                    <svg viewBox="0 0 14 16" fill="none"
                                         style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; width:12px; height:12px;">
                                        <path d="M1 3.5h12M4.5 3.5V2a.5.5 0 01.5-.5h3a.5.5 0 01.5.5v1.5M5.5 7v5M8.5 7v5M2.5 3.5l.9 10a.5.5 0 00.5.5h6.2a.5.5 0 00.5-.5l.9-10"/>
                                    </svg>
                                    Deletar
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- GRID DE EXERCÍCIOS --}}
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
                                                    <rect x="2" y="9" width="4" height="6" rx="1"/>
                                                    <rect x="18" y="9" width="4" height="6" rx="1"/>
                                                    <rect x="7" y="11" width="10" height="2" rx="1"/>
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
                                            <svg viewBox="0 0 10 12"><polygon points="0,0 10,6 0,12"/></svg>
                                        </button>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-state"><p>Nenhum exercício neste treino.</p></div>
                    @endif
                @endif

            @endif

        </div>
    </div>
</x-app-layout>