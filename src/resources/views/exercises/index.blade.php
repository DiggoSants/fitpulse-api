<x-app-layout>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endpush

<div class="py-6">
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8" style="max-width:768px;">

    <div class="dashboard-welcome">
        <div>
            <p class="section-label" style="margin-bottom:4px;">BIBLIOTECA</p>
            <h1 style="font-size:22px; font-weight:700; color:var(--text-white); margin:0;">Exercícios</h1>
        </div>
        <a href="/exercises/create" class="btn-save" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" style="stroke:#fff; stroke-width:2; stroke-linecap:round;">
                <line x1="7" y1="1" x2="7" y2="13"/><line x1="1" y1="7" x2="13" y2="7"/>
            </svg>
            Novo Exercício
        </a>
    </div>

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
            <div class="exercise-name">{{ $exercise->name }}</div>
            <div class="exercise-muscle">{{ $exercise->muscle_group }}</div>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
            <a href="/exercises/{{ $exercise->id }}/edit"
               style="font-size:12px; font-weight:600; color:var(--text-muted); text-decoration:none; padding:6px 12px; border:1px solid var(--border); border-radius:var(--radius-pill); transition:color .2s, border-color .2s;"
               onmouseover="this.style.borderColor='rgba(214,21,50,.4)'; this.style.color='var(--red-light)'"
               onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--text-muted)'">
               ✏️ Editar
            </a>

            <form action="/exercises/{{ $exercise->id }}" method="POST" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete" style="font-size:12px; padding:6px 12px;">
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