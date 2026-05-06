<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="dash-hero">
                <div class="dash-hero__ring"></div>
                <div class="dash-hero__inner">
                    <div>
                        <div class="dash-hero__eyebrow">Relatórios</div>
                        <h2 class="dash-hero__title">Vendas da Lojinha</h2>
                        <p class="dash-hero__sub">Receita, lucro e volume por produto.</p>
                    </div>
                    <div class="dash-hero__right">
                        <span class="dash-hero__pulse">
                            <span class="dash-hero__pulse-dot"></span>
                            GERENTE
                        </span>
                        <a href="{{ route('dashboard') }}" class="btn-ghost" style="text-decoration:none;">
                            ← Voltar ao painel
                        </a>
                        @if(Auth::user()->isManager())
                            <a href="{{ route('shop.manager') }}" class="btn-ghost" style="text-decoration:none;">
                                Gerenciar produtos
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Cards de resumo --}}
            <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:28px;">
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Receita Total</span>
                    <strong class="mgr-stat-card__value">
                        R$ {{ number_format($summary['total_revenue'], 2, ',', '.') }}
                    </strong>
                    <span class="mgr-stat-card__sub">todas as vendas</span>
                </div>
                <div class="mgr-stat-card mgr-stat-card--green">
                    <span class="mgr-stat-card__label">Lucro Total</span>
                    <strong class="mgr-stat-card__value">
                        R$ {{ number_format($summary['total_profit'], 2, ',', '.') }}
                    </strong>
                    <span class="mgr-stat-card__sub">receita - custo</span>
                </div>
                <div class="mgr-stat-card">
                    <span class="mgr-stat-card__label">Itens Vendidos</span>
                    <strong class="mgr-stat-card__value">{{ $summary['total_sales'] }}</strong>
                    <span class="mgr-stat-card__sub">unidades</span>
                </div>
            </div>

            {{-- Tabela de produtos --}}
            <div class="mgr-table-wrap">
                <table class="mgr-table">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Categoria</th>
                            <th>Status</th>
                            <th>Qtd. Vendida</th>
                            <th>Receita</th>
                            <th>Lucro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $p)
                            <tr>
                                <td>
                                    <span style="font-weight:700; color:var(--text-white);">
                                        {{ $p['name'] }}
                                    </span>
                                </td>
                                <td>
                                    @if($p['category'] === 'suplemento')
                                        <span class="shop-badge shop-badge--sup" style="position:static;">Suplemento</span>
                                    @else
                                        <span class="shop-badge shop-badge--ace" style="position:static;">Acessório</span>
                                    @endif
                                </td>
                                <td>
                                    @if($p['status'] === 'active')
                                        <span class="mgr-badge-ok">Ativo</span>
                                    @else
                                        <span class="mgr-badge-bad">Inativo</span>
                                    @endif
                                </td>
                                <td>{{ $p['total_quantity'] }}</td>
                                <td>R$ {{ number_format($p['total_revenue'], 2, ',', '.') }}</td>
                                <td style="color: {{ $p['total_profit'] >= 0 ? '#4ade80' : '#f87171' }}; font-weight:700;">
                                    R$ {{ number_format($p['total_profit'], 2, ',', '.') }}
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
</x-app-layout>