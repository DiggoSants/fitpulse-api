<x-app-layout>
<div class="session-wrap">
    <div class="session-hero">
        <div>
            <p class="session-kicker">Treino do dia</p>
            <h1 class="session-title">{{ $session->workout?->name ?? 'Treino finalizado' }}</h1>
            <p class="session-sub">Finalizado em {{ $session->completed_at?->format('d/m/Y H:i') }}</p>
        </div>
        <span class="session-status session-status--completed">Finalizado</span>
    </div>

    @if(session('success'))
        <div class="session-alert session-alert--success">{{ session('success') }}</div>
    @endif

    <section class="session-card session-empty-state">
        <div class="session-completed-icon">✓</div>
        <h2>Treino finalizado</h2>
        <p>Você concluiu {{ $session->completed_exercises }} de {{ $session->total_exercises }} exercícios hoje.</p>

        <div class="session-progress-bar">
            <span style="width: 100%;"></span>
        </div>

        <div class="session-actions">
            <a href="{{ route('workouts.index') }}" class="btn-save session-link-btn">Ver meus treinos</a>
            <a href="{{ route('dashboard') }}" class="btn-cancel session-link-btn">Voltar ao painel</a>
        </div>
    </section>
</div>
</x-app-layout>
