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
                       <h1 class="workout-form-title">Criar Exercício</h1>
                   </div>
                      <a href="/exercises" class="workout-form-back">← Voltar</a>
                </div>
                <div class="profile-card">
                    <form action="/exercises" method="POST">
                        @csrf

                        <div class="profile-field">
                            <label>Nome do exercício</label>
                            <input type="text" name="name" placeholder="Ex: Supino Reto">
                        </div>

                        <div class="profile-field">
                            <label>Grupo muscular</label>
                            <input type="text" name="muscle_group" placeholder="Ex: Peito">
                        </div>

                        <div class="profile-field">
                            <label>Descrição</label>
                           <textarea name="description" class="profile-textarea" placeholder="Descrição do exercício..."></textarea>
                        </div>

                        <div class="profile-form-row"> <button type="submit" class="btn-save">Salvar</button> <a href="/exercises" class="btn-cancel" style="text-decoration:none;">Cancelar</a> </div>
                    </form>
                </div>

            </div>
        </div>

    </div>

</x-app-layout>