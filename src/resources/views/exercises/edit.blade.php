<x-app-layout>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endpush

<div class="form-page">

    <div class="form-watermark" aria-hidden="true">
        <span>FIT</span>
        <span>PULSE</span>
    </div>

    <div class="form-content py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" style="max-width:560px;">

       <div class="workout-form-header">
    <div>
        <p class="workout-form-kicker">EXERCÍCIOS</p>
        <h1 class="workout-form-title">Editar Exercício</h1>
    </div>

    <a href="/exercises" class="workout-form-back">
        ← Voltar
    </a>
</div>

        <div class="profile-card">
            <form action="/exercises/{{ $exercise->id }}" method="POST">
                @csrf
                @method('PUT')

                <div class="profile-field">
                    <label>Nome do exercício</label>
                    <input type="text" name="name" value="{{ $exercise->name }}">
                </div>

                <div class="profile-field">
                    <label>Grupo muscular</label>
                    <input type="text" name="muscle_group" value="{{ $exercise->muscle_group }}">
                </div>

                <div class="profile-field">
                    <label>Descrição</label>
                    <textarea name="description" class="profile-textarea">{{ $exercise->description }}</textarea>
                </div>

                <div class="profile-form-row">
                    <button type="submit" class="btn-save">Atualizar</button>
                    <a href="/exercises" class="btn-cancel" style="text-decoration:none;">Cancelar</a>
                </div>
            </form>
        </div>

    </div>
    </div>

</div>

</x-app-layout>