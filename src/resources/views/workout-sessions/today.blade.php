<x-app-layout>
@php
    $progress = $session->progress_percentage;
    $isPending = $session->status === 'pending';
    $isInProgress = $session->status === 'in_progress';
@endphp

<div class="session-wrap" data-session-status="{{ $session->status }}">
    <div class="session-hero">
        <div>
            <p class="session-kicker">Treino do dia</p>
            <h1 class="session-title">{{ $workout->name }}</h1>
            <p class="session-sub">{{ now()->format('d/m/Y') }} · {{ $session->completed_exercises }} de {{ $session->total_exercises }} exercícios</p>
        </div>

        <span class="session-status session-status--{{ $session->status }}" id="session-status-label">
            @if($isPending)
                Não iniciado
            @elseif($isInProgress)
                Em andamento
            @else
                Finalizado
            @endif
        </span>
    </div>

    @if(session('success'))
        <div class="session-alert session-alert--success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="session-alert session-alert--error">{{ session('error') }}</div>
    @endif

    <div class="session-alert" id="session-feedback" style="display:none;"></div>

    <section class="session-card">
        <div class="session-progress-head">
            <div>
                <p class="session-section-label">Progresso</p>
                <strong id="session-progress-text">{{ $progress }}%</strong>
            </div>
            <span id="session-count-text">{{ $session->completed_exercises }}/{{ $session->total_exercises }}</span>
        </div>

        <div class="session-progress-bar" aria-label="Progresso do treino">
            <span id="session-progress-fill" style="width: {{ $progress }}%;"></span>
        </div>

        <div class="session-actions">
            @if($isPending)
                <button type="button" class="btn-save" id="start-workout-btn">
                    Iniciar treino
                </button>
            @endif

            <button type="button" class="btn-save session-finish-btn" id="finish-workout-btn" style="{{ $isInProgress ? '' : 'display:none;' }}">
                Finalizar treino
            </button>

            <a href="{{ route('workouts.index') }}" class="btn-cancel session-link-btn">Ver meus treinos</a>
        </div>
    </section>

    <section class="session-card">
        <div class="session-list-head">
            <div>
                <p class="session-section-label">Execução</p>
                <h2>Exercícios</h2>
            </div>
            <span>{{ $sessionExercises->count() }} item{{ $sessionExercises->count() !== 1 ? 's' : '' }}</span>
        </div>

        <div class="session-exercise-list" id="session-exercise-list">
            @foreach($sessionExercises as $sessionExercise)
                @php
                    $exercise = $sessionExercise->workoutExercise->exercise;
                    $done = $sessionExercise->completed;
                @endphp

                <article class="session-exercise-card {{ $done ? 'is-completed' : '' }}" data-exercise-card="{{ $sessionExercise->id }}">
                    <div class="session-exercise-main">
                        <span class="session-exercise-order">{{ $loop->iteration }}</span>
                        <div>
                            <h3>{{ $exercise->name }}</h3>
                            <p>{{ $exercise->muscle_group ?? 'Grupo muscular' }}</p>
                            <div class="session-exercise-meta">
                                <span>{{ $sessionExercise->workoutExercise->sets }} séries</span>
                                <span>{{ $sessionExercise->workoutExercise->reps }} reps</span>
                            </div>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="session-exercise-check"
                        data-complete-url="{{ route('workout-sessions.complete-exercise', $sessionExercise->id) }}"
                        data-exercise-id="{{ $sessionExercise->id }}"
                        {{ (!$isInProgress || $done) ? 'disabled' : '' }}
                    >
                        {{ $done ? 'Concluído' : 'Marcar concluído' }}
                    </button>
                </article>
            @endforeach
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const startButton = document.getElementById('start-workout-btn');
    const finishButton = document.getElementById('finish-workout-btn');
    const feedback = document.getElementById('session-feedback');
    const progressText = document.getElementById('session-progress-text');
    const countText = document.getElementById('session-count-text');
    const progressFill = document.getElementById('session-progress-fill');
    const statusLabel = document.getElementById('session-status-label');
    const exerciseButtons = Array.from(document.querySelectorAll('.session-exercise-check'));
    let completedCount = {{ (int) $session->completed_exercises }};
    const totalCount = {{ (int) $session->total_exercises }};

    function showFeedback(message, type = 'success') {
        feedback.textContent = message;
        feedback.className = `session-alert session-alert--${type}`;
        feedback.style.display = 'block';
    }

    async function postJson(url, payload = {}) {
        const response = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(data.message || data.error || 'Não foi possível concluir a ação.');
        }

        return data;
    }

    function updateProgress(count, progress) {
        completedCount = count;
        const percent = Number(progress ?? (totalCount ? Math.round((count / totalCount) * 100) : 0));
        progressText.textContent = `${percent}%`;
        countText.textContent = `${count}/${totalCount}`;
        progressFill.style.width = `${percent}%`;
    }

    if (startButton) {
        startButton.addEventListener('click', async function() {
            startButton.disabled = true;

            try {
                const data = await postJson("{{ route('workout-sessions.start', $session->id) }}");
                showFeedback(data.message || 'Treino iniciado.');
                startButton.style.display = 'none';
                finishButton.style.display = '';
                statusLabel.textContent = 'Em andamento';
                statusLabel.className = 'session-status session-status--in_progress';
                exerciseButtons.forEach(button => {
                    if (button.textContent.trim() !== 'Concluído') button.disabled = false;
                });
            } catch (error) {
                startButton.disabled = false;
                showFeedback(error.message, 'error');
            }
        });
    }

    exerciseButtons.forEach(button => {
        button.addEventListener('click', async function() {
            if (button.disabled) return;

            button.disabled = true;

            try {
                const data = await postJson(button.dataset.completeUrl);
                const card = document.querySelector(`[data-exercise-card="${button.dataset.exerciseId}"]`);
                card?.classList.add('is-completed');
                button.textContent = 'Concluído';
                updateProgress(Number(data.completed_count), Number(data.progress));
                showFeedback(data.message || 'Exercício concluído.');
            } catch (error) {
                button.disabled = false;
                showFeedback(error.message, 'error');
            }
        });
    });

    if (finishButton) {
        finishButton.addEventListener('click', async function() {
            finishButton.disabled = true;

            try {
                const data = await postJson("{{ route('workout-sessions.complete', $session->id) }}");
                showFeedback(data.message || 'Treino finalizado.');
                window.setTimeout(() => window.location.reload(), 700);
            } catch (error) {
                finishButton.disabled = false;
                showFeedback(error.message, 'error');
            }
        });
    }
});
</script>
</x-app-layout>
