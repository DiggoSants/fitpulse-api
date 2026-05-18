<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div style="margin-bottom:16px; padding:12px 16px; background:rgba(74,222,128,0.08); border:1px solid rgba(74,222,128,0.2); border-radius:10px; color:#4ade80; font-size:13px; font-weight:600;">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('info'))
                <div style="margin-bottom:16px; padding:12px 16px; background:rgba(59,130,246,0.08); border:1px solid rgba(59,130,246,0.2); border-radius:10px; color:#93c5fd; font-size:13px; font-weight:600;">
                    {{ session('info') }}
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
                        <span style="max-width:280px; font-size:12px; line-height:1.5; color:var(--text-muted); text-align:right;">
                            Quer ajustar seu treino? Fale com seu instrutor para manter tudo alinhado com sua evolução.
                        </span>
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
                    <p style="font-size:13px; margin-top:6px; opacity:.45;">Seu instrutor vai montar seu treino por aqui. Se já combinou um plano, peça para ele liberar na plataforma.</p>
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
                            <div class="dash-stat__bg-icon"></div>
                            <div class="dash-stat__header">
                                <span class="dash-stat__dot"></span>
                                <span class="dash-stat__label">Séries totais</span>
                            </div>
                            <div class="dash-stat__value">{{ $exercises->sum(fn($e) => (int) $e->sets) }}</div>
                        </div>
                        <div class="dash-stat dash-stat--blue">
                            <div class="dash-stat__bg-icon"></div>
                            <div class="dash-stat__header">
                                <span class="dash-stat__dot"></span>
                                <span class="dash-stat__label">Reps totais</span>
                            </div>
                            <div class="dash-stat__value">{{ $exercises->sum(fn($e) => (int) $e->reps) }}</div>
                        </div>
                        <div class="dash-stat dash-stat--green">
                            <div class="dash-stat__bg-icon"></div>
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
                        <div style="max-width:300px; font-size:12px; line-height:1.5; color:var(--text-muted); text-align:right;">
                            Edição bloqueada para alunos. Peça ajustes ao seu instrutor quando quiser evoluir o plano.
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
                                        {{-- BOTÃO PLAY com nome do exercício --}}
                                        <button
                                            class="btn-play"
                                            title="Ver tutorial"
                                            onclick="openExerciseModal('{{ addslashes($item->exercise->name) }}', {{ (int)$item->sets }}, {{ (int)$item->reps }}, {{ (int)($item->rest_time ?? 0) }})"
                                        >
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

    {{-- ══════════════════════════════════════════════════════
         MODAL DE TUTORIAL DO EXERCÍCIO
    ══════════════════════════════════════════════════════ --}}
    <div id="exercise-modal" style="
        display:none;
        position:fixed; inset:0; z-index:9999;
        background:rgba(0,0,0,0.88);
        align-items:center; justify-content:center;
        padding:16px;
    ">
        <div class="ex-modal-box">
            {{-- Fechar --}}
            <button onclick="closeExerciseModal()" class="ex-modal-close">✕</button>

            {{-- Nome do exercício --}}
            <p id="modal-exercise-name" class="ex-modal-title"></p>

            {{-- Stats: séries / reps / descanso --}}
            <div class="ex-modal-stats">
                <div class="ex-modal-stat ex-modal-stat--red">
                    <span class="ex-modal-stat__value" id="modal-sets">—</span>
                    <span class="ex-modal-stat__label">séries</span>
                </div>
                <div class="ex-modal-stat-divider"></div>
                <div class="ex-modal-stat ex-modal-stat--blue">
                    <span class="ex-modal-stat__value" id="modal-reps">—</span>
                    <span class="ex-modal-stat__label">repetições</span>
                </div>
                <div class="ex-modal-stat-divider"></div>
                <div class="ex-modal-stat ex-modal-stat--green">
                    <span class="ex-modal-stat__value" id="modal-rest">—</span>
                    <span class="ex-modal-stat__label">descanso</span>
                </div>
            </div>

            {{-- Loading --}}
            <div id="modal-loading" style="text-align:center; color:var(--text-muted); padding:48px 0;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                     style="stroke:#f87171; stroke-width:2; stroke-linecap:round; margin:0 auto 12px; display:block; animation:spin 1s linear infinite;">
                    <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                </svg>
                Buscando tutorial...
            </div>

            {{-- Vídeo --}}
            <div id="modal-video" style="display:none;">
                <iframe
                    id="yt-iframe"
                    width="100%" height="370"
                    frameborder="0" allowfullscreen
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    style="border-radius:12px; display:block;"
                ></iframe>
            </div>

            {{-- Erro --}}
            <div id="modal-error" style="display:none; text-align:center; padding:48px 0;">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                     style="stroke:#f87171; stroke-width:1.8; stroke-linecap:round; margin:0 auto 12px; display:block; opacity:.6;">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <p style="color:#f87171; font-size:13px;">Nenhum vídeo encontrado para este exercício.</p>
            </div>
        </div>
    </div>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── MODAL BOX ── */
        .ex-modal-box {
            background: #111;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px;
            padding: 24px;
            width: 100%; max-width: 740px;
            position: relative;
            box-shadow: 0 24px 64px rgba(0,0,0,0.6);
        }
        [data-theme="light"] .ex-modal-box {
            background: #fff;
            border-color: rgba(0,0,0,0.10);
            box-shadow: 0 24px 64px rgba(0,0,0,0.18);
        }

        /* ── FECHAR ── */
        .ex-modal-close {
            position: absolute; top: 14px; right: 16px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.10);
            color: #fff; width: 32px; height: 32px;
            border-radius: 50%; font-size: 16px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: background .15s;
        }
        .ex-modal-close:hover { background: rgba(255,255,255,0.12); }
        [data-theme="light"] .ex-modal-close {
            background: rgba(0,0,0,0.05);
            border-color: rgba(0,0,0,0.10);
            color: #333;
        }
        [data-theme="light"] .ex-modal-close:hover { background: rgba(0,0,0,0.10); }

        /* ── TÍTULO ── */
        .ex-modal-title {
            font-size: 17px; font-weight: 800; color: #fff;
            margin: 0 40px 16px 0; letter-spacing: -.01em;
        }
        [data-theme="light"] .ex-modal-title { color: #111; }

        /* ── STATS BAR ── */
        .ex-modal-stats {
            display: flex;
            align-items: center;
            gap: 0;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 14px;
            padding: 14px 20px;
            margin-bottom: 18px;
        }
        [data-theme="light"] .ex-modal-stats {
            background: rgba(0,0,0,0.03);
            border-color: rgba(0,0,0,0.08);
        }

        .ex-modal-stat {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }

        .ex-modal-stat__value {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 32px;
            letter-spacing: 1px;
            line-height: 1;
        }
        .ex-modal-stat--red  .ex-modal-stat__value { color: #f87171; }
        .ex-modal-stat--blue .ex-modal-stat__value { color: #60a5fa; }
        .ex-modal-stat--green .ex-modal-stat__value { color: #4ade80; }

        [data-theme="light"] .ex-modal-stat--red  .ex-modal-stat__value { color: #dc2626; }
        [data-theme="light"] .ex-modal-stat--blue .ex-modal-stat__value { color: #2563eb; }
        [data-theme="light"] .ex-modal-stat--green .ex-modal-stat__value { color: #16a34a; }

        .ex-modal-stat__label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .10em;
            color: var(--text-muted);
        }
        [data-theme="light"] .ex-modal-stat__label { color: rgba(0,0,0,0.40); }

        .ex-modal-stat-divider {
            width: 1px;
            height: 36px;
            background: rgba(255,255,255,0.07);
            flex-shrink: 0;
        }
        [data-theme="light"] .ex-modal-stat-divider { background: rgba(0,0,0,0.08); }

        .exercise-grid-card__thumb {
    aspect-ratio: 1/1 !important;
    background: #0a0a0a !important;
}
.exercise-grid-card__thumb img {
    object-fit: contain !important;
}
[data-theme="light"] .exercise-grid-card__thumb {
    background: #f5f5f5 !important;
}
    </style>

    <script>
    const EXERCISE_VIDEO_URL = "{{ route('exercise.video', [], false) }}";

    async function openExerciseModal(exerciseName, sets, reps, rest) {
        const modal = document.getElementById('exercise-modal');
        modal.style.display = 'flex';

        document.getElementById('modal-exercise-name').textContent = exerciseName;
        document.getElementById('modal-sets').textContent  = sets  || '—';
        document.getElementById('modal-reps').textContent  = reps  || '—';
        document.getElementById('modal-rest').textContent  = rest ? rest + 's' : '—';
        document.getElementById('modal-loading').style.display = 'block';
        document.getElementById('modal-video').style.display   = 'none';
        document.getElementById('modal-error').style.display   = 'none';
        document.getElementById('yt-iframe').src               = '';

        try {
            const res  = await fetch(EXERCISE_VIDEO_URL + '?q=' + encodeURIComponent(exerciseName), {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) throw new Error('Erro ao carregar video.');
            const data = await res.json();

            document.getElementById('modal-loading').style.display = 'none';

            if (data.video_id) {
                document.getElementById('yt-iframe').src =
                    'https://www.youtube.com/embed/' + data.video_id + '?autoplay=1&rel=0';
                document.getElementById('modal-video').style.display = 'block';
            } else {
                document.getElementById('modal-error').style.display = 'block';
            }
        } catch (e) {
            document.getElementById('modal-loading').style.display = 'none';
            document.getElementById('modal-error').style.display   = 'block';
        }
    }

    function closeExerciseModal() {
        document.getElementById('exercise-modal').style.display = 'none';
        document.getElementById('yt-iframe').src = '';
    }

    document.getElementById('exercise-modal').addEventListener('click', function (e) {
        if (e.target === this) closeExerciseModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeExerciseModal();
    });
    </script>
</x-app-layout>
