<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Hero --}}
            <div class="dash-hero" style="margin-bottom:28px;">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Evolução Física</div>
                        <h2 class="dash-hero__title">Minha Evolução</h2>
                        <p class="dash-hero__sub">Registre e acompanhe seu progresso físico.</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            ALUNO
                        </span>
                        <a href="{{ route('dashboard') }}" class="btn-ghost" style="text-decoration:none;">
                            ← Voltar
                        </a>
                    </div>
                </div>
            </div>

            <div style="display:grid; gap:24px;">

                {{-- Formulário --}}
                <div class="ev-form-card">
                    <h3 class="ev-section-title">Nova Avaliação</h3>

                    <form id="eval-form" style="display:grid; gap:14px;">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                            <label class="ev-label">
                                Peso (kg)
                                <input id="eval-weight" type="number" step="0.1" min="1" required
                                    placeholder="Ex: 75.5" class="ev-input" />
                            </label>
                            <label class="ev-label">
                                Altura (cm)
                                <input id="eval-height" type="number" step="0.1" min="1" required
                                    placeholder="Ex: 175" class="ev-input" />
                            </label>
                        </div>

                        <label class="ev-label">
                            Gordura Corporal (%) <span class="ev-optional">— opcional</span>
                            <input id="eval-fat" type="number" step="0.1" min="0" max="100"
                                placeholder="Ex: 18.5" class="ev-input" />
                        </label>

                        <label class="ev-label">
                            Observações <span class="ev-optional">— opcional</span>
                            <textarea id="eval-notes" rows="3"
                                placeholder="Como você está se sentindo, metas, etc."
                                class="ev-input ev-textarea"></textarea>
                        </label>

                        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                            <button type="submit" id="eval-submit" class="btn-save" style="padding:12px 20px; gap:8px; display:inline-flex; align-items:center;">
                                <svg id="eval-spinner" width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    style="display:none; stroke:#fff; stroke-width:2.5; stroke-linecap:round; animation:spin .7s linear infinite;">
                                    <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                                </svg>
                                <span id="eval-submit-label">Salvar avaliação</span>
                            </button>
                            <span id="eval-message" style="font-size:13px;"></span>
                        </div>
                    </form>
                </div>

                {{-- Histórico --}}
                <div class="ev-form-card">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
                        <h3 class="ev-section-title" style="margin-bottom:0;">Histórico de Avaliações</h3>
                        <span class="ev-count-badge">{{ $evaluations->count() }} registro(s)</span>
                    </div>

                    @if($evaluations->isEmpty())
                        <div class="empty-state" style="padding:3rem 1rem;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none"
                                style="stroke:var(--text-muted); stroke-width:1.1; margin:0 auto 16px; display:block; opacity:.20;">
                                <path d="M3 3v18h18"/><path d="M7 16l4-4 4 4 4-6"/>
                            </svg>
                            <p>Nenhuma avaliação registrada ainda.</p>
                            <p style="font-size:13px; margin-top:6px; opacity:.45;">Registre sua primeira avaliação acima.</p>
                        </div>
                    @else
                        @php
                            $last  = $evaluations->first();
                            $first = $evaluations->last();
                            $weightChange = round($last->weight - $first->weight, 2);
                            $imcChange    = round($last->imc - $first->imc, 2);
                            $fatChange    = ($last->body_fat && $first->body_fat)
                                            ? round($last->body_fat - $first->body_fat, 2) : null;
                        @endphp

                        {{-- ── GRÁFICO DE EVOLUÇÃO ── --}}
                        @if($evaluations->count() > 1)
                        <div class="ev-chart-wrap">
                            {{-- Resumo topo --}}
                            <div class="ev-chart-meta">
                                <div class="ev-chart-stat">
                                    <span class="ev-chart-stat__label">Peso atual</span>
                                    <span class="ev-chart-stat__val">{{ $last->weight }} kg</span>
                                    <span class="ev-chart-stat__delta {{ $weightChange <= 0 ? 'ev-delta--down' : 'ev-delta--up' }}">
                                        {{ $weightChange <= 0 ? '▼' : '▲' }} {{ abs($weightChange) }} kg desde o início
                                    </span>
                                </div>
                                <div class="ev-chart-stat">
                                    <span class="ev-chart-stat__label">IMC atual</span>
                                    <span class="ev-chart-stat__val">{{ $last->imc }}</span>
                                    <span class="ev-chart-stat__delta" style="color:var(--text-muted);">{{ $last->imc_classification }}</span>
                                </div>
                                @if($last->body_fat)
                                <div class="ev-chart-stat">
                                    <span class="ev-chart-stat__label">Gordura atual</span>
                                    <span class="ev-chart-stat__val">{{ $last->body_fat }}%</span>
                                    @if($fatChange !== null)
                                    <span class="ev-chart-stat__delta {{ $fatChange <= 0 ? 'ev-delta--down' : 'ev-delta--up' }}">
                                        {{ $fatChange <= 0 ? '▼' : '▲' }} {{ abs($fatChange) }}% desde o início
                                    </span>
                                    @endif
                                </div>
                                @endif
                            </div>

                            {{-- Canvas do gráfico --}}
                            <div style="position:relative; width:100%; height:180px;">
                                <canvas id="weightChart" role="img"
                                    aria-label="Gráfico de evolução do peso e gordura corporal ao longo do tempo">
                                    Evolução do peso desde o início.
                                </canvas>
                            </div>

                            {{-- Legenda --}}
                            <div class="ev-chart-legend">
                                <span class="ev-legend-item">
                                    <span class="ev-legend-dot" style="background:#d61532;"></span>
                                    Peso (kg)
                                </span>
                                @if($evaluations->whereNotNull('body_fat')->count() > 1)
                                <span class="ev-legend-item">
                                    <span class="ev-legend-dot ev-legend-dot--dashed" style="border-color:#3b82f6;"></span>
                                    Gordura (%)
                                </span>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- ── LISTA DE AVALIAÇÕES ── --}}
                        <div style="display:flex; flex-direction:column; gap:10px;">
                            @foreach($evaluations as $i => $eval)
                            @php
                                $prev      = $evaluations->get($i + 1);
                                $wChange   = $prev ? round($eval->weight - $prev->weight, 2) : null;
                                $fatChg    = ($prev && $eval->body_fat && $prev->body_fat)
                                             ? round($eval->body_fat - $prev->body_fat, 2) : null;
                                $isFirst   = $i === $evaluations->count() - 1;
                                $isLatest  = $i === 0;

                                // IMC bar
                                $imc = $eval->imc;
                                if ($imc < 18.5)       { $imcPct = ($imc / 18.5) * 30; $imcColor = '#3b82f6'; $imcLabel = 'Abaixo do peso'; }
                                elseif ($imc < 25)     { $imcPct = 30 + (($imc - 18.5) / 6.5) * 25; $imcColor = '#22c55e'; $imcLabel = 'Peso normal'; }
                                elseif ($imc < 30)     { $imcPct = 55 + (($imc - 25) / 5) * 25; $imcColor = '#f59e0b'; $imcLabel = 'Sobrepeso'; }
                                else                   { $imcPct = min(95, 80 + (($imc - 30) / 10) * 15); $imcColor = '#ef4444'; $imcLabel = 'Obesidade'; }
                            @endphp

                            <div class="ev-card">
                                <div class="ev-card__top">
                                    <div class="ev-card__left">
                                        <div class="ev-card__icon">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                style="stroke:#f87171; stroke-width:1.8; stroke-linecap:round;">
                                                <path d="M3 3v18h18"/><path d="M7 16l4-4 4 4 4-6"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="ev-card__date">
                                                {{ $eval->created_at->format('d/m/Y') }}
                                                @if($isLatest)
                                                    <span class="ev-badge ev-badge--latest">mais recente</span>
                                                @elseif($isFirst)
                                                    <span class="ev-badge ev-badge--first">primeiro registro</span>
                                                @endif
                                            </div>
                                            <div class="ev-card__ago">{{ $eval->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                    <div class="ev-chips">
                                        <span class="ev-chip ev-chip--kg">{{ $eval->weight }} kg</span>
                                        <span class="ev-chip ev-chip--imc">IMC {{ $eval->imc }}</span>
                                        @if($eval->body_fat)
                                            <span class="ev-chip ev-chip--fat">{{ $eval->body_fat }}% gordura</span>
                                        @endif
                                        @if($wChange !== null)
                                            <span class="ev-chip {{ $wChange <= 0 ? 'ev-chip--delta-down' : 'ev-chip--delta-up' }}">
                                                {{ $wChange <= 0 ? '▼' : '▲' }} {{ abs($wChange) }} kg
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Barra IMC --}}
                                <div class="ev-imc-bar-wrap">
                                    <div class="ev-imc-label">
                                        <span>IMC — {{ $imcLabel }}</span>
                                        <span class="ev-imc-val">{{ $eval->imc }}</span>
                                    </div>
                                    <div class="ev-imc-track">
                                        <div class="ev-imc-fill" style="width:{{ round($imcPct) }}%; background:{{ $imcColor }};">
                                            <div class="ev-imc-marker" style="background:{{ $imcColor }};"></div>
                                        </div>
                                    </div>
                                    <div class="ev-imc-scale">
                                        <span>Abaixo (↓18.5)</span>
                                        <span>Normal</span>
                                        <span>Sobrepeso</span>
                                        <span>Obeso (↑30)</span>
                                    </div>
                                </div>

                                @if($eval->notes)
                                <div class="ev-note">{{ $eval->notes }}</div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div id="eval-toast" class="shop-toast" style="display:none; position:fixed; bottom:24px; right:24px; z-index:9999;"></div>

    {{-- Chart.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>

    <script>
        // ── Formulário ────────────────────────────────────────────
        const CSRF     = document.querySelector('meta[name="csrf-token"]').content;
        const ENDPOINT = "{{ route('evaluations.store', [], false) }}";

        document.getElementById('eval-form').addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn     = document.getElementById('eval-submit');
            const spinner = document.getElementById('eval-spinner');
            btn.disabled          = true;
            spinner.style.display = 'block';
            try {
                const res  = await fetch(ENDPOINT, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
                    body: JSON.stringify({
                        weight:   document.getElementById('eval-weight').value,
                        height:   document.getElementById('eval-height').value,
                        body_fat: document.getElementById('eval-fat').value || null,
                        notes:    document.getElementById('eval-notes').value || null,
                    }),
                });
                const data = await res.json();
                if (res.ok) {
                    showToast('Avaliação registrada com sucesso! 🎉', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message || 'Erro ao salvar avaliação.', 'error');
                    btn.disabled          = false;
                    spinner.style.display = 'none';
                }
            } catch (err) {
                showToast('Erro de conexão. Tente novamente.', 'error');
                btn.disabled          = false;
                spinner.style.display = 'none';
            }
        });

        function showToast(msg, type) {
            const toast       = document.getElementById('eval-toast');
            toast.textContent = msg;
            toast.className   = 'shop-toast' + (type === 'error' ? ' shop-toast--error' : '');
            toast.style.display = 'flex';
            setTimeout(() => {
                toast.style.opacity   = '0';
                toast.style.transform = 'translateY(6px)';
                setTimeout(() => { toast.style.display = 'none'; toast.style.opacity = '1'; toast.style.transform = 'none'; }, 300);
            }, 3500);
        }

        // ── Gráfico de Evolução ───────────────────────────────────
        @if($evaluations->count() > 1)
        (function () {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark'
                        || (!document.documentElement.getAttribute('data-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
            
           const gridColor    = isDark ? 'rgba(255,255,255,0.10)' : 'rgba(0,0,0,0.07)';
           const textColor = isDark ? '#ffffff' : 'rgba(0,0,0,0.45)';
           const tooltipBg    = isDark ? '#1a1a1a' : '#fff';
           const tooltipBorder = isDark ? 'rgba(255,255,255,0.15)' : 'rgba(0,0,0,0.10)';
           const tooltipTitle  = isDark ? '#ffffff' : '#111';
           const tooltipBody   = isDark ? 'rgba(255,255,255,0.75)' : 'rgba(0,0,0,0.55)';


            // Dados vindos do PHP (ordem cronológica — mais antigo primeiro)
            const labels  = @json($evaluations->reverse()->values()->map(fn($e) => $e->created_at->format('d/m'))->toArray());
            const weights = @json($evaluations->reverse()->values()->map(fn($e) => (float) $e->weight)->toArray());
            const fats    = @json($evaluations->reverse()->values()->map(fn($e) => $e->body_fat ? (float) $e->body_fat : null)->toArray());

            const hasFat = fats.some(v => v !== null);

            const datasets = [
                {
                    label: 'Peso (kg)',
                    data: weights,
                    borderColor: '#d61532',
                    backgroundColor: isDark ? 'rgba(214,21,50,0.18)' : 'rgba(214,21,50,0.06)',
                    borderWidth: 2,
                    pointBackgroundColor: '#d61532',
                    pointBorderColor: isDark ? '#111' : '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                }
            ];

            if (hasFat) {
                datasets.push({
                    label: 'Gordura (%)',
                    data: fats,
                    borderColor: '#3b82f6',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [5, 4],
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: isDark ? '#111' : '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    fill: false,
                    tension: 0.4,
                    yAxisID: 'y2'
                });
            }

            const scales = {
                x: {
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { size: 11 }, autoSkip: false, maxRotation: 0 }
                },
                y: {
                    position: 'left',
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { size: 11 }, callback: v => v + ' kg' }
                }
            };

            if (hasFat) {
                scales.y2 = {
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: { color: textColor, font: { size: 11 }, callback: v => v + '%' }
                };
            }

            new Chart(document.getElementById('weightChart'), {
                type: 'line',
                data: { labels, datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: tooltipBg,
                            borderColor: tooltipBorder,
                            borderWidth: 1,
                            titleColor: tooltipTitle,
                            bodyColor: tooltipBody,
                            padding: 10,
                            cornerRadius: 8
                        }
                    },
                    scales
                }
            });
        })();
        @endif
    </script>
</x-app-layout>
