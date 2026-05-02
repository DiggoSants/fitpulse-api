<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @endpush

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
                            <button type="submit" id="product-submit" class="btn-save" style="padding:12px 20px;">
                                Salvar produto
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
                                                <button type="button" class="btn-ghost"
                                                    style="font-size:12px; padding:8px 12px;"
                                                    onclick="editProduct({{ $product->id }})">
                                                    Editar
                                                </button>
                                                @if($product->status === 'active')
                                                    <button type="button" class="btn-ghost"
                                                        style="font-size:12px; padding:8px 12px; background:rgba(214,21,50,0.15); border-color:rgba(214,21,50,0.28); color:#f87171;"
                                                        onclick="deleteProduct({{ $product->id }})">
                                                        Inativar
                                                    </button>
                                                @else
                                                    <button type="button" class="btn-ghost"
                                                        style="font-size:12px; padding:8px 12px; background:rgba(34,197,94,0.12); border-color:rgba(34,197,94,0.28); color:#4ade80;"
                                                        onclick="restoreProduct({{ $product->id }})">
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
        const CSRF            = document.querySelector('meta[name="csrf-token"]').content;
        const PRODUCT_ENDPOINT = "{{ route('products.store') }}";
        const PRODUCT_BASE_URL = "{{ url('/products') }}";
        const managerProducts  = @json($managerProducts);

        let editingProductId = null;

        // ── Salvar / Atualizar produto ────────────────────────────────
        document.getElementById('product-form').addEventListener('submit', async function (event) {
            event.preventDefault();

            const data = {
                name:        document.getElementById('product-name').value.trim(),
                category:    document.getElementById('product-category').value,
                description: document.getElementById('product-description').value.trim(),
                image:       document.getElementById('product-image').value.trim(),
                price:       document.getElementById('product-price').value,
                cost:        document.getElementById('product-cost').value,
            };

            const url    = editingProductId ? `${PRODUCT_BASE_URL}/${editingProductId}` : PRODUCT_ENDPOINT;
            const method = editingProductId ? 'PUT' : 'POST';

            const res      = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
                body: JSON.stringify(data),
            });
            const response = await res.json();

            if (res.ok) {
                alert(response.message || 'Produto salvo com sucesso!');
                location.reload();
            } else {
                alert(response.message || 'Erro ao salvar produto.');
            }
        });

        // ── Editar produto (preenche formulário) ──────────────────────
        function editProduct(id) {
            const product = managerProducts.find(p => p.id === id);
            if (!product) return;

            editingProductId = id;

            document.getElementById('product-name').value        = product.name;
            document.getElementById('product-category').value    = product.category;
            document.getElementById('product-description').value = product.description || '';
            document.getElementById('product-image').value       = product.image || '';
            document.getElementById('product-price').value       = product.price;
            document.getElementById('product-cost').value        = product.cost;

            document.getElementById('product-submit').textContent  = 'Atualizar produto';
            document.getElementById('product-message').textContent = 'Editando produto existente.';
        }

        // ── Inativar produto ──────────────────────────────────────────
        async function deleteProduct(id) {
            if (!confirm('Inativar este produto?')) return;

            const res      = await fetch(`${PRODUCT_BASE_URL}/${id}`, {
                method:  'DELETE',
                headers: {
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
            });
            const response = await res.json();

            if (res.ok) {
                alert(response.message || 'Produto inativado.');
                location.reload();
            } else {
                alert(response.message || 'Erro ao inativar produto.');
            }
        }

        // ── Ativar produto ────────────────────────────────────────────
        async function restoreProduct(id) {
            if (!confirm('Ativar este produto?')) return;

            const res      = await fetch(`${PRODUCT_BASE_URL}/${id}/restore`, {
                method:  'POST',
                headers: {
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': CSRF,
                },
            });
            const response = await res.json();

            if (res.ok) {
                alert(response.message || 'Produto ativado.');
                location.reload();
            } else {
                alert(response.message || 'Erro ao ativar produto.');
            }
        }

        // ── Limpar formulário ─────────────────────────────────────────
        document.getElementById('product-reset').addEventListener('click', function () {
            editingProductId = null;
            document.getElementById('product-form').reset();
            document.getElementById('product-submit').textContent  = 'Salvar produto';
            document.getElementById('product-message').textContent = '';
        });
    </script>

</x-app-layout>