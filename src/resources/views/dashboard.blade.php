<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @endpush
<div class="py-6">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

<h2 class="text-2xl font-bold mb-6">
Seu Treino
</h2>

@if($exercises->isEmpty())

<p class="empty-state">Nenhum treino encontrado.</p>

@else

<ul class="exercise-list">

@foreach($exercises as $exercise)

<li class="exercise-card">

    <div class="exercise-thumb">
        <svg viewBox="0 0 24 24">
            <rect x="2" y="9" width="4" height="6" rx="1"/>
            <rect x="18" y="9" width="4" height="6" rx="1"/>
            <rect x="7" y="11" width="10" height="2" rx="1"/>
        </svg>
    </div>

    <div class="exercise-info">
        <div class="exercise-name">{{ $exercise->exercise->name }}</div>
        <div class="chips">
            <span class="chip chip--series">{{ $exercise->sets }} séries</span>
            <span class="chip chip--reps">{{ $exercise->reps }} reps</span>
            <span class="chip chip--rest">{{ $exercise->rest }}s descanso</span>
        </div>
    </div>

    <button class="btn-play" title="Iniciar">
        <svg viewBox="0 0 10 12"><polygon points="0,0 10,6 0,12"/></svg>
    </button>

</li>

@endforeach

</ul>

@endif

</div>
</div>

</x-app-layout>