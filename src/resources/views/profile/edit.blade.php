<x-app-layout>
    <x-slot name="header"></x-slot>

    <div class="ex-form-page">

        <a href="/exercises" class="btn-back">
            <svg viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            Voltar
        </a>

        <h2 class="ex-form-page__title">Editar Exercício</h2>

        {{-- GIF atual --}}
        @if($exercise->gif_url)
        <div style="margin-bottom:1.5rem;">
            <p class="section-label">GIF atual</p>
            <div class="gif-preview-card gif-preview--visible" style="pointer-events:none;">
                <div class="gif-preview-thumb">
                    <img id="current-gif" src="{{ $exercise->gif_url }}" alt="{{ $exercise->name }}">
                </div>
                <div>
                    <p class="gif-preview-name">{{ $exercise->name }}</p>
                    <p class="gif-preview-target">{{ $exercise->muscle_group }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Busca novo GIF --}}
        <div class="gif-search">
            <p class="section-label">Trocar GIF (opcional)</p>
            <div class="gif-search__row">
                <input type="text" id="gif-input" class="gif-search__input"
                       value="{{ $exercise->name }}" placeholder="Buscar exercício...">
                <button type="button" class="gif-search__btn" onclick="searchGif()">Buscar</button>
            </div>
            <div class="gif-results" id="gif-results"></div>
        </div>

        <form action="/exercises/{{ $exercise->id }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="gif_url" id="f-gif-url" value="{{ $exercise->gif_url }}">

            <div class="ex-form-card">

                <div class="ex-field">
                    <label>Nome do exercício</label>
                    <input type="text" name="name" value="{{ $exercise->name }}">
                </div>

                <div class="ex-field">
                    <label>Grupo muscular</label>
                    <input type="text" name="muscle_group" value="{{ $exercise->muscle_group }}">
                </div>

                <div class="ex-field">
                    <label>Descrição</label>
                    <textarea name="description" rows="3">{{ $exercise->description }}</textarea>
                </div>

            </div>

            <div class="ex-form-actions">
                <button type="submit" class="btn-save">Atualizar</button>
                <a href="/exercises" class="btn-cancel" style="text-decoration:none; display:inline-flex; align-items:center;">Cancelar</a>
            </div>

        </form>

    </div>

    <script>
    async function searchGif() {
        const q = document.getElementById('gif-input').value.trim();
        if (!q) return;
        const box = document.getElementById('gif-results');
        box.style.display = 'flex';
        box.innerHTML = '<p style="font-size:13px;color:var(--text-muted);padding:8px 0;">Buscando...</p>';
        try {
            const res = await fetch(
                `https://exercisedb.p.rapidapi.com/exercises/name/${encodeURIComponent(q)}?limit=8&offset=0`,
                { headers: { 'x-rapidapi-host': 'exercisedb.p.rapidapi.com', 'x-rapidapi-key': '{{ config("services.exercisedb.key") }}' } }
            );
            const data = await res.json();
            if (!Array.isArray(data) || !data.length) {
                box.innerHTML = '<p style="font-size:13px;color:var(--text-muted);padding:8px 0;">Nenhum resultado.</p>';
                return;
            }
            box.innerHTML = data.map(ex => `
                <div class="gif-result-item" onclick="selectGif(this,'${ex.gifUrl}','${ex.name}','${ex.target}','${ex.bodyPart}')">
                    <div class="gif-result-thumb"><img src="${ex.gifUrl}" loading="lazy"></div>
                    <div>
                        <p class="gif-result-name">${ex.name}</p>
                        <p class="gif-result-target">${ex.target} · ${ex.bodyPart}</p>
                    </div>
                </div>
            `).join('');
        } catch(e) {
            box.innerHTML = '<p style="font-size:13px;color:var(--red-light);padding:8px 0;">Erro — verifique a chave da API.</p>';
        }
    }

    function selectGif(el, gifUrl, name, target, bodyPart) {
        document.querySelectorAll('.gif-result-item').forEach(i => i.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('f-gif-url').value = gifUrl;
        const cur = document.getElementById('current-gif');
        if (cur) cur.src = gifUrl;
    }

    document.getElementById('gif-input').addEventListener('keydown', e => {
        if (e.key === 'Enter') { e.preventDefault(); searchGif(); }
    });
    </script>

</x-app-layout>