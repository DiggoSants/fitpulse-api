<x-app-layout>
    @push('styles')
        <style>
            /* ── EVAL ACCORDION — tema-aware ── */
            .eval-wrap {
                padding: 24px;
                border-radius: 20px;
                background: rgba(255,255,255,0.04);
                border: 1px solid rgba(255,255,255,0.08);
            }
            [data-theme="light"] .eval-wrap {
                background: #fff;
                border-color: rgba(0,0,0,0.08);
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            }

            .eval-wrap__title {
                font-size: 18px;
                font-weight: 800;
                margin: 0 0 20px;
                color: #f5f5f5;
            }
            [data-theme="light"] .eval-wrap__title { color: #111; }

            /* accordion item */
            .eval-accordion {
                border-radius: 14px;
                border: 1px solid rgba(255,255,255,0.07);
                background: rgba(255,255,255,0.03);
                overflow: hidden;
            }
            [data-theme="light"] .eval-accordion {
                border-color: rgba(0,0,0,0.08);
                background: #fafafa;
            }

            /* botão do accordion */
            .eval-accordion__btn {
                width: 100%;
                background: none;
                border: none;
                padding: 14px 18px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                text-align: left;
                transition: background .15s;
            }
            .eval-accordion__btn:hover { background: rgba(255,255,255,0.03); }
            [data-theme="light"] .eval-accordion__btn:hover { background: rgba(0,0,0,0.02); }

            /* avatar */
            .eval-avatar {
                width: 38px; height: 38px;
                border-radius: 10px;
                background: rgba(214,21,50,0.12);
                border: 1px solid rgba(214,21,50,0.20);
                display: flex; align-items: center; justify-content: center;
                flex-shrink: 0;
                font-size: 15px; font-weight: 700;
                color: #f87171;
            }
            [data-theme="light"] .eval-avatar {
                background: rgba(214,21,50,0.08);
                border-color: rgba(214,21,50,0.18);
                color: #c1121f;
            }

            /* nome / email */
            .eval-name {
                font-size: 13px; font-weight: 700;
                color: #f5f5f5;
            }
            [data-theme="light"] .eval-name { color: #111; }

            .eval-email {
                font-size: 11px; margin-top: 2px;
                color: rgba(255,255,255,0.40);
            }
            [data-theme="light"] .eval-email { color: rgba(0,0,0,0.45); }

            /* chevron */
            .eval-chevron {
                stroke: rgba(255,255,255,0.35);
                stroke-width: 2;
                stroke-linecap: round;
                flex-shrink: 0;
                transition: transform .22s;
            }
            [data-theme="light"] .eval-chevron { stroke: rgba(0,0,0,0.30); }

            /* corpo expandível */
            .eval-accordion__body {
                display: none;
                padding: 0 18px 18px;
                border-top: 1px solid rgba(255,255,255,0.06);
            }
            [data-theme="light"] .eval-accordion__body {
                border-top-color: rgba(0,0,0,0.06);
            }

            .eval-empty-text {
                font-size: 13px;
                color: rgba(255,255,255,0.35);
                padding-top: 14px;
                margin: 0;
            }
            [data-theme="light"] .eval-empty-text { color: rgba(0,0,0,0.38); }

            /* item de avaliação */
            .eval-item {
                border-radius: 12px;
                border: 1px solid rgba(255,255,255,0.06);
                background: rgba(255,255,255,0.025);
                padding: 12px 16px;
            }
            [data-theme="light"] .eval-item {
                border-color: rgba(0,0,0,0.07);
                background: #f5f5f5;
            }

            .eval-date {
                font-size: 12px; font-weight: 700;
                color: #f5f5f5;
            }
            [data-theme="light"] .eval-date { color: #111; }

            .eval-ago {
                font-size: 11px; margin-top: 2px;
                color: rgba(255,255,255,0.35);
            }
            [data-theme="light"] .eval-ago { color: rgba(0,0,0,0.40); }

            .eval-notes {
                margin-top: 8px; font-size: 11px;
                color: rgba(255,255,255,0.35);
                border-top: 1px solid rgba(255,255,255,0.05);
                padding-top: 8px;
            }
            [data-theme="light"] .eval-notes {
                color: rgba(0,0,0,0.45);
                border-top-color: rgba(0,0,0,0.06);
            }

            /* chips */
            .ev-chip {
                display: inline-flex; align-items: center;
                font-size: 11px; font-weight: 700;
                padding: 3px 10px; border-radius: 99px;
            }
            .ev-chip--kg  { background: rgba(59,130,246,0.14); border: 1px solid rgba(59,130,246,0.22); color: #93c5fd; }
            .ev-chip--imc { background: rgba(214,21,50,0.12);  border: 1px solid rgba(214,21,50,0.22);  color: #f87171; }
            .ev-chip--fat { background: rgba(34,197,94,0.10);  border: 1px solid rgba(34,197,94,0.20);  color: #4ade80; }
            .ev-chip--none { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); color: rgba(255,255,255,0.35); }
            .ev-chip--delta-down { background: rgba(34,197,94,0.10);  border: 1px solid rgba(34,197,94,0.20);  color: #4ade80; }
            .ev-chip--delta-up   { background: rgba(214,21,50,0.10);  border: 1px solid rgba(214,21,50,0.20);  color: #f87171; }

            [data-theme="light"] .ev-chip--kg   { background: rgba(59,130,246,0.08);  border-color: rgba(59,130,246,0.18);  color: #1d4ed8; }
            [data-theme="light"] .ev-chip--imc  { background: rgba(214,21,50,0.07);   border-color: rgba(214,21,50,0.18);   color: #b91c1c; }
            [data-theme="light"] .ev-chip--fat  { background: rgba(22,163,74,0.07);   border-color: rgba(22,163,74,0.18);   color: #15803d; }
            [data-theme="light"] .ev-chip--none { background: rgba(0,0,0,0.05);       border-color: rgba(0,0,0,0.10);       color: rgba(0,0,0,0.40); }
            [data-theme="light"] .ev-chip--delta-down { background: rgba(22,163,74,0.07);  border-color: rgba(22,163,74,0.18);  color: #15803d; }
            [data-theme="light"] .ev-chip--delta-up   { background: rgba(214,21,50,0.07);  border-color: rgba(214,21,50,0.18);  color: #b91c1c; }
        </style>
    @endpush

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Hero --}}
            <div class="dash-hero" style="margin-bottom:28px;">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Relatórios</div>
                        <h2 class="dash-hero__title">Evolução Física</h2>
                        <p class="dash-hero__sub">Acompanhe o progresso físico de todos os alunos.</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            GERENTE
                        </span>
                        <a href="{{ route('dashboard') }}" class="btn-ghost" style="text-decoration:none;">← Voltar</a>
                    </div>
                </div>
            </div>

            {{-- Resumo geral --}}
            @php
                $totalAlunos  = $users->count();
                $comAvaliacao = $users->filter(fn($u) => $u->physicalEvaluations->isNotEmpty())->count();
                $semAvaliacao = $totalAlunos - $comAvaliacao;
            @endphp

            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:12px; margin-bottom:24px;">
                <div class="dash-stat dash-stat--red">
                    <div class="dash-stat__header"><span class="dash-stat__dot"></span><span class="dash-stat__label">Total de alunos</span></div>
                    <div class="dash-stat__value" style="font-size:32px;">{{ $totalAlunos }}</div>
                </div>
                <div class="dash-stat dash-stat--green">
                    <div class="dash-stat__header"><span class="dash-stat__dot"></span><span class="dash-stat__label">Com avaliação</span></div>
                    <div class="dash-stat__value" style="font-size:32px;">{{ $comAvaliacao }}</div>
                </div>
                <div class="dash-stat dash-stat--blue">
                    <div class="dash-stat__header"><span class="dash-stat__dot"></span><span class="dash-stat__label">Sem avaliação</span></div>
                    <div class="dash-stat__value" style="font-size:32px;">{{ $semAvaliacao }}</div>
                </div>
            </div>

            {{-- Lista de alunos --}}
            <div class="eval-wrap">
                <h3 class="eval-wrap__title">Alunos</h3>

                @if($users->isEmpty())
                    <div class="empty-state" style="padding:3rem 1rem;">
                        <p class="eval-empty-text">Nenhum aluno cadastrado ainda.</p>
                    </div>
                @else
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        @foreach($users as $user)
                        @php
                            $evals   = $user->physicalEvaluations;
                            $last    = $evals->first();
                            $prev    = $evals->get(1);
                            $wChange = ($last && $prev) ? round($last->weight - $prev->weight, 2) : null;
                        @endphp
                        <div class="eval-accordion">

                            <button type="button" class="eval-accordion__btn" onclick="toggleAccordion(this)">
                                <div style="display:flex; align-items:center; gap:14px; flex:1; min-width:0;">
                                    <div class="eval-avatar">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="eval-name">{{ $user->name }}</div>
                                        <div class="eval-email">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                    @if($last)
                                        <span class="ev-chip ev-chip--kg">{{ $last->weight }} kg</span>
                                        <span class="ev-chip ev-chip--imc">IMC {{ $last->imc }}</span>
                                        @if($wChange !== null)
                                            <span class="ev-chip {{ $wChange <= 0 ? 'ev-chip--delta-down' : 'ev-chip--delta-up' }}">
                                                {{ $wChange > 0 ? '+' : '' }}{{ $wChange }} kg
                                            </span>
                                        @endif
                                    @else
                                        <span class="ev-chip ev-chip--none">Sem avaliação</span>
                                    @endif
                                    <svg class="eval-chevron" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M3 5l4 4 4-4"/>
                                    </svg>
                                </div>
                            </button>

                            <div class="eval-accordion__body">
                                @if($evals->isEmpty())
                                    <p class="eval-empty-text">Nenhuma avaliação registrada.</p>
                                @else
                                    <div style="display:flex; flex-direction:column; gap:8px; padding-top:14px;">
                                        @foreach($evals as $i => $eval)
                                        @php
                                            $prevEval = $evals->get($i + 1);
                                            $wDiff    = $prevEval ? round($eval->weight - $prevEval->weight, 2) : null;
                                        @endphp
                                        <div class="eval-item">
                                            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                                                <div>
                                                    <div class="eval-date">{{ $eval->created_at->format('d/m/Y') }}</div>
                                                    <div class="eval-ago">{{ $eval->created_at->diffForHumans() }}</div>
                                                </div>
                                                <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                                    <span class="ev-chip ev-chip--kg" style="padding:2px 8px;">{{ $eval->weight }} kg</span>
                                                    <span class="ev-chip ev-chip--imc" style="padding:2px 8px;">IMC {{ $eval->imc }}</span>
                                                    @if($eval->body_fat)
                                                        <span class="ev-chip ev-chip--fat" style="padding:2px 8px;">{{ $eval->body_fat }}% gord.</span>
                                                    @endif
                                                    @if($wDiff !== null)
                                                        <span class="ev-chip {{ $wDiff <= 0 ? 'ev-chip--delta-down' : 'ev-chip--delta-up' }}" style="padding:2px 8px;">
                                                            {{ $wDiff > 0 ? '+' : '' }}{{ $wDiff }} kg
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($eval->notes)
                                                <div class="eval-notes">{{ $eval->notes }}</div>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script>
        function toggleAccordion(btn) {
            const body    = btn.nextElementSibling;
            const chevron = btn.querySelector('.eval-chevron');
            const open    = body.style.display === 'block';
            body.style.display      = open ? 'none' : 'block';
            chevron.style.transform = open ? '' : 'rotate(180deg)';
        }
    </script>
</x-app-layout>