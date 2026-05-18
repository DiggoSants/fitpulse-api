<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Hero --}}
            <div class="dash-hero" style="margin-bottom:28px;">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Lojinha</div>
                        <h2 class="dash-hero__title">Gerenciar produtos</h2>
                        <p class="dash-hero__sub">Cadastre suplementos e acessórios para a lojinha do aluno.</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            GERENTE
                        </span>
                        <a href="{{ route('reports.shop.products') }}" class="btn-ghost" style="text-decoration:none;">
                            Ver relatório
                        </a>
                    </div>
                </div>
            </div>

            <div style="display:grid; gap:24px;">

                {{-- Formulário de cadastro --}}
                <div class="mgr-table-wrap" style="padding:24px; border-radius:20px; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08);">
                    <h3 style="font-size:18px; margin:0 0 8px;">Cadastrar novo produto</h3>
                    <form id="product-form" style="display:grid; gap:14px;">

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                            <label style="display:grid; gap:6px; font-size:12px; color:rgba(255,255,255,0.75);">
                                Nome
                                <input id="product-name" type="text" name="name" required
                                    style="width:100%; padding:12px 14px; border-radius:12px; border:1px solid rgba(255,255,255,0.10); background:rgba(255,255,255,0.05); color:#fff;" />
                            </label>
                            <label style="display:grid; gap:6px; font-size:12px; color:rgba(255,255,255,0.75);">
                                Categoria
                                <select id="product-category" name="category" required
                                    style="width:100%; padding:12px 14px; border-radius:12px; border:1px solid rgba(255,255,255,0.10); background:rgba(255,255,255,0.05); color:#fff;">
                                    <option value="suplemento">Suplemento</option>
                                    <option value="acessorio">Acessório</option>
                                </select>
                            </label>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                            <label style="display:grid; gap:6px; font-size:12px; color:rgba(255,255,255,0.75);">
                                Preço
                                <input id="product-price" type="number" name="price" min="0" step="0.01" required
                                    style="width:100%; padding:12px 14px; border-radius:12px; border:1px solid rgba(255,255,255,0.10); background:rgba(255,255,255,0.05); color:#fff;" />
                            </label>
                            <label style="display:grid; gap:6px; font-size:12px; color:rgba(255,255,255,0.75);">
                                Custo
                                <input id="product-cost" type="number" name="cost" min="0" step="0.01" required
                                    style="width:100%; padding:12px 14px; border-radius:12px; border:1px solid rgba(255,255,255,0.10); background:rgba(255,255,255,0.05); color:#fff;" />
                            </label>
                        </div>

                        <label style="display:grid; gap:6px; font-size:12px; color:rgba(255,255,255,0.75);">
                            Imagem (URL)
                            <input id="product-image" type="url" name="image" placeholder="https://..."
                                style="width:100%; padding:12px 14px; border-radius:12px; border:1px solid rgba(255,255,255,0.10); background:rgba(255,255,255,0.05); color:#fff;" />
                        </label>

                        <label style="display:grid; gap:6px; font-size:12px; color:rgba(255,255,255,0.75);">
                            Descrição
                            <textarea id="product-description" name="description" rows="3"
                                style="width:100%; padding:12px 14px; border-radius:12px; border:1px solid rgba(255,255,255,0.10); background:rgba(255,255,255,0.05); color:#fff;"></textarea>
                        </label>

                        <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
                            <button type="submit" id="product-submit" class="btn-save" style="padding:12px 20px; gap:8px;">
                              <svg id="product-submit-spinner" width="14" height="14" viewBox="0 0 24 24" fill="none"
                                style="display:none; stroke:#fff; stroke-width:2.5; stroke-linecap:round; animation:spin .7s linear infinite;">
                                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                             </svg>
                             <span id="product-submit-label">Salvar produto</span>
                            </button>

                            <button type="button" id="product-reset" class="btn-ghost" style="padding:12px 20px;">
                                Limpar formulário
                            </button>
                            <span id="product-message" style="font-size:13px; color:rgba(255,255,255,0.68);"></span>
                        </div>

                    </form>
                </div>

                {{-- Tabela de produtos --}}
                <div class="mgr-table-wrap" style="padding:24px; border-radius:20px; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.08);">
                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:18px;">
                        <h3 style="font-size:18px; margin:0;">Produtos cadastrados</h3>
                        <span style="font-size:13px; color:rgba(255,255,255,0.65);">Total: {{ $products->count() }}</span>
                    </div>

                    <div class="mgr-table-wrap">
                        <table class="mgr-table">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Categoria</th>
                                    <th>Preço</th>
                                    <th>Custo</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <div class="mgr-student-cell">
                                                <div class="mgr-student-cell__avatar"
                                                    style="background:rgba(214,21,50,0.15); color:#f87171; font-size:11px;">
                                                    {{ strtoupper(substr($product->name, 0, 2)) }}
                                                </div>
                                                <div class="mgr-student-cell__content">
                                                    <span class="mgr-student-cell__name">{{ $product->name }}</span>
                                                    <span style="font-size:13px; color:var(--text-muted);">
                                                        {{ $product->description ?? '—' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($product->category === 'suplemento')
                                                <span class="shop-badge shop-badge--sup" style="position:static;">Suplemento</span>
                                            @else
                                                <span class="shop-badge shop-badge--ace" style="position:static;">Acessório</span>
                                            @endif
                                        </td>
                                        <td>R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                                        <td>R$ {{ number_format($product->cost, 2, ',', '.') }}</td>
                                        <td>
                                            @if($product->status === 'active')
                                                <span class="mgr-badge-ok">Ativo</span>
                                            @else
                                                <span class="mgr-badge-bad">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                                <button type="button" class="btn-ghost" style="font-size:12px; padding:8px 12px;"
                                                onclick="editProduct({{ $product->id }}, this)">
                                                Editar
                                            </button>
                                            @if($product->status === 'active')
                                            <button type="button" class="btn-ghost"
                                            style="font-size:12px; padding:8px 12px; background:rgba(214,21,50,0.15); border-color:rgba(214,21,50,0.28); color:#f87171;"
                                            onclick="deleteProduct({{ $product->id }}, this)">
                                            Inativar
                                        </button>
                                        @else
                                        <button type="button" class="btn-ghost"
                                        style="font-size:12px; padding:8px 12px; background:rgba(34,197,94,0.12); border-color:rgba(34,197,94,0.28); color:#4ade80;"
                                        onclick="restoreProduct({{ $product->id }}, this)">
                                        Ativar
                                    </button>
                                    @endif

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align:center; padding:32px; color:var(--text-muted); font-size:13px;">
                                            Nenhum produto cadastrado ainda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

<div id="confirm-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.65); backdrop-filter:blur(4px); z-index:99999; align-items:center; justify-content:center; padding:20px;">
    <div style="background:#161616; border:1px solid rgba(255,255,255,0.10); border-radius:20px; width:100%; max-width:360px; overflow:hidden; box-shadow:0 24px 60px rgba(0,0,0,0.50); animation:shopModalIn .22s ease;">
        <div style="padding:22px 24px 0;">
            <div style="width:44px; height:44px; border-radius:12px; background:rgba(214,21,50,0.12); border:1px solid rgba(214,21,50,0.25); display:flex; align-items:center; justify-content:center; margin-bottom:14px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="stroke:#f87171; stroke-width:2; stroke-linecap:round;">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <p id="confirm-text" style="font-size:15px; font-weight:700; color:#f5f5f5; margin:0 0 6px;"></p>
            <p id="confirm-sub" style="font-size:13px; color:rgba(255,255,255,0.45); margin:0 0 22px; line-height:1.5;"></p>
        </div>
        <div style="display:flex; gap:10px; padding:0 24px 22px;">
         <button onclick="confirmResolve(false)" class="confirm-btn-cancel">
            Cancelar
         </button>
         <button id="confirm-ok-btn" onclick="confirmResolve(true)" class="confirm-btn-ok">
           Confirmar
          </button>
        </div>
    </div>
</div>
    @php
        $managerProducts = $products->map(function ($product) {
            return [
                'id'          => $product->id,
                'name'        => $product->name,
                'category'    => $product->category,
                'description' => $product->description,
                'image'       => $product->image,
                'price'       => (float) $product->price,
                'cost'        => (float) $product->cost,
                'status'      => $product->status,
            ];
        })->values();
    @endphp

    <script>
        function showConfirm(text, sub, okLabel = 'Confirmar') {
    return new Promise(resolve => {
        document.getElementById('confirm-text').textContent = text;
        document.getElementById('confirm-sub').textContent  = sub;
        document.getElementById('confirm-ok-btn').textContent = okLabel;
        const overlay = document.getElementById('confirm-overlay');
        overlay.style.display = 'flex';
        window._confirmResolve = resolve;
    });
}

function confirmResolve(result) {
    document.getElementById('confirm-overlay').style.display = 'none';
    if (window._confirmResolve) window._confirmResolve(result);
}
        const CSRF             = document.querySelector('meta[name="csrf-token"]').content;
const PRODUCT_ENDPOINT = "{{ route('products.store', [], false) }}";
const PRODUCT_UPDATE_ENDPOINT = "{{ route('products.update', ['id' => '__ID__'], false) }}";
const PRODUCT_DELETE_ENDPOINT = "{{ route('products.destroy', ['id' => '__ID__'], false) }}";
const PRODUCT_RESTORE_ENDPOINT = "{{ route('products.restore', ['id' => '__ID__'], false) }}";
const managerProducts  = @json($managerProducts);

let editingProductId = null;

function setButtonLoading(btn, loading, originalText) {
    btn.disabled = loading;
    if (loading) {
        btn.dataset.original = btn.textContent.trim();
        btn.innerHTML = `
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                 style="stroke:currentColor; stroke-width:2.5; stroke-linecap:round; animation:spin .7s linear infinite; flex-shrink:0;">
                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
            </svg>
            ${originalText}`;
    } else {
        btn.disabled = false;
        btn.textContent = btn.dataset.original || originalText;
    }
}

function setSubmitLoading(loading) {
    const btn     = document.getElementById('product-submit');
    const spinner = document.getElementById('product-submit-spinner');
    const label   = document.getElementById('product-submit-label');
    btn.disabled             = loading;
    spinner.style.display    = loading ? 'block' : 'none';
}

function endpoint(template, id) {
    return template.replace('__ID__', encodeURIComponent(id));
}

async function readJsonResponse(res) {
    try {
        return await res.json();
    } catch (e) {
        if (res.status === 419) {
            return { message: 'Sua sessão expirou. Atualize a página e tente novamente.' };
        }
        if (res.status === 401 || res.status === 403) {
            return { message: 'Você não tem permissão para gerenciar produtos nesta sessão.' };
        }
        if (res.status >= 500) {
            return { message: 'Erro interno no servidor. Confira os logs do Railway.' };
        }
        return { message: 'O servidor respondeu de um jeito inesperado.' };
    }
}

document.getElementById('product-form').addEventListener('submit', async function (event) {
    event.preventDefault();
    setSubmitLoading(true);

    const data = {
        name:        document.getElementById('product-name').value.trim(),
        category:    document.getElementById('product-category').value,
        description: document.getElementById('product-description').value.trim(),
        image:       document.getElementById('product-image').value.trim(),
        price:       document.getElementById('product-price').value,
        cost:        document.getElementById('product-cost').value,
    };

    const url    = editingProductId ? endpoint(PRODUCT_UPDATE_ENDPOINT, editingProductId) : PRODUCT_ENDPOINT;
    const method = editingProductId ? 'PUT' : 'POST';

    try {
        const res      = await fetch(url, {
            method,
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify(data),
        });
        const response = await readJsonResponse(res);
        if (res.ok) {
            alert(response.message || 'Produto salvo com sucesso!');
            location.reload();
        } else {
            alert(response.errors ? Object.values(response.errors).flat().join('\n') : (response.message || 'Erro ao salvar produto.'));
            setSubmitLoading(false);
        }
    } catch (e) {
        alert('Não consegui falar com o servidor. Atualize a página e tente novamente.');
        setSubmitLoading(false);
    }
});

function editProduct(id, btn) {
    const product = managerProducts.find(p => p.id === id);
    if (!product) return;

    editingProductId = id;
    document.getElementById('product-name').value        = product.name;
    document.getElementById('product-category').value    = product.category;
    document.getElementById('product-description').value = product.description || '';
    document.getElementById('product-image').value       = product.image || '';
    document.getElementById('product-price').value       = product.price;
    document.getElementById('product-cost').value        = product.cost;

    document.getElementById('product-submit-label').textContent = 'Atualizar produto';
    document.getElementById('product-message').textContent      = 'Editando produto existente.';

    document.getElementById('product-form').scrollIntoView({ behavior: 'smooth' });
}

async function deleteProduct(id, btn) {
    const ok = await showConfirm('Inativar produto', 'O produto ficará invisível na lojinha do aluno.', 'Inativar');
    if (!ok) return;
    setButtonLoading(btn, true, 'Inativando...');

    try {
        const res      = await fetch(endpoint(PRODUCT_DELETE_ENDPOINT, id), {
            method: 'DELETE',
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
        });
        const response = await readJsonResponse(res);
        if (res.ok) { location.reload(); }
        else { alert(response.message || 'Erro ao inativar.'); setButtonLoading(btn, false, 'Inativar'); }
    } catch (e) {
        alert('Não consegui falar com o servidor. Atualize a página e tente novamente.');
        setButtonLoading(btn, false, 'Inativar');
    }
}

async function restoreProduct(id, btn) {
    const ok = await showConfirm('Ativar produto', 'O produto voltará a aparecer na lojinha do aluno.', 'Ativar');
    if (!ok) return;
    setButtonLoading(btn, true, 'Ativando...');

    try {
        const res      = await fetch(endpoint(PRODUCT_RESTORE_ENDPOINT, id), {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
        });
        const response = await readJsonResponse(res);
        if (res.ok) { location.reload(); }
        else { alert(response.message || 'Erro ao ativar.'); setButtonLoading(btn, false, 'Ativar'); }
    } catch (e) {
        alert('Não consegui falar com o servidor. Atualize a página e tente novamente.');
        setButtonLoading(btn, false, 'Ativar');
    }
}

document.getElementById('product-reset').addEventListener('click', function () {
    editingProductId = null;
    document.getElementById('product-form').reset();
    document.getElementById('product-submit-label').textContent = 'Salvar produto';
    document.getElementById('product-message').textContent      = '';
});

    </script>

</x-app-layout>
