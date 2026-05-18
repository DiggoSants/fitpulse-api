<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="dash-hero" style="margin-bottom:24px;">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Lojinha</div>
                        <h2 class="dash-hero__title">Compre produtos</h2>
                        <p class="dash-hero__sub">Explore suplementos e acessórios disponíveis.</p>
                    </div>
                </div>
            </div>

            <div style="display:flex; flex-wrap:wrap; gap:10px; margin-bottom:24px;">
                <button type="button" class="shop-filter-btn is-active" onclick="shopFilter('all', this)">Todos</button>
                <button type="button" class="shop-filter-btn" onclick="shopFilter('suplemento', this)">Suplementos</button>
                <button type="button" class="shop-filter-btn" onclick="shopFilter('acessorio', this)">Acessórios</button>
            </div>

            <div id="shop-skeleton" class="shop-grid">
                @for ($i = 0; $i < 6; $i++)
                    <div class="shop-card-skeleton">
                        <div class="shop-card-skeleton__img"></div>
                        <div style="padding:14px 16px; display:grid; gap:10px;">
                            <div style="width:80%; height:16px; background:rgba(255,255,255,0.08); border-radius:999px;"></div>
                            <div style="width:100%; height:12px; background:rgba(255,255,255,0.06); border-radius:999px;"></div>
                            <div style="width:60%; height:12px; background:rgba(255,255,255,0.06); border-radius:999px;"></div>
                        </div>
                    </div>
                @endfor
            </div>

            <div id="shop-empty" style="display:none; padding:40px 20px; text-align:center; color:rgba(255,255,255,0.55); border:1px solid rgba(255,255,255,0.06); border-radius:18px; background:rgba(255,255,255,0.03);">
                Nenhum produto disponível no momento.
            </div>

            <div id="shop-grid" class="shop-grid" style="display:none;"></div>
        </div>
    </div>

   <div id="shop-modal-overlay" class="shop-modal-overlay" style="display:none;">
    <div class="shop-modal">
        <div class="shop-modal__header">
            <h3 class="shop-modal__title">Confirmar compra</h3>
            <button type="button" class="shop-modal__close" onclick="closeShopModal()">✕</button>
        </div>
        <div class="shop-modal__body">
            <div class="shop-modal__img-wrap">
                <img id="shop-modal-img" src="" alt="" class="shop-modal__img" style="display:none;" />
                <div id="shop-modal-img-placeholder" class="shop-modal__img-placeholder">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                         style="stroke:currentColor; stroke-width:1.5; opacity:.35;">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                </div>
            </div>
            <div>
                <div id="shop-modal-name" class="shop-modal__product-name"></div>
                <div id="shop-modal-cat" class="shop-modal__product-cat"></div>
                <div id="shop-modal-price" class="shop-modal__product-price"></div>
            </div>
        </div>
        <div class="shop-modal__qty-row">
            <div class="shop-modal__qty-label">Quantidade</div>
            <div class="shop-modal__qty-ctrl">
                <button type="button" class="shop-qty-btn" onclick="changeQty(-1)">-</button>
                <div id="shop-modal-qty" class="shop-modal__qty-val">1</div>
                <button type="button" class="shop-qty-btn" onclick="changeQty(1)">+</button>
            </div>
        </div>
        <div class="shop-modal__total-row">
            <span>Total</span>
            <strong id="shop-modal-total" class="shop-modal__total-val">R$ 0,00</strong>
        </div>
        <div class="shop-modal__footer">
            <button type="button" class="shop-modal__btn-cancel" onclick="closeShopModal()">Cancelar</button>
            <button type="button" id="shop-modal-confirm-btn" class="shop-modal__btn" onclick="confirmPurchase()">Confirmar compra</button>
        </div>
    </div>
</div>

