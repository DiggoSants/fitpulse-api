<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Student;
use App\Models\Manager;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ShopController extends Controller
{
    /**
     * Verifica se o usuário autenticado é gerente.
     */
    private function isManager()
    {
        $user = Auth::user();
        if (!$user) return false;
        return Manager::where('user_id', $user->id)->exists();
    }

    /**
     * Lista produtos – alunos veem apenas disponíveis (estoque > 0);
     * gerente vê todos os produtos ativos (com estoque ou não).
     */
    public function index()
    {
        $query = Product::active();

        // Se não for gerente, filtra apenas produtos com estoque positivo
        if (!$this->isManager()) {
            $query->where('stock', '>', 0);
        }

        $products = $query->get()->map(function ($product) {
            return [
                'id'          => $product->id,
                'name'        => $product->name,
                'category'    => $product->category,
                'description' => $product->description,
                'image'       => $product->image,
                'price'       => $product->price,
            ];
        });

        return response()->json(['data' => $products]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'category'    => ['required', 'in:suplemento,acessorio'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'cost'        => ['required', 'numeric', 'min:0'],
        ], [
            'name.required'     => 'O nome do produto é obrigatório',
            'category.required' => 'A categoria é obrigatória',
            'category.in'       => 'Categoria inválida. Use: suplemento ou acessorio',
            'price.required'    => 'O preço é obrigatório',
            'cost.required'     => 'O custo é obrigatório',
        ]);

        $product = Product::create([
            'name'        => $request->name,
            'category'    => $request->category,
            'description' => $request->description,
            'image'       => $request->image,
            'price'       => $request->price,
            'cost'        => $request->cost,
            'status'      => 'active',
        ]);

        return response()->json([
            'message' => 'Produto cadastrado com sucesso!',
            'data'    => $product,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => ['sometimes', 'string', 'max:255'],
            'category'    => ['sometimes', 'in:suplemento,acessorio'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'string'],
            'price'       => ['sometimes', 'numeric', 'min:0'],
            'cost'        => ['sometimes', 'numeric', 'min:0'],
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->only([
            'name',
            'category',
            'description',
            'image',
            'price',
            'cost'
        ]));

        return response()->json([
            'message' => 'Produto atualizado com sucesso!',
            'data'    => $product,
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'inactive']);

        return response()->json([
            'message' => 'Produto inativado com sucesso!',
        ]);
    }

    public function restore($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'active']);

        return response()->json([
            'message' => 'Produto ativado com sucesso!',
        ]);
    }

    public function sale(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ], [
            'product_id.required' => 'Selecione um produto',
            'product_id.exists'   => 'Produto não encontrado',
            'quantity.required'   => 'Informe a quantidade',
            'quantity.min'        => 'A quantidade deve ser pelo menos 1',
        ]);

        DB::beginTransaction();
        try {
            $product = Product::where('id', $request->product_id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->firstOrFail();

            if (!$product->isAvailable()) {
                throw ValidationException::withMessages([
                    'product_id' => 'Produto indisponível no momento.'
                ]);
            }

            if ($product->stock < $request->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => "Estoque insuficiente. Disponível: {$product->stock} unidades."
                ]);
            }

            $totalPrice = $product->price * $request->quantity;

            /** @var \App\Models\User $user */
            $user      = Auth::user();
            $studentId = null;
            if ($user && $user->isStudent()) {
                $student   = Student::where('user_id', $user->id)->first();
                $studentId = $student?->id;
            }

            $sale = Sale::create([
                'product_id'  => $product->id,
                'student_id'  => $studentId,
                'quantity'    => $request->quantity,
                'total_price' => $totalPrice,
            ]);

            $product->decreaseStock($request->quantity);

            DB::commit();

            return response()->json([
                'message' => 'Venda realizada com sucesso!',
                'data'    => [
                    'sale_id'     => $sale->id,
                    'product'     => $product->name,
                    'quantity'    => $sale->quantity,
                    'total_price' => $sale->total_price,
                ],
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Erro ao processar venda: ' . $e->getMessage()], 500);
        }
    }

    public function report(Request $request)
    {
        $products = Product::withCount('sales')
            ->withSum('sales', 'quantity')
            ->withSum('sales', 'total_price')
            ->get()
            ->map(function ($product) {
                $totalQuantity = (int) ($product->sales_sum_quantity ?? 0);
                $totalRevenue  = (float) ($product->sales_sum_total_price ?? 0);
                $totalProfit   = ($product->price - $product->cost) * $totalQuantity;

                return [
                    'product_id'     => $product->id,
                    'name'           => $product->name,
                    'category'       => $product->category,
                    'price'          => $product->price,
                    'cost'           => $product->cost,
                    'total_quantity' => $totalQuantity,
                    'total_revenue'  => round($totalRevenue, 2),
                    'total_profit'   => round($totalProfit, 2),
                    'status'         => $product->status,
                ];
            })
            ->sortByDesc('total_revenue')
            ->values();

        $summary = [
            'total_revenue' => round($products->sum('total_revenue'), 2),
            'total_profit'  => round($products->sum('total_profit'), 2),
            'total_sales'   => $products->sum('total_quantity'),
        ];

        if ($request->expectsJson()) {
            return response()->json(['data' => $products, 'summary' => $summary]);
        }

        return view('reports.shop-products', compact('products', 'summary'));
    }

    public function studentView()
    {
        return view('shop.index');
    }

    public function managerView()
    {
        $products = Product::orderByDesc('created_at')->get();
        return view('shop.manager', compact('products'));
    }

    /**
     * Lista todos os produtos com informações de estoque (para o gerente)
     */
    public function managerStock()
    {
        // Apenas gerentes podem acessar
        if (!$this->isManager()) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $products = Product::orderBy('name')->get(['id', 'name', 'stock', 'min_stock', 'status']);
        return response()->json(['data' => $products]);
    }

    /**
     * Atualiza o estoque de um produto (gerente)
     */
    public function updateStock(Request $request, $id)
    {
        if (!$this->isManager()) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($id);
        $product->update(['stock' => $request->stock]);

        return response()->json([
            'message' => 'Estoque atualizado com sucesso!',
            'product' => $product->only(['id', 'name', 'stock'])
        ]);
    }

    /**
     * Aumenta o estoque (reposição)
     */
    public function restock(Request $request, $id)
    {
        if (!$this->isManager()) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($id);
        $product->increaseStock($request->quantity);

        return response()->json([
            'message' => "Estoque de {$product->name} aumentado em {$request->quantity} unidades.",
            'stock'   => $product->stock
        ]);
    }

    /**
     * Produtos com estoque baixo (alerta para gerente)
     */
    public function lowStock()
    {
        if (!$this->isManager()) {
            return response()->json(['message' => 'Acesso negado.'], 403);
        }

        $products = Product::whereRaw('stock <= min_stock')
            ->where('status', 'active')
            ->get(['id', 'name', 'stock', 'min_stock']);

        return response()->json(['data' => $products]);
    }
}