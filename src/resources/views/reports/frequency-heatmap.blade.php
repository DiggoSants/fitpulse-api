<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endpush

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- HERO --}}
            <div class="dash-hero" style="margin-bottom:24px;">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Relatórios</div>
                        <h2 class="dash-hero__title">Frequência</h2>
                        <p class="dash-hero__sub">Mapa de calor — dias e horários mais movimentados</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            GERENTE
                        </span>
                        <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                            <a href="{{ route('dashboard') }}" class="btn-ghost"
                               style="text-decoration:none; font-size:12px; padding:9px 18px;">
                                ← Gerente
                            </a>
                            <a href="{{ route('reports.plans.comparative') }}" class="btn-ghost"
                               style="text-decoration:none; font-size:12px; padding:9px 18px;">
                                Ver Comparativo
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARDS RESUMO --}}
            <div class="hm-summary" id="hm-summary">
                <div class="hm-summary-card">
                    <span class="hm-summary-card__label">Total de registros</span>
                    <strong class="hm-summary-card__value" id="hm-total">—</strong>
                    <span class="hm-summary-card__sub">últimos 90 dias</span>
                </div>
                <div class="hm-summary-card hm-summary-card--red">
                    <span class="hm-summary-card__label">Dia mais movimentado</span>
                    <strong class="hm-summary-card__value" id="hm-peak-day">—</strong>
                    <span class="hm-summary-card__sub">maior volume</span>
                </div>
                <div class="hm-summary-card hm-summary-card--green">
                    <span class="hm-summary-card__label">Horário de pico</span>
                    <strong class="hm-summary-card__value" id="hm-peak-hour">—</strong>
                    <span class="hm-summary-card__sub">hora mais frequente</span>
                </div>
            </div>

            {{-- HEATMAP --}}
            <div class="hm-wrap">
                <div class="hm-header">
                    <p class="section-label" style="margin:0;">MAPA DE CALOR — PRESENÇA POR DIA E HORA</p>
                    <div class="hm-legend">
                        <span class="hm-legend__label">Menos</span>
                        <div class="hm-legend__bar">
                            <div class="hm-legend__cell" style="--intensity:0"></div>
                            <div class="hm-legend__cell" style="--intensity:0.25"></div>
                            <div class="hm-legend__cell" style="--intensity:0.5"></div>
                            <div class="hm-legend__cell" style="--intensity:0.75"></div>
                            <div class="hm-legend__cell" style="--intensity:1"></div>
                        </div>
                        <span class="hm-legend__label">Mais</span>
                    </div>
                </div>

                {{-- Skeleton enquanto carrega --}}
                <div id="hm-skeleton" class="hm-skeleton-wrap">
                    <div class="hm-skeleton-grid">
                        @for($i = 0; $i < 7; $i++)
                            <div class="hm-skeleton-row">
                                <div class="sk hm-skeleton-label"></div>
                                @for($j = 0; $j < 24; $j++)
                                    <div class="sk hm-skeleton-cell"></div>
                                @endfor
                            </div>
                        @endfor
                    </div>
                </div>

                {{-- Grid real (injetado via JS) --}}
                <div id="hm-grid" class="hm-grid" style="display:none;">
                    {{-- horas no topo --}}
                    <div class="hm-hour-row">
                        <div class="hm-day-label"></div>
                        @for($h = 0; $h < 24; $h++)
                            <div class="hm-hour-label">{{ sprintf('%02d', $h) }}</div>
                        @endfor
                    </div>
                    {{-- linhas injetadas pelo JS --}}
                    <div id="hm-rows"></div>
                </div>

                {{-- Tooltip --}}
                <div id="hm-tooltip" class="hm-tooltip" style="display:none;"></div>

                {{-- Empty state --}}
                <div id="hm-empty" class="hm-empty" style="display:none;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none"
                         style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 14px; display:block; opacity:.20;">
                        <rect x="3" y="3" width="18" height="18" rx="3"/>
                        <path d="M3 9h18M9 21V9"/>
                    </svg>
                    <p>Nenhum registro de frequência ainda.</p>
                    <p style="font-size:13px; margin-top:6px; opacity:.45;">Os dados aparecerão aqui conforme os alunos registrarem presença.</p>
                </div>
            </div>

        </div>
    </div>

    <script>
    (function () {
        const DAYS  = ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'];
        const endpoint = "{{ route('reports.frequency.heatmap') }}";

        async function loadHeatmap() {
            try {
                const res  = await fetch(endpoint, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();
                const data = json.data ?? [];

                document.getElementById('hm-skeleton').style.display = 'none';

                if (!data.length || data.every(d => d.count === 0)) {
                    document.getElementById('hm-empty').style.display = 'block';
                    return;
                }

                const maxVal   = Math.max(...data.map(d => d.count), 1);
                const total    = data.reduce((s, d) => s + d.count, 0);

                // Peak day
                const dayTotals = {};
                data.forEach(d => { dayTotals[d.day_of_week] = (dayTotals[d.day_of_week] || 0) + d.count; });
                const peakDay = Object.entries(dayTotals).sort((a,b) => b[1]-a[1])[0];

                // Peak hour
                const hourTotals = {};
                data.forEach(d => { hourTotals[d.hour] = (hourTotals[d.hour] || 0) + d.count; });
                const peakHour = Object.entries(hourTotals).sort((a,b) => b[1]-a[1])[0];

                document.getElementById('hm-total').textContent     = total.toLocaleString('pt-BR');
                document.getElementById('hm-peak-day').textContent  = peakDay  ? DAYS[peakDay[0]]               : '—';
                document.getElementById('hm-peak-hour').textContent = peakHour ? sprintf(peakHour[0]) + ':00'   : '—';

                // Build rows
                const rowsEl = document.getElementById('hm-rows');
                DAYS.forEach((dayName, d) => {
                    const row = document.createElement('div');
                    row.className = 'hm-row';

                    const lbl = document.createElement('div');
                    lbl.className   = 'hm-day-label';
                    lbl.textContent = dayName.substring(0, 3);
                    row.appendChild(lbl);

                    for (let h = 0; h < 24; h++) {
                        const cell = data.find(x => x.day_of_week === d && x.hour === h);
                        const count = cell ? cell.count : 0;
                        const intensity = count / maxVal;

                        const el = document.createElement('div');
                        el.className = 'hm-cell';
                        el.style.setProperty('--intensity', intensity);
                        el.setAttribute('data-count', count);
                        el.setAttribute('data-day', dayName);
                        el.setAttribute('data-hour', sprintf(h) + ':00');

                        el.addEventListener('mouseenter', showTooltip);
                        el.addEventListener('mouseleave', hideTooltip);
                        el.addEventListener('mousemove',  moveTooltip);

                        row.appendChild(el);
                    }

                    rowsEl.appendChild(row);
                });

                document.getElementById('hm-grid').style.display = 'block';

            } catch (e) {
                document.getElementById('hm-skeleton').style.display = 'none';
                document.getElementById('hm-empty').style.display    = 'block';
                console.error('Heatmap error:', e);
            }
        }

        function sprintf(n) { return String(n).padStart(2, '0'); }

        const tooltip = document.getElementById('hm-tooltip');

        function showTooltip(e) {
            const el    = e.currentTarget;
            const count = el.getAttribute('data-count');
            const day   = el.getAttribute('data-day');
            const hour  = el.getAttribute('data-hour');
            tooltip.innerHTML = `<strong>${day}</strong> às ${hour}<br><span>${count} registro${count != 1 ? 's' : ''}</span>`;
            tooltip.style.display = 'block';
            moveTooltip(e);
        }

        function hideTooltip()  { tooltip.style.display = 'none'; }

        function moveTooltip(e) {
            const x = e.clientX + 14;
            const y = e.clientY - 38;
            tooltip.style.left = x + 'px';
            tooltip.style.top  = y + 'px';
        }

        loadHeatmap();
    })();
    </script>
</x-app-layout>