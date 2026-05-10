<x-app-layout>
    @php
        $workoutCreateUrl = route('workouts.create', request()->filled('student_id') ? ['student_id' => request('student_id')] : []);
    @endphp

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
                    <a href="{{ $workoutCreateUrl }}" class="workout-form-back">← Voltar</a>
                </div>

                <div class="profile-card">
                    <form action="/exercises" method="POST" id="exercise-form">
                        @csrf

                        {{-- Campo oculto que armazena a URL da imagem escolhida --}}
                        <input type="hidden" name="image_url" id="image_url_input">

                        <div class="profile-field">
                            <label>Nome do exercício</label>
                            <input
                                type="text"
                                name="name"
                                id="exercise-name"
                                placeholder="Ex: Supino Reto"
                                autocomplete="off"
                            >
                        </div>

                        {{-- PICKER DE IMAGEM --}}
                        <div class="profile-field" id="image-picker-wrap" style="display:none;">
                            <label style="display:flex; align-items:center; justify-content:space-between;">
                                <span>Imagem do exercício</span>
                                <span id="img-search-status" style="font-size:11px; color:var(--text-muted); font-weight:500;"></span>
                            </label>

                            {{-- Imagem selecionada --}}
                            <div id="selected-image-preview" style="display:none; margin-bottom:12px; position:relative;">
                                <img
                                    id="selected-img"
                                    src=""
                                    alt="Imagem selecionada"
                                    style="width:100%; max-height:180px; object-fit:contain;
                                           border-radius:12px; background:rgba(255,255,255,0.05);
                                           border:1px solid rgba(74,222,128,0.3);"
                                >
                                <button
                                    type="button"
                                    onclick="clearImage()"
                                    style="position:absolute; top:8px; right:8px;
                                           background:rgba(0,0,0,0.55); border:1px solid rgba(255,255,255,0.15);
                                           color:#fff; border-radius:50%; width:26px; height:26px;
                                           font-size:13px; cursor:pointer; display:flex;
                                           align-items:center; justify-content:center;"
                                >✕</button>
                                <span style="display:block; text-align:center; font-size:11px;
                                             color:#4ade80; margin-top:6px; font-weight:600;">
                                    ✓ Imagem selecionada
                                </span>
                            </div>

                            {{-- Grade de opções --}}
                            <div id="image-grid" style="
                                display:grid;
                                grid-template-columns: repeat(4, 1fr);
                                gap:8px;
                            "></div>

                            {{-- Empty / Erro --}}
                            <p id="img-empty" style="display:none; font-size:12px; color:var(--text-muted);
                                                     text-align:center; padding:16px 0; opacity:.6;">
                                Nenhuma imagem encontrada para este exercício.
                            </p>

                            {{-- Botão para pular --}}
                            <button
                                type="button"
                                onclick="clearImage()"
                                id="skip-image-btn"
                                style="display:none; margin-top:10px; font-size:11px; color:var(--text-muted);
                                       background:none; border:none; cursor:pointer; text-decoration:underline;
                                       width:100%; text-align:center;"
                            >
                                Continuar sem imagem
                            </button>
                        </div>

                        <div class="profile-field">
                            <label>Grupo muscular</label>
                            <input type="text" name="muscle_group" placeholder="Ex: Peito">
                        </div>

                        <div class="profile-field">
                            <label>Descrição</label>
                            <textarea name="description" class="profile-textarea" placeholder="Descrição do exercício..."></textarea>
                        </div>

                        <div class="profile-form-row">
                            <button type="submit" class="btn-save">Salvar</button>
                            <a href="{{ $workoutCreateUrl }}" class="btn-cancel" style="text-decoration:none;">Cancelar</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>

    <style>
        .img-option {
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid transparent;
            cursor: pointer;
            background: rgba(255,255,255,0.04);
            transition: border-color .15s, transform .15s;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .img-option:hover {
            border-color: rgba(214,21,50,0.45);
            transform: scale(1.03);
        }
        .img-option.selected {
            border-color: #4ade80;
        }
        .img-option img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: rgba(255,255,255,0.06);
        }

        /* Light mode */
        [data-theme="light"] .img-option {
            background: rgba(0,0,0,0.03);
        }
        [data-theme="light"] .img-option img {
            background: #f5f5f5;
        }
        [data-theme="light"] #selected-img {
            background: #f5f5f5 !important;
            border-color: rgba(22,163,74,0.4) !important;
        }
        [data-theme="light"] #skip-image-btn {
            color: rgba(0,0,0,0.40) !important;
        }
    </style>

    <script>
    const IMAGE_SEARCH_URL = "{{ route('exercise.images') }}";

    let debounceTimer = null;
    let lastQuery     = '';
    let selectedUrl   = '';

    document.getElementById('exercise-name').addEventListener('input', function () {
        const val = this.value.trim();
        clearTimeout(debounceTimer);

        if (val.length < 3) {
            hidePicker();
            return;
        }

        debounceTimer = setTimeout(() => {
            if (val !== lastQuery) {
                lastQuery = val;
                fetchImages(val);
            }
        }, 600);
    });

    async function fetchImages(query) {
        const wrap    = document.getElementById('image-picker-wrap');
        const status  = document.getElementById('img-search-status');
        const grid    = document.getElementById('image-grid');
        const empty   = document.getElementById('img-empty');
        const skipBtn = document.getElementById('skip-image-btn');

        wrap.style.display  = 'block';
        grid.innerHTML      = '';
        empty.style.display = 'none';
        skipBtn.style.display = 'none';
        status.textContent  = 'Buscando imagens...';

        // Skeleton
        for (let i = 0; i < 4; i++) {
            const sk = document.createElement('div');
            sk.className = 'sk';
            sk.style.cssText = 'aspect-ratio:1; border-radius:10px;';
            grid.appendChild(sk);
        }

        try {
            const res  = await fetch(IMAGE_SEARCH_URL + '?q=' + encodeURIComponent(query));
            const data = await res.json();

            grid.innerHTML = '';

            if (!data.images || data.images.length === 0) {
                status.textContent  = '';
                empty.style.display = 'block';
                skipBtn.style.display = 'block';
                return;
            }

            status.textContent = data.images.length + ' opção(ões) encontrada(s)';
            skipBtn.style.display = 'block';

            data.images.forEach(item => {
    const url = item.url ?? item; // ← linha nova
    const btn = document.createElement('button');
    btn.type      = 'button';
    btn.className = 'img-option';
    btn.title     = 'Selecionar esta imagem';

    const img = document.createElement('img');
    img.src   = url;
    img.alt   = query;
    img.onerror = () => { btn.style.display = 'none'; };

    btn.appendChild(img);
    btn.addEventListener('click', () => selectImage(url, btn));
    grid.appendChild(btn);
});

        } catch (e) {
            grid.innerHTML     = '';
            status.textContent = '';
            empty.style.display = 'block';
            skipBtn.style.display = 'block';
        }
    }

    function selectImage(url, btn) {
        // Remove seleção anterior
        document.querySelectorAll('.img-option').forEach(b => b.classList.remove('selected'));
        btn.classList.add('selected');

        selectedUrl = url;
        document.getElementById('image_url_input').value = url;

        // Mostra preview
        const preview = document.getElementById('selected-image-preview');
        document.getElementById('selected-img').src = url;
        preview.style.display = 'block';

        // Oculta a grade
        document.getElementById('image-grid').style.display       = 'none';
        document.getElementById('img-search-status').textContent  = '';
        document.getElementById('skip-image-btn').style.display   = 'none';
    }

    function clearImage() {
        selectedUrl = '';
        document.getElementById('image_url_input').value = '';
        document.getElementById('selected-image-preview').style.display = 'none';
        document.getElementById('image-grid').style.display              = 'grid';

        const query = document.getElementById('exercise-name').value.trim();
        if (query.length >= 3) {
            document.getElementById('img-search-status').textContent = '';
            // Reexibe as opções sem rebuscar
            document.querySelectorAll('.img-option').forEach(b => {
                b.classList.remove('selected');
                b.style.display = '';
            });
            document.getElementById('skip-image-btn').style.display = 'block';
        }
    }

    function hidePicker() {
        document.getElementById('image-picker-wrap').style.display = 'none';
        document.getElementById('image_url_input').value           = '';
        document.getElementById('image-grid').innerHTML            = '';
        document.getElementById('selected-image-preview').style.display = 'none';
        selectedUrl = '';
        lastQuery   = '';
    }
    </script>

</x-app-layout>
