<x-app-layout>
    @push('styles')
        <style>
            /* ─── Tokens ─────────────────────────────────────────── */
            :root {
                --red:       #d61532;
                --red-dim:   rgba(214,21,50,.12);
                --red-ring:  rgba(214,21,50,.28);
                --green:     #4ade80;
                --amber:     #fbbf24;
                --blue-chip: #bfdbfe;
                --surface:   rgba(255,255,255,.04);
                --border:    rgba(255,255,255,.08);
                --muted:     var(--text-muted);
            }
            [data-theme="light"] {
                --surface: #fff;
                --border:  rgba(0,0,0,.08);
                --red-dim: rgba(214,21,50,.07);
            }

            /* ─── Hero ───────────────────────────────────────────── */
            .ia-hero {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 16px;
                flex-wrap: wrap;
                margin-bottom: 28px;
            }
            .ia-hero__eyebrow {
                font-size: 11px;
                font-weight: 800;
                letter-spacing: .12em;
                text-transform: uppercase;
                color: var(--red);
                margin-bottom: 4px;
            }
            .ia-hero__title {
                margin: 0;
                font-size: 26px;
                font-weight: 900;
                color: var(--text-white);
                letter-spacing: -.01em;
            }
            .ia-hero__sub {
                margin: 4px 0 0;
                font-size: 13px;
                color: var(--muted);
                font-weight: 600;
            }
            .ia-hero__actions {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-shrink: 0;
            }

            /* ─── Stats strip ────────────────────────────────────── */
            .ia-stats {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 12px;
                margin-bottom: 24px;
            }
            .inst-agenda-stats {
                grid-template-columns: repeat(4, 1fr);
                margin-bottom: 24px;
            }
            .ia-stat {
                border: 1px solid var(--border);
                border-radius: 14px;
                background: var(--surface);
                padding: 14px 16px;
                display: flex;
                flex-direction: column;
                gap: 2px;
            }
            .ia-stat__label {
                font-size: 10px;
                font-weight: 800;
                letter-spacing: .09em;
                text-transform: uppercase;
                color: var(--muted);
            }
            .ia-stat__value {
                font-size: 26px;
                font-weight: 900;
                color: var(--text-white);
                line-height: 1.1;
            }
            .ia-stat__value--red    { color: var(--red); }
            .ia-stat__value--green  { color: var(--green); }
            .ia-stat__value--amber  { color: var(--amber); }
            .ia-stat__sub {
                font-size: 11px;
                color: var(--muted);
                font-weight: 600;
            }
            [data-theme="light"] .ia-stat__value { color: #111827; }

            /* ─── Tab shell ──────────────────────────────────────── */
            .ia-shell {
                border: 1px solid var(--border);
                border-radius: 18px;
                background: var(--surface);
                overflow: hidden;
            }

            /* ─── Tab nav ────────────────────────────────────────── */
            .ia-tabs {
                display: flex;
                border-bottom: 1px solid var(--border);
                background: rgba(0,0,0,.12);
                overflow-x: auto;
                scrollbar-width: none;
            }
            .ia-tabs::-webkit-scrollbar { display: none; }

            .ia-tab {
                flex-shrink: 0;
                padding: 15px 22px;
                font-size: 12px;
                font-weight: 800;
                letter-spacing: .07em;
                text-transform: uppercase;
                color: var(--muted);
                background: transparent;
                border: none;
                border-bottom: 2px solid transparent;
                cursor: pointer;
                transition: color .18s, border-color .18s;
                display: flex;
                align-items: center;
                gap: 8px;
                white-space: nowrap;
                margin-bottom: -1px;
            }
            .ia-tab:hover { color: var(--text-white); }
            .ia-tab.ia-tab--active {
                color: var(--text-white);
                border-bottom-color: var(--red);
            }
            .ia-tab__badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 18px;
                height: 18px;
                padding: 0 5px;
                border-radius: 99px;
                background: var(--red-dim);
                border: 1px solid var(--red-ring);
                color: #f87171;
                font-size: 10px;
                font-weight: 900;
            }
            [data-theme="light"] .ia-tabs { background: rgba(0,0,0,.03); }
            [data-theme="light"] .ia-tab.ia-tab--active { color: #111827; }

            /* ─── Panels ─────────────────────────────────────────── */
            .ia-panel { display: none; }
            .ia-panel.ia-panel--active { display: block; }

            /* ─── Panel: Disponibilidade ─────────────────────────── */
            .ia-avail {
                padding: 24px;
            }
            .ia-avail-hint {
                font-size: 13px;
                color: var(--muted);
                margin: 0 0 20px;
                font-weight: 600;
            }
            .ia-avail-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                gap: 14px;
            }
            .ia-avail-day {
                border: 1px solid var(--border);
                border-radius: 14px;
                padding: 16px;
                background: rgba(0,0,0,.10);
            }
            [data-theme="light"] .ia-avail-day { background: rgba(0,0,0,.03); }
            .ia-avail-day__name {
                font-size: 11px;
                font-weight: 900;
                letter-spacing: .10em;
                text-transform: uppercase;
                color: var(--text-white);
                margin: 0 0 14px;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            [data-theme="light"] .ia-avail-day__name { color: #111827; }
            .ia-avail-day__name::before {
                content: '';
                display: inline-block;
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: var(--red);
                flex-shrink: 0;
            }
            .ia-shift-list {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .ia-shift {
                border: 1px solid var(--border);
                border-radius: 10px;
                padding: 10px 12px;
                background: rgba(255,255,255,.025);
                transition: border-color .15s, background .15s;
            }
            [data-theme="light"] .ia-shift { background: #f9fafb; }
            .ia-shift:has(input:checked) {
                border-color: var(--red-ring);
                background: var(--red-dim);
            }
            .ia-shift-check {
                display: flex;
                align-items: center;
                gap: 9px;
                cursor: pointer;
                font-size: 12px;
                font-weight: 800;
                color: var(--text-white);
                user-select: none;
            }
            [data-theme="light"] .ia-shift-check { color: #111827; }
            .ia-shift-check input[type="checkbox"] {
                width: 15px;
                height: 15px;
                accent-color: var(--red);
                flex-shrink: 0;
            }
            .ia-shift-times {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 6px;
                margin-top: 8px;
                padding-top: 8px;
                border-top: 1px solid var(--border);
            }
            .ia-shift-times input[type="time"] {
                width: 100%;
                min-width: 0;
                background: rgba(0,0,0,.18);
                border: 1px solid var(--border);
                border-radius: 8px;
                color: var(--text-white);
                font-size: 11px;
                font-weight: 700;
                padding: 7px 9px;
                font-family: inherit;
                color-scheme: dark;
                line-height: 1.2;
            }
            .ia-shift-times input[type="time"]::-webkit-calendar-picker-indicator {
                filter: invert(1);
                opacity: .75;
            }
            [data-theme="light"] .ia-shift-times input[type="time"] {
                background: #fff;
                color: #111827;
                border-color: rgba(0,0,0,.12);
                color-scheme: light;
            }
            [data-theme="light"] .ia-shift-times input[type="time"]::-webkit-calendar-picker-indicator {
                filter: none;
            }
            .ia-avail-footer {
                margin-top: 22px;
                display: flex;
                justify-content: flex-end;
            }

            /* ─── Panel: Semana ──────────────────────────────────── */
            .ia-week {
                padding: 20px;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }
            .ia-day-row {
                border: 1px solid var(--border);
                border-radius: 14px;
                overflow: hidden;
            }
            .ia-day-head {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 13px 16px;
                background: rgba(0,0,0,.12);
                border-bottom: 1px solid var(--border);
            }
            [data-theme="light"] .ia-day-head { background: rgba(0,0,0,.04); }
            .ia-day-abbr {
                width: 38px;
                height: 38px;
                border-radius: 10px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                background: var(--red-dim);
                border: 1px solid var(--red-ring);
                color: #f87171;
                font-size: 11px;
                font-weight: 900;
                text-transform: uppercase;
            }
            .ia-day-info strong {
                display: block;
                color: var(--text-white);
                font-size: 13px;
                font-weight: 800;
            }
            [data-theme="light"] .ia-day-info strong { color: #111827; }
            .ia-day-info span {
                display: block;
                color: var(--muted);
                font-size: 11px;
                margin-top: 2px;
            }
            .ia-slots-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 10px;
                padding: 14px 16px;
            }
            .ia-slot {
                border: 1px solid var(--border);
                border-radius: 11px;
                padding: 12px 13px;
                background: rgba(255,255,255,.025);
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .ia-slot--free     { border-color: rgba(74,222,128,.22); background: rgba(74,222,128,.05); }
            .ia-slot--occupied { border-color: rgba(251,191,36,.24); background: rgba(251,191,36,.06); }
            .ia-slot--unavailable { opacity: .55; }
            .ia-slot-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
            }
            .ia-slot-top strong {
                font-size: 12px;
                font-weight: 900;
                color: var(--text-white);
            }
            [data-theme="light"] .ia-slot-top strong { color: #111827; }
            .ia-slot-time {
                font-size: 11px;
                color: var(--muted);
                font-weight: 600;
            }
            .ia-badge {
                display: inline-flex;
                align-items: center;
                padding: 3px 9px;
                border-radius: 99px;
                font-size: 9px;
                font-weight: 900;
                letter-spacing: .07em;
                text-transform: uppercase;
                white-space: nowrap;
            }
            .ia-badge--free        { color: var(--green); background: rgba(74,222,128,.10); border: 1px solid rgba(74,222,128,.22); }
            .ia-badge--occupied    { color: var(--amber); background: rgba(251,191,36,.11); border: 1px solid rgba(251,191,36,.24); }
            .ia-badge--unavailable { color: var(--muted); background: rgba(255,255,255,.05); border: 1px solid var(--border); }
            .ia-chips {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }
            .ia-chip {
                display: inline-flex;
                align-items: center;
                padding: 4px 9px;
                border-radius: 99px;
                border: 1px solid rgba(147,197,253,.20);
                background: rgba(59,130,246,.08);
                color: var(--blue-chip);
                font-size: 10px;
                font-weight: 800;
                max-width: 180px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            /* ─── Panel: Próximos ────────────────────────────────── */
            .ia-next {
                padding: 20px;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .ia-next-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                padding: 14px 16px;
                border: 1px solid var(--border);
                border-radius: 13px;
                background: rgba(0,0,0,.10);
                transition: border-color .15s;
            }
            .ia-next-item:hover { border-color: rgba(255,255,255,.16); }
            [data-theme="light"] .ia-next-item { background: rgba(0,0,0,.03); }
            .ia-next-item strong {
                display: block;
                font-size: 13px;
                font-weight: 800;
                color: var(--text-white);
            }
            [data-theme="light"] .ia-next-item strong { color: #111827; }
            .ia-next-item span {
                display: block;
                font-size: 12px;
                color: var(--muted);
                margin-top: 2px;
            }

            /* ─── Empty ──────────────────────────────────────────── */
            .ia-empty {
                padding: 28px 20px;
                text-align: center;
                color: var(--muted);
                font-size: 13px;
                font-weight: 600;
                border: 1px dashed rgba(255,255,255,.10);
                border-radius: 12px;
                margin: 4px;
            }

            /* ─── Toast ──────────────────────────────────────────── */
            .ia-toast {
                margin-bottom: 16px;
                padding: 12px 16px;
                background: rgba(74,222,128,.08);
                border: 1px solid rgba(74,222,128,.2);
                border-radius: 10px;
                color: var(--green);
                font-size: 13px;
                font-weight: 700;
            }

            /* ─── Responsive ─────────────────────────────────────── */
            @media (max-width: 860px) {
                .ia-stats { grid-template-columns: repeat(2, 1fr); }
                .inst-agenda-stats { grid-template-columns: repeat(2, 1fr) !important; }
            }
            @media (max-width: 520px) {
                .ia-stats { grid-template-columns: repeat(2, 1fr); }
                .ia-avail-grid { grid-template-columns: 1fr; }
                .ia-hero__title { font-size: 22px; }
            }
        </style>
    @endpush

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Hero --}}
            <div class="dash-hero" style="margin-bottom:1.25rem;">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Instrutor</div>
                        <h2 class="dash-hero__title">Minha Agenda</h2>
                        <p class="dash-hero__sub">{{ $agenda['instructor']['specialty'] }}</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            AGENDA
                        </span>
                        <a href="{{ route('dashboard') }}" class="btn-ghost" style="text-decoration:none;">Painel</a>
                    </div>
                </div>
            </div>

            {{-- Toast --}}
            @if(session('success'))
                <div class="ia-toast">{{ session('success') }}</div>
            @endif

            {{-- Stats --}}
            <div class="mgr-stats inst-agenda-stats" style="grid-template-columns:repeat(4,1fr);">
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Dias cadastrados</span>
                    <strong class="mgr-stat-card__value">{{ $agenda['summary']['registered_days'] }}</strong>
                    <span class="mgr-stat-card__sub">na semana</span>
                </div>
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Turnos</span>
                    <strong class="mgr-stat-card__value">{{ $agenda['summary']['registered_slots'] }}</strong>
                    <span class="mgr-stat-card__sub">cadastrados</span>
                </div>
                <div class="mgr-stat-card mgr-stat-card--green">
                    <span class="mgr-stat-card__label">Livres</span>
                    <strong class="mgr-stat-card__value">{{ $agenda['summary']['free_slots'] }}</strong>
                    <span class="mgr-stat-card__sub">sem aluno</span>
                </div>
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Ocupados</span>
                    <strong class="mgr-stat-card__value" style="color:#fbbf24;">{{ $agenda['summary']['occupied_slots'] }}</strong>
                    <span class="mgr-stat-card__sub">{{ $agenda['summary']['linked_students'] }} aluno(s)</span>
                </div>
            </div>

            {{-- Tabbed shell --}}
            @php
                $availabilityBySlot = $availabilities->keyBy(fn($item) => $item->week_day.'|'.$item->shift);
                $slotIndex = 0;
                $nextCount = count($agenda['next_slots']);
            @endphp

            <div class="ia-shell">

                {{-- Tab nav --}}
                <nav class="ia-tabs" role="tablist" aria-label="Seções da agenda">
                    <button
                        class="ia-tab ia-tab--active"
                        role="tab" aria-selected="true" aria-controls="ia-panel-avail"
                        data-ia-tab="avail"
                        type="button">
                        Disponibilidade
                    </button>
                    <button
                        class="ia-tab"
                        role="tab" aria-selected="false" aria-controls="ia-panel-week"
                        data-ia-tab="week"
                        type="button">
                        Semana
                        @if($agenda['summary']['registered_slots'] > 0)
                            <span class="ia-tab__badge">{{ $agenda['summary']['registered_slots'] }}</span>
                        @endif
                    </button>
                    <button
                        class="ia-tab"
                        role="tab" aria-selected="false" aria-controls="ia-panel-next"
                        data-ia-tab="next"
                        type="button">
                        Próximos horários
                        @if($nextCount > 0)
                            <span class="ia-tab__badge">{{ $nextCount }}</span>
                        @endif
                    </button>
                </nav>

                {{-- ── Panel 1: Disponibilidade ── --}}
                <div class="ia-panel ia-panel--active" id="ia-panel-avail" role="tabpanel">
                    <form method="POST" action="{{ route('instructor.availability.store') }}">
                        @csrf
                        <div class="ia-avail">
                            <p class="ia-avail-hint">Marque os turnos em que você está disponível e defina os horários.</p>

                            <div class="ia-avail-grid">
                                @foreach($weekDays as $dayKey => $dayLabel)
                                    <div class="ia-avail-day">
                                        <p class="ia-avail-day__name">{{ $dayLabel }}</p>
                                        <div class="ia-shift-list">
                                            @foreach($shifts as $shiftKey => $shiftLabel)
                                                @php
                                                    $saved = $availabilityBySlot->get($dayKey.'|'.$shiftKey);
                                                    $idx   = $slotIndex++;
                                                    $startTime = '';
                                                    $endTime   = '';
                                                    if ($saved?->start_time) {
                                                        $startTime = $saved->start_time instanceof \Carbon\CarbonInterface
                                                            ? $saved->start_time->format('H:i')
                                                            : mb_substr((string)$saved->start_time, 0, 5);
                                                    }
                                                    if ($saved?->end_time) {
                                                        $endTime = $saved->end_time instanceof \Carbon\CarbonInterface
                                                            ? $saved->end_time->format('H:i')
                                                            : mb_substr((string)$saved->end_time, 0, 5);
                                                    }
                                                @endphp
                                                <div class="ia-shift">
                                                    <input type="hidden" name="availability[{{ $idx }}][week_day]" value="{{ $dayKey }}">
                                                    <input type="hidden" name="availability[{{ $idx }}][shift]"    value="{{ $shiftKey }}">
                                                    <input type="hidden" name="availability[{{ $idx }}][active]"   value="0">

                                                    <label class="ia-shift-check" for="av-{{ $idx }}">
                                                        <input
                                                            id="av-{{ $idx }}"
                                                            type="checkbox"
                                                            name="availability[{{ $idx }}][active]"
                                                            value="1"
                                                            @checked((bool)($saved?->active ?? false))>
                                                        {{ $shiftLabel }}
                                                    </label>

                                                    <div class="ia-shift-times">
                                                        <input type="time" name="availability[{{ $idx }}][start_time]" value="{{ $startTime }}" aria-label="Início">
                                                        <input type="time" name="availability[{{ $idx }}][end_time]"   value="{{ $endTime }}"   aria-label="Fim">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="ia-avail-footer">
                                <button type="submit" class="btn-save" style="font-size:12px; padding:9px 20px;">
                                    Salvar disponibilidade
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- ── Panel 2: Semana ── --}}
                <div class="ia-panel" id="ia-panel-week" role="tabpanel">
                    <div class="ia-week">
                        @forelse($agenda['days'] as $day)
                            <div class="ia-day-row">
                                <div class="ia-day-head">
                                    <span class="ia-day-abbr">{{ $day['short_label'] }}</span>
                                    <div class="ia-day-info">
                                        <strong>{{ $day['label'] }}</strong>
                                        <span>{{ count($day['slots']) }} horário(s)</span>
                                    </div>
                                </div>

                                @if(count($day['slots']) > 0)
                                    <div class="ia-slots-grid">
                                        @foreach($day['slots'] as $slot)
                                            <div class="ia-slot ia-slot--{{ $slot['status'] }}">
                                                <div class="ia-slot-top">
                                                    <strong>{{ $slot['shift_label'] }}</strong>
                                                    <span class="ia-badge ia-badge--{{ $slot['status'] }}">{{ $slot['status_label'] }}</span>
                                                </div>
                                                <span class="ia-slot-time">{{ $slot['time_label'] }}</span>
                                                @if(count($slot['students']) > 0)
                                                    <div class="ia-chips">
                                                        @foreach($slot['students'] as $student)
                                                            <span class="ia-chip" title="{{ $student['email'] }}">{{ $student['name'] }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="ia-empty">Sem horário cadastrado.</div>
                                @endif
                            </div>
                        @empty
                            <div class="ia-empty">Nenhum dia cadastrado ainda.</div>
                        @endforelse
                    </div>
                </div>

                {{-- ── Panel 3: Próximos horários ── --}}
                <div class="ia-panel" id="ia-panel-next" role="tabpanel">
                    <div class="ia-next">
                        @forelse($agenda['next_slots'] as $slot)
                            <div class="ia-next-item">
                                <div>
                                    <strong>{{ $slot['next_label'] }}</strong>
                                    <span>{{ $slot['time_label'] }}</span>
                                </div>
                                <span class="ia-badge ia-badge--{{ $slot['status'] }}">{{ $slot['status_label'] }}</span>
                            </div>
                        @empty
                            <div class="ia-empty">Nenhum horário disponível para exibir.</div>
                        @endforelse
                    </div>
                </div>

            </div>{{-- /ia-shell --}}
        </div>
    </div>

    <script>
        (function () {
            const tabs = document.querySelectorAll('[data-ia-tab]');
            const panels = document.querySelectorAll('.ia-panel');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const target = tab.dataset.iaTab;

                    tabs.forEach(item => {
                        item.classList.remove('ia-tab--active');
                        item.setAttribute('aria-selected', 'false');
                    });

                    panels.forEach(panel => panel.classList.remove('ia-panel--active'));

                    tab.classList.add('ia-tab--active');
                    tab.setAttribute('aria-selected', 'true');

                    document.getElementById('ia-panel-' + target)?.classList.add('ia-panel--active');
                });
            });
        })();
    </script>
</x-app-layout>
