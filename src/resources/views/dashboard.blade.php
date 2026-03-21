<x-app-layout>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <h2>Meu Treino</h2>
            <h3>
              
                <a href="{{ route('workout.edit', $workout->id) }}"
                    style="margin-left: 10px; color: yellow;">
                    ✏️ Editar
                </a>
            </h3>
            @if(isset($workout))
            <h3>{{ $workout->name ?? 'Treino' }}</h3>

            @if($exercises->count())
            <div style="display: flex; flex-wrap: wrap; gap: 20px;">

                @foreach($exercises as $item)
                <div style="border: 1px solid #ccc; padding: 15px; width: 250px; border-radius: 10px;">

                    <h4>{{ $item->exercise->name }}</h4>

                    <div style="
    margin-top: 10px;
    height: 150px;
    background: #222;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
">

                        {{-- FUTURO: imagem --}}
                        {{-- <img src="{{ $item->exercise->image_url }}" style="width:100%; height:100%; object-fit:cover;"> --}}

                        {{-- FUTURO: vídeo --}}
                        {{--
    <video controls style="width:100%; height:100%;">
        <source src="{{ $item->exercise->video_url }}">
                        </video>
                        --}}

                        <span style="color:#666;">Mídia do exercício</span>

                    </div>

                    <p><strong>Séries:</strong> {{ $item->sets }}</p>
                    <p><strong>Repetições:</strong> {{ $item->reps }}</p>

                </div>
                @endforeach

            </div>
            @else
            <p>Nenhum exercício encontrado.</p>
            @endif

            @else
            <p>Nenhum treino disponível.</p>
            @endif

        </div>
    </div>

</x-app-layout>