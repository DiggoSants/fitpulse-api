<x-app-layout>
    @php
        $workoutCreateUrl = route('workouts.create', request()->filled('student_id') ? ['student_id' => request('student_id')] : []);
        $exerciseCreateUrl = route('exercises.create', request()->filled('student_id') ? ['student_id' => request('student_id')] : []);
    @endphp
<div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 exercise-library-shell">

           <div class="exercise-library-header">
    <div class="exercise-library-header__left">
        <p class="workout-form-kicker">BIBLIOTECA</p>
        <h1 class="exercise-library-header__title">Exercícios</h1>
        <p class="exercise-library-header__sub">
            Gerencie a biblioteca de exercícios disponíveis para montar treinos.
        </p>
    </div>

    <div class="exercise-library-header__actions">
        <a href="{{ $workoutCreateUrl }}" class="workout-form-back">← Voltar</a>

        <a href="{{ $exerciseCreateUrl }}" class="btn-save exercise-library-header__new">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                <line x1="7" y1="1" x2="7" y2="13" />
                <line x1="1" y1="7" x2="13" y2="7" />
            </svg>
            Novo Exercício
        </a>
    </div>
</div>

           <ul class="exercise-library-list">
    @foreach($exercises as $exercise)
        <li class="exercise-library-card">
            <div class="exercise-library-card__main">
                <div class="exercise-library-card__icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="2" y="9" width="4" height="6" rx="1" />
                        <rect x="18" y="9" width="4" height="6" rx="1" />
                        <rect x="7" y="11" width="10" height="2" rx="1" />
                    </svg>
                </div>

                <div class="exercise-library-card__content">
                    <div class="exercise-library-card__top">
                        <h2 class="exercise-library-card__title">{{ $exercise->name }}</h2>
                        <span class="exercise-library-card__badge">{{ $exercise->muscle_group }}</span>
                    </div>

                    @if(!empty($exercise->description))
                        <p class="exercise-library-card__desc">{{ $exercise->description }}</p>
                    @else
                        <p class="exercise-library-card__desc">Sem descrição cadastrada.</p>
                    @endif
                </div>
            </div>

            <div class="exercise-library-card__actions">
                <a href="/exercises/{{ $exercise->id }}/edit"
                   class="exercise-library-btn exercise-library-btn--edit">
                    Editar
                </a>

                <form action="/exercises/{{ $exercise->id }}" method="POST" style="margin:0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="exercise-library-btn exercise-library-btn--delete">
                        🗑 Deletar
                    </button>
                </form>
            </div>
        </li>
    @endforeach
</ul>

        </div>
    </div>
</x-app-layout>
