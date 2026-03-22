<x-app-layout>
@push('styles')
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endpush
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
    {{-- ══ HERO ══ --}}
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
                <a href="{{ route('workout.create') }}" class="btn-save"
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

    @if(isset($workout))
    
            <form action="{{ route('workout.destroy', $workout->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')

                <button type="submit"
                    style="color:red; margin-left:10px; background:none; border:none; cursor:pointer;">
                    🗑️ Deletar
                </button>
            </form>        

        {{-- ══ STAT CARDS ══ --}}
        <div class="dash-stats">
            <div class="dash-stat dash-stat--red">
                <div class="dash-stat__bg-icon">🏋️</div>
                <div class="dash-stat__header">
                    <span class="dash-stat__dot"></span>
                    <span class="dash-stat__label">Exercícios</span>
                </div>
                <div class="dash-stat__value">{{ $exercises->count() }}</div>
            </div>
            <div class="dash-stat dash-stat--blue">
                <div class="dash-stat__bg-icon">🔁</div>
                <div class="dash-stat__header">
                    <span class="dash-stat__dot"></span>
                    <span class="dash-stat__label">Séries totais</span>
                </div>
                <div class="dash-stat__value">{{ $exercises->sum('sets') }}</div>
            </div>
            <div class="dash-stat dash-stat--green">
                <div class="dash-stat__bg-icon">⚡</div>
                <div class="dash-stat__header">
                    <span class="dash-stat__dot"></span>
                    <span class="dash-stat__label">Reps totais</span>
                </div>
                <div class="dash-stat__value">{{ $exercises->sum('reps') }}</div>
            </div>
        </div>

        {{-- ══ CABEÇALHO EXERCÍCIOS ══ --}}
        <div class="exercises-header">
            <div class="exercises-header__left">
                <span class="exercises-header__tag">Treino atual</span>
                <h3 class="exercises-header__name">{{ $workout->name ?? 'Treino' }}</h3>
                <span class="exercises-header__badge">{{ $exercises->count() }} exerc.</span>
            </div>
            <a href="{{ route('workout.edit', $workout->id) }}" class="exercises-header__edit">
                <svg width="12" height="12" viewBox="0 0 14 14" fill="none"
                     style="stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;">
                    <path d="M9.5 2.5l2 2L4 12H2v-2L9.5 2.5z"/>
                </svg>
                Editar
            </a>
        </div>

        {{-- ══ GRADE LADO A LADO ══ --}}
        @if($exercises->count())
            <ul class="exercise-grid">
            @foreach($exercises as $item)
            <li class="exercise-grid-card">

                {{-- Thumb --}}
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

                {{-- Corpo --}}
                <div class="exercise-grid-card__body">
                    <div class="exercise-grid-card__name">{{ $item->exercise->name }}</div>
                    <div class="chips">
                        <span class="chip chip--series">{{ $item->sets }} séries</span>
                        <span class="chip chip--reps">{{ $item->reps }} reps</span>
                        <span class="chip chip--rest">{{ $item->rest_time ?? 0 }}s</span>
                    </div>
                </div>

                {{-- Rodapé --}}
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
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none"
                     style="stroke:var(--text-muted); stroke-width:1.2; margin:0 auto 12px; display:block; opacity:.25;">
                    <rect x="2" y="9" width="4" height="6" rx="1"/>
                    <rect x="18" y="9" width="4" height="6" rx="1"/>
                    <rect x="7" y="11" width="10" height="2" rx="1"/>
                </svg>
                <p>Nenhum exercício encontrado.</p>
            </div>
        @endif

    @else
        <div class="empty-state" style="padding:5rem 1rem;">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none"
                 style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 18px; display:block; opacity:.20;">
                <rect x="2" y="9" width="4" height="6" rx="1"/>
                <rect x="18" y="9" width="4" height="6" rx="1"/>
                <rect x="7" y="11" width="10" height="2" rx="1"/>
            </svg>
            <p>Nenhum treino disponível.</p>
            <p style="font-size:13px; margin-top:6px; opacity:.45;">Crie seu primeiro treino para começar.</p>
        </div>
    @endif

</div>
</div>

</x-app-layout>