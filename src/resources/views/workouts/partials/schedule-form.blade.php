@php
    $scheduleDays    = array_values($scheduleDays ?? []);
    $scheduleStudent = $scheduleStudent ?? null;
    $scheduleIsCompact = $scheduleIsCompact ?? false;
    $scheduleOldMatches = !$scheduleStudent || (string) old('student_id') === (string) $scheduleStudent->id;
    $selectedDays   = $scheduleOldMatches ? old('days', $scheduleDays) : $scheduleDays;
    $selectedDays   = is_array($selectedDays) ? $selectedDays : [];
    $dayShorts      = [
        'monday'    => 'SEG',
        'tuesday'   => 'TER',
        'wednesday' => 'QUA',
        'thursday'  => 'QUI',
        'friday'    => 'SEX',
        'saturday'  => 'SAB',
        'sunday'    => 'DOM',
    ];
    $formUid  = 'schedule-form-' . ($scheduleStudent?->id ?? 'self') . '-' . uniqid();
    $hasError = count($scheduleDays) < $minScheduleDays;
@endphp

<form id="{{ $formUid }}"
      action="{{ route('student-schedule.store') }}"
      method="POST"
      class="weekly-schedule-form {{ $scheduleIsCompact ? 'weekly-schedule-form--compact' : '' }}">
    @csrf
    @if($scheduleStudent)
        <input type="hidden" name="student_id" value="{{ $scheduleStudent->id }}">
    @endif

    {{-- Grade de dias --}}
    <div class="weekly-days">
        @foreach($weekDays as $dayKey => $dayLabel)
            @php $checked = in_array($dayKey, $selectedDays, true); @endphp
            <label class="weekly-day-option {{ $checked ? 'is-selected' : '' }}">
                <input type="checkbox" name="days[]" value="{{ $dayKey }}" @checked($checked)>
                <span class="weekly-day-option__short">{{ $dayShorts[$dayKey] ?? mb_strtoupper(mb_substr($dayLabel, 0, 3)) }}</span>
                <span class="weekly-day-option__label">{{ $dayLabel }}</span>
            </label>
        @endforeach
    </div>

    {{-- Erros de validação do servidor --}}
    @if($scheduleOldMatches)
        @error('days')
            <div class="weekly-validation weekly-validation--error">{{ $message }}</div>
        @enderror
    @endif

    {{-- Rodapé compacto: abreviações dos dias + aviso + botão --}}
    <div class="sched-footer">
        <div class="sched-footer__days">
            <span class="sched-footer__label">Dias:</span>
            @forelse($scheduleDays as $dayKey)
                <span class="sched-footer__tag">{{ $dayShorts[$dayKey] ?? $dayKey }}</span>
            @empty
                <em class="sched-footer__empty">nenhum</em>
            @endforelse

            @if($hasError)
                <span class="sched-footer__warn" title="Mínimo de {{ $minScheduleDays }} dias necessário">
                    ⚠ mín. {{ $minScheduleDays }}
                </span>
            @endif
        </div>

        <button type="submit" class="sched-footer__btn">Salvar</button>
    </div>
</form>

<style>
/* ── Rodapé compacto da agenda ──────────────────────────── */
.sched-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
}

.sched-footer__days {
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
    flex: 1;
    min-width: 0;
}

.sched-footer__label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--text-muted);
    white-space: nowrap;
}

.sched-footer__tag {
    font-size: 10px;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 99px;
    background: rgba(214,21,50,0.10);
    border: 1px solid rgba(214,21,50,0.20);
    color: #f87171;
    white-space: nowrap;
}

.sched-footer__empty {
    font-size: 11px;
    font-style: normal;
    color: rgba(255,255,255,0.28);
}

.sched-footer__warn {
    font-size: 10px;
    font-weight: 700;
    color: #fbbf24;
    background: rgba(251,191,36,0.10);
    border: 1px solid rgba(251,191,36,0.22);
    border-radius: 99px;
    padding: 2px 8px;
    white-space: nowrap;
}

.sched-footer__btn {
    flex-shrink: 0;
    padding: 6px 16px;
    border-radius: 99px;
    background: #d61532;
    border: none;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .04em;
    cursor: pointer;
    font-family: 'Montserrat', sans-serif;
    transition: opacity .18s, transform .15s;
}

.sched-footer__btn:hover {
    opacity: .85;
    transform: translateY(-1px);
}

/* Light mode */
[data-theme="light"] .sched-footer__label  { color: rgba(0,0,0,0.40); }
[data-theme="light"] .sched-footer__tag    { background: rgba(214,21,50,0.07); border-color: rgba(214,21,50,0.15); color: #b91c1c; }
[data-theme="light"] .sched-footer__empty  { color: rgba(0,0,0,0.30); }
[data-theme="light"] .sched-footer__warn   { color: #92400e; background: rgba(217,119,6,0.08); border-color: rgba(217,119,6,0.22); }
</style>

<script>
(function () {
    var form = document.getElementById('{{ $formUid }}');
    if (!form) return;

    form.querySelectorAll('.weekly-day-option input[type="checkbox"]').forEach(function (cb) {
        cb.addEventListener('change', function () {
            this.closest('.weekly-day-option').classList.toggle('is-selected', this.checked);
        });
    });
})();
</script>