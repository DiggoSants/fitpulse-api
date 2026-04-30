<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Student;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::active()->get()->map(function ($product) {
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

        $product = Product::where('id', $request->product_id)
            ->where('status', 'active')
            ->firstOrFail();
        $totalPrice = $product->price * $request->quantity;

        /** @var \App\Models\User $user */
        $user      = Auth::user();
        $studentId = null;

        if ($user->isStudent()) {
            $student   = Student::where('user_id', $user->id)->first();
            $studentId = $student?->id;
        }

        $sale = Sale::create([
            'product_id'  => $product->id,
            'student_id'  => $studentId,
            'quantity'    => $request->quantity,
            'total_price' => $totalPrice,
        ]);

        return response()->json([
            'message' => 'Venda registrada com sucesso!',
            'data'    => [
                'sale_id'     => $sale->id,
                'product'     => $product->name,
                'quantity'    => $sale->quantity,
                'total_price' => $sale->total_price,
            ],
        ], 201);
    }
    public function report()
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

        return response()->json([
            'data'    => $products,
            'summary' => [
                'total_revenue' => round($products->sum('total_revenue'), 2),
                'total_profit'  => round($products->sum('total_profit'), 2),
                'total_sales'   => $products->sum('total_quantity'),
            ],
        ]);
    }
}