<div id="shop-toast" class="shop-toast" style="display:none;"></div>
    <script>
        const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
        const ENDPOINT_PRODUCTS = "{{ route('products.index', [], false) }}";
        const ENDPOINT_SALE     = "{{ route('sales.store', [], false) }}";

        let allProducts    = [];
        let currentFilter  = 'all';
        let selectedProduct = null;
        let currentQty     = 1;

        async function loadProducts() {
            try {
                const res  = await fetch(ENDPOINT_PRODUCTS, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();
                allProducts = json.data ?? [];

                document.getElementById('shop-skeleton').style.display = 'none';

                if (!allProducts.length) {
                    document.getElementById('shop-empty').style.display = 'block';
                    return;
                }

                renderProducts(allProducts);
                document.getElementById('shop-grid').style.display = 'grid';
            } catch (e) {
                document.getElementById('shop-skeleton').style.display = 'none';
                document.getElementById('shop-empty').style.display    = 'block';
                console.error('Shop error:', e);
            }
        }

        function renderProducts(products) {
            const grid = document.getElementById('shop-grid');
            grid.innerHTML = '';

            const filtered = currentFilter === 'all'
                ? products
                : products.filter(p => p.category === currentFilter);

            if (!filtered.length) {
                grid.style.display = 'none';
                document.getElementById('shop-empty').style.display = 'block';
                return;
            }

            document.getElementById('shop-empty').style.display = 'none';
            grid.style.display = 'grid';

            filtered.forEach(p => {
                const price = parseFloat(p.price).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                const catLabel = p.category === 'suplemento' ? 'Suplemento' : 'Acessório';
                const catClass  = p.category === 'suplemento' ? 'shop-badge--sup' : 'shop-badge--ace';

                const card = document.createElement('div');
                card.className        = 'shop-card';
                card.dataset.category = p.category;

                card.innerHTML = `
                    <div class="shop-card__img-wrap">
                        ${p.image
                            ? `<img src="${p.image}" alt="${p.name}" class="shop-card__img" loading="lazy">`
                            : `<div class="shop-card__img-placeholder">
                                   <svg width="32" height="32" viewBox="0 0 24 24" fill="none"
                                        style="stroke:var(--text-muted); stroke-width:1.5; opacity:.30;">
                                       <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                       <line x1="3" y1="6" x2="21" y2="6"/>
                                       <path d="M16 10a4 4 0 0 1-8 0"/>
                                   </svg>
                               </div>`
                        }
                        <span class="shop-badge ${catClass}">${catLabel}</span>
                    </div>
                    <div class="shop-card__body">
                        <p class="shop-card__name">${p.name}</p>
                        ${p.description ? `<p class="shop-card__desc">${p.description}</p>` : ''}
                        <div class="shop-card__footer">
                            <span class="shop-card__price">${price}</span>
                            <button type="button" class="shop-card__btn" onclick="openShopModal(${JSON.stringify(p).replace(/"/g, '&quot;')})">
                                Comprar
                            </button>
                        </div>
                    </div>
                `;

                grid.appendChild(card);
            });
        }

        window.shopFilter = function (type, btn) {
            document.querySelectorAll('.shop-filter-btn').forEach(b => b.classList.remove('is-active'));
            if (btn) btn.classList.add('is-active');
            currentFilter = type;
            renderProducts(allProducts);
        };

        window.openShopModal = function (product) {
            selectedProduct = product;
            currentQty      = 1;

            const img         = document.getElementById('shop-modal-img');
            const placeholder = document.getElementById('shop-modal-img-placeholder');

            if (product.image) {
                img.src           = product.image;
                img.style.display = 'block';
                placeholder.style.display = 'none';
            } else {
                img.style.display         = 'none';
                placeholder.style.display = 'flex';
            }

            document.getElementById('shop-modal-name').textContent  = product.name;
            document.getElementById('shop-modal-cat').textContent   = product.category === 'suplemento' ? 'Suplemento' : 'Acessório';
            document.getElementById('shop-modal-qty').textContent   = '1';
            updateModalTotal();

            document.getElementById('shop-modal-overlay').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        };

        window.closeShopModal = function () {
            document.getElementById('shop-modal-overlay').style.display = 'none';
            document.body.style.overflow = '';
            selectedProduct = null;
        };

        window.changeQty = function (delta) {
            currentQty = Math.max(1, currentQty + delta);
            document.getElementById('shop-modal-qty').textContent = currentQty;
            updateModalTotal();
        };

        function updateModalTotal() {
            if (!selectedProduct) return;
            const total = (parseFloat(selectedProduct.price) * currentQty)
                .toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            document.getElementById('shop-modal-price').textContent = parseFloat(selectedProduct.price)
                .toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) + ' / un.';
            document.getElementById('shop-modal-total').textContent = total;
        }

        window.confirmPurchase = async function () {
            if (!selectedProduct) return;

            const btn         = document.getElementById('shop-modal-confirm-btn');
            btn.disabled      = true;
            btn.textContent = 'Processando...';

            try {
                const res = await fetch(ENDPOINT_SALE, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({ product_id: selectedProduct.id, quantity: currentQty }),
                });

                const data = await res.json();

                if (res.ok) {
                    closeShopModal();
                    showShopToast('Compra realizada com sucesso! 🎉', 'success');
                } else {
                    showShopToast(data.message || 'Erro ao processar compra.', 'error');
                }
            } catch (e) {
                showShopToast('Erro de conexão. Tente novamente.', 'error');
            } finally {
                btn.disabled    = false;
                btn.textContent = 'Confirmar compra';
            }
        };

        function showShopToast(msg, type) {
            const toast         = document.getElementById('shop-toast');
            toast.textContent   = msg;
            toast.style.display = 'flex';
            toast.className     = 'shop-toast' + (type === 'error' ? ' shop-toast--error' : '');

            setTimeout(() => {
                toast.style.opacity   = '0';
                toast.style.transform = 'translateY(-6px)';
                setTimeout(() => {
                    toast.style.display   = 'none';
                    toast.style.opacity   = '1';
                    toast.style.transform = 'none';
                }, 300);
            }, 3500);
        }

        document.addEventListener('DOMContentLoaded', loadProducts);
    </script>
</x-app-layout>
