<x-app-layout>
<div class="session-wrap">
    <div class="session-hero">
        <div>
            <p class="session-kicker">Treino do dia</p>
            <h1 class="session-title">Nenhum treino para hoje</h1>
            <p class="session-sub">{{ now()->format('d/m/Y') }}</p>
        </div>
        <span class="session-status session-status--empty">Sem treino</span>
    </div>

    <section class="session-card session-empty-state">
        <h2>Nenhum treino para hoje</h2>
        <p>{{ $message }}</p>

        <div class="session-actions">
            <a href="{{ route('workouts.index') }}" class="btn-save session-link-btn">Ver agenda de treinos</a>
            <a href="{{ route('dashboard') }}" class="btn-cancel session-link-btn">Voltar ao painel</a>
        </div>
    </section>
</div>
</x-app-layout>
