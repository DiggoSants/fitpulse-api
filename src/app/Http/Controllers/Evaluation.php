<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PhysicalEvaluation;
use App\Models\User;

class EvaluationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id'  => ['sometimes', 'exists:users,id'],
            'weight'   => ['required', 'numeric', 'min:1'],
            'height'   => ['required', 'numeric', 'min:1'],
            'body_fat' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes'    => ['nullable', 'string'],
        ], [
            'weight.required' => 'O peso é obrigatório',
            'height.required' => 'A altura é obrigatória',
            'body_fat.max'    => 'A gordura corporal não pode ultrapassar 100%',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Define para qual usuário a avaliação será registrada
        if ($request->filled('user_id') && ($user->isInstructor() || $user->isManager())) {
            $targetUserId = $request->user_id;
        } else {
            $targetUserId = $user->id;
        }

        $evaluation = PhysicalEvaluation::create([
            'user_id'  => $targetUserId,
            'weight'   => $request->weight,
            'height'   => $request->height,
            'body_fat' => $request->body_fat,
            'notes'    => $request->notes,
        ]);

        return response()->json([
            'message' => 'Avaliação registrada com sucesso!',
            'data'    => [
                'id'                 => $evaluation->id,
                'weight'             => $evaluation->weight,
                'height'             => $evaluation->height,
                'body_fat'           => $evaluation->body_fat,
                'imc'                => $evaluation->imc,
                'imc_classification' => $evaluation->imc_classification,
                'notes'              => $evaluation->notes,
                'date'               => $evaluation->created_at->format('d/m/Y H:i'),
            ],
        ], 201);
    }

    public function history($userId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Aluno só pode ver as próprias avaliações
        if ($user->isStudent() && $user->id != $userId) {
            return response()->json([
                'message' => 'Você não tem permissão para ver as avaliações de outro usuário.',
            ], 403);
        }

        $targetUser = User::findOrFail($userId);

        $evaluations = PhysicalEvaluation::where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($evaluation) {
                return [
                    'id'                 => $evaluation->id,
                    'weight'             => $evaluation->weight,
                    'height'             => $evaluation->height,
                    'body_fat'           => $evaluation->body_fat,
                    'imc'                => $evaluation->imc,
                    'imc_classification' => $evaluation->imc_classification,
                    'notes'              => $evaluation->notes,
                    'date'               => $evaluation->created_at->format('d/m/Y'),
                ];
            });

        return response()->json([
            'user'  => $targetUser->name,
            'data'  => $evaluations,
            'total' => $evaluations->count(),
        ]);
    }

    public function evolution($userId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Aluno só pode ver a própria evolução
        if ($user->isStudent() && $user->id != $userId) {
            return response()->json([
                'message' => 'Você não tem permissão para ver a evolução de outro usuário.',
            ], 403);
        }

        $targetUser  = User::findOrFail($userId);
        $evaluations = PhysicalEvaluation::where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($evaluations->isEmpty()) {
            return response()->json([
                'user'    => $targetUser->name,
                'message' => 'Nenhuma avaliação encontrada.',
                'data'    => [],
            ]);
        }

        $first = $evaluations->first();
        $last  = $evaluations->last();

        // Monta histórico com variação em relação à avaliação anterior
        $history = $evaluations->map(function ($evaluation, $index) use ($evaluations) {
            $prev = $index > 0 ? $evaluations[$index - 1] : null;

            return [
                'id'                 => $evaluation->id,
                'date'               => $evaluation->created_at->format('d/m/Y'),
                'weight'             => $evaluation->weight,
                'height'             => $evaluation->height,
                'body_fat'           => $evaluation->body_fat,
                'imc'                => $evaluation->imc,
                'imc_classification' => $evaluation->imc_classification,
                'notes'              => $evaluation->notes,
                'variation'          => $prev ? [
                    'weight'   => round($evaluation->weight - $prev->weight, 2),
                    'body_fat' => $evaluation->body_fat && $prev->body_fat
                        ? round($evaluation->body_fat - $prev->body_fat, 2)
                        : null,
                    'imc'      => round($evaluation->imc - $prev->imc, 2),
                ] : null,
            ];
        });

        return response()->json([
            'user' => $targetUser->name,
            'data' => $history,
            'summary' => [
                'total_evaluations' => $evaluations->count(),
                'first_evaluation'  => $first->created_at->format('d/m/Y'),
                'last_evaluation'   => $last->created_at->format('d/m/Y'),
                'weight_change'     => round($last->weight - $first->weight, 2),
                'imc_change'        => round($last->imc - $first->imc, 2),
                'body_fat_change'   => $first->body_fat && $last->body_fat
                    ? round($last->body_fat - $first->body_fat, 2)
                    : null,
            ],
        ]);
    }
}