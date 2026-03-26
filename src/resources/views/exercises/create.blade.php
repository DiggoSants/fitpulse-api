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

                <div class="dashboard-welcome">
                    <div>
                        <p class="section-label" style="margin-bottom:4px;">EXERCÍCIOS</p>
                        <h1 style="font-size:22px; font-weight:700; color:var(--text-white); margin:0;">Criar Exercício</h1>
                    </div>
                    <a href="/exercises"
                        style="font-size:12px; font-weight:600; color:var(--text-muted); text-decoration:none; display:inline-flex; align-items:center; gap:5px; padding:6px 14px; border:1px solid var(--border); border-radius:var(--radius-pill);"> ← Voltar
                    </a>
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
                            <textarea name="description" placeholder="Descrição do exercício..."
                                style="width:100%; background:var(--surface-2); border:1px solid var(--border); border-radius:var(--radius-md); padding:11px 14px; font-size:14px; color:var(--text-white); outline:none; font-family:'Montserrat',sans-serif; resize:vertical; min-height:100px; box-sizing:border-box;"></textarea>
                        </div>

                        <div class="profile-form-row"> <button type="submit" class="btn-save">Salvar</button> <a href="/exercises" class="btn-cancel" style="text-decoration:none;">Cancelar</a> </div>
                    </form>
                </div>

            </div>
        </div>

    </div>

</x-app-layout>