<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount([
            'enrollments as active_students_count' => function ($query) {
                $query->where('status', 'active')
                    ->where('end_date', '>=', now()->toDateString());
            }
        ])->get();

        return response()->json(['data' => $plans]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'price'         => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'benefits'      => ['nullable', 'string'],
        ], [
            'name.required'          => 'O nome do plano é obrigatório',
            'price.required'         => 'O preço é obrigatório',
            'price.min'              => 'O preço não pode ser negativo',
            'duration_days.required' => 'A duração é obrigatória',
            'duration_days.min'      => 'A duração deve ser de pelo menos 1 dia',
        ]);

        $plan = Plan::create([
            'name'          => $request->name,
            'description'   => $request->description,
            'price'         => $request->price,
            'duration_days' => $request->duration_days,
            'benefits'      => $request->benefits,
            'status'        => 'active',
        ]);

        return response()->json([
            'message' => 'Plano criado com sucesso!',
            'data'    => $plan,
        ], 201);
    }

    public function show($id)
    {
        $plan = Plan::withCount([
            'enrollments as active_students_count' => function ($query) {
                $query->where('status', 'active')
                    ->where('end_date', '>=', now()->toDateString());
            }
        ])->findOrFail($id);

        return response()->json(['data' => $plan]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'          => ['sometimes', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'price'         => ['sometimes', 'numeric', 'min:0'],
            'duration_days' => ['sometimes', 'integer', 'min:1'],
            'benefits'      => ['nullable', 'string'],
        ], [
            'price.min'          => 'O preço não pode ser negativo',
            'duration_days.min'  => 'A duração deve ser de pelo menos 1 dia',
        ]);

        $plan = Plan::findOrFail($id);

        $plan->update($request->only([
            'name',
            'description',
            'price',
            'duration_days',
            'benefits',
        ]));

        return response()->json([
            'message' => 'Plano atualizado com sucesso!',
            'data'    => $plan,
        ]);
    }

    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);

        $activeStudents = $plan->enrollments()
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->count();

        if ($activeStudents > 0) {
            return response()->json([
                'message' => "Este plano possui {$activeStudents} aluno(s) ativo(s) e não pode ser inativado.",
            ], 422);
        }

        $plan->update(['status' => 'inactive']);

        return response()->json([
            'message' => 'Plano inativado com sucesso! O histórico foi preservado.',
        ]);
    }

    public function restore($id)
    {
        $plan = Plan::findOrFail($id);

        if ($plan->status === 'active') {
            return response()->json(['message' => 'Este plano já está ativo.'], 422);
        }

        $plan->update(['status' => 'active']);

        return response()->json([
            'message' => 'Plano reativado com sucesso!',
            'data'    => $plan,
        ]);
    }
}
