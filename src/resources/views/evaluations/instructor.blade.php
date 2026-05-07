<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @endpush

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="dash-hero" style="margin-bottom:28px;">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Alunos</div>
                        <h2 class="dash-hero__title">Evolução Física</h2>
                        <p class="dash-hero__sub">Acompanhe o progresso físico dos seus alunos.</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            INSTRUTOR
                        </span>
                        <a href="{{ route('dashboard') }}" class="btn-ghost" style="text-decoration:none;">← Voltar</a>
                    </div>
                </div>
            </div>

            @php
                $totalAlunos  = $students->count();
                $comAvaliacao = $students->filter(fn($s) => $s->user->physicalEvaluations->isNotEmpty())->count();
                $semAvaliacao = $totalAlunos - $comAvaliacao;
            @endphp

            <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:12px; margin-bottom:24px;">
                <div class="dash-stat dash-stat--red">
                    <div class="dash-stat__header"><span class="dash-stat__dot"></span><span class="dash-stat__label">Meus alunos</span></div>
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

            <div class="eval-students-wrap" style="padding:24px; border-radius:20px; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08);">
                <h3 style="font-size:18px; margin:0 0 20px;">Meus Alunos</h3>

                @if($students->isEmpty())
                    <div class="empty-state" style="padding:3rem 1rem;">
                        <p>Nenhum aluno vinculado ainda.</p>
                    </div>
                @else
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        @foreach($students as $student)
                        @php
                            $evals   = $student->user->physicalEvaluations->sortByDesc('created_at');
                            $last    = $evals->first();
                            $prev    = $evals->get(1);
                            $wChange = ($last && $prev) ? round($last->weight - $prev->weight, 2) : null;
                        @endphp
                        <div class="eval-accordion" style="border-radius:14px; border:1px solid rgba(255,255,255,0.07); background:rgba(255,255,255,0.03); overflow:hidden;">

                            <button type="button" onclick="toggleAccordion(this)"
                                style="width:100%; background:none; border:none; padding:14px 18px; cursor:pointer; display:flex; align-items:center; justify-content:space-between; gap:12px; text-align:left;">
                                <div style="display:flex; align-items:center; gap:14px; flex:1; min-width:0;">
                                    <div style="width:38px; height:38px; border-radius:10px; background:rgba(214,21,50,0.12); border:1px solid rgba(214,21,50,0.20); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:15px; font-weight:700; color:#f87171;">
                                        {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="eval-student-name" style="font-size:13px; font-weight:700;">{{ $student->user->name }}</div>
                                        <div class="eval-student-email" style="font-size:11px; margin-top:2px;">{{ $student->user->email }}</div>
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
                                        <span class="eval-badge-none">Sem avaliação</span>
                                    @endif
                                    <svg class="eval-chevron" width="14" height="14" viewBox="0 0 14 14" fill="none"
                                        style="stroke:rgba(255,255,255,0.35); stroke-width:2; stroke-linecap:round; flex-shrink:0; transition:transform .22s;">
                                        <path d="M3 5l4 4 4-4"/>
                                    </svg>
                                </div>
                            </button>

                            <div class="eval-accordion__body" style="display:none; padding:0 18px 18px; border-top:1px solid rgba(255,255,255,0.06);">
                                @if($evals->isEmpty())
                                    <p class="eval-empty-text" style="font-size:13px; padding-top:14px;">Nenhuma avaliação registrada.</p>
                                @else
                                    <div style="display:flex; flex-direction:column; gap:8px; padding-top:14px;">
                                        @foreach($evals as $i => $eval)
                                        @php $wDiff = ($i < $evals->count() - 1) ? round($eval->weight - $evals->get($i + 1)->weight, 2) : null; @endphp
                                        <div class="eval-item" style="border-radius:12px; padding:12px 16px;">
                                            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
                                                <div>
                                                    <div class="eval-date" style="font-size:12px; font-weight:700;">{{ $eval->created_at->format('d/m/Y') }}</div>
                                                    <div class="eval-ago" style="font-size:11px; margin-top:2px;">{{ $eval->created_at->diffForHumans() }}</div>
                                                </div>
                                                <div class="ev-chips">
                                                    <span class="ev-chip ev-chip--kg">{{ $eval->weight }} kg</span>
                                                    <span class="ev-chip ev-chip--imc">IMC {{ $eval->imc }}</span>
                                                    @if($eval->body_fat)
                                                        <span class="ev-chip ev-chip--fat">{{ $eval->body_fat }}% gord.</span>
                                                    @endif
                                                    @if($wDiff !== null)
                                                        <span class="ev-chip {{ $wDiff <= 0 ? 'ev-chip--delta-down' : 'ev-chip--delta-up' }}">
                                                            {{ $wDiff > 0 ? '+' : '' }}{{ $wDiff }} kg
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($eval->notes)
                                                <div class="eval-notes" style="margin-top:8px; font-size:11px; padding-top:8px;">{{ $eval->notes }}</div>
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