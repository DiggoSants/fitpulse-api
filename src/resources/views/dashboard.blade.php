<x-app-layout>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @endpush

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <h2 class="text-2xl font-bold mb-6">
                Seu Treino
            </h2>

            <a href="{{ route('workout.create') }}"
                style="
    display:inline-block;
    margin-bottom:20px;
    padding:10px 15px;
    background:#4CAF50;
    color:white;
    border-radius:8px;
    text-decoration:none;
   ">
                + Criar Treino
            </a>

            @if(isset($workout))

            <h3 class="mb-4">
                {{ $workout->name ?? 'Treino' }}

                <a href="{{ route('workout.edit', $workout->id) }}"
                    style="margin-left: 10px; color: yellow;">
                    ✏️ Editar
                </a>
            </h3>
            <form action="{{ route('workout.destroy', $workout->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')

                <button type="submit"
                    style="color:red; margin-left:10px; background:none; border:none; cursor:pointer;">
                    🗑️ Deletar
                </button>
            </form>

            @if($exercises->count())

            <ul class="exercise-list">

                @foreach($exercises as $item)

                <li class="exercise-card">

                    <div class="exercise-thumb">

                        {{-- FUTURO: IMAGEM --}}
                        {{-- <img src="{{ $item->exercise->image_url }}"> --}}

                        {{-- fallback atual --}}
                        <svg viewBox="0 0 24 24">
                            <rect x="2" y="9" width="4" height="6" rx="1" />
                            <rect x="18" y="9" width="4" height="6" rx="1" />
                            <rect x="7" y="11" width="10" height="2" rx="1" />
                        </svg>

                    </div>

                    <div class="exercise-info">
                        <div class="exercise-name">
                            {{ $item->exercise->name }}
                        </div>

                        <div class="chips">
                            <span class="chip chip--series">
                                {{ $item->sets }} séries
                            </span>

                            <span class="chip chip--reps">
                                {{ $item->reps }} reps
                            </span>

                            <span class="chip chip--rest">
                                {{ $item->rest_time ?? 0 }}s descanso
                            </span>
                        </div>
                    </div>

                    <button class="btn-play" title="Iniciar">
                        <svg viewBox="0 0 10 12">
                            <polygon points="0,0 10,6 0,12" />
                        </svg>
                    </button>

                </li>

                @endforeach

            </ul>

            @else
            <p class="empty-state">Nenhum exercício encontrado.</p>
            @endif

            @else
            <p class="empty-state">Nenhum treino disponível.</p>
            @endif

        </div>
    </div>

</x-app-layout>