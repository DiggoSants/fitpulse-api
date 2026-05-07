<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\PlanGroup;

class GamificationController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $group         = $user->planGroups()->with('plan')->first();
        $baseDiscount  = $group ? $group->baseDiscount() : 0.0;
        $bonusDiscount = $user->gamificationBonus();
        $totalDiscount = min($baseDiscount + $bonusDiscount, 25.0); // limite máximo 25%

        return response()->json([
            'data' => [
                'points'         => $user->points,
                'points_to_next' => $user->pointsToNextReward(),
                'has_bonus'      => $user->hasGamificationBonus(),
                'bonus_discount' => $bonusDiscount,
                'group'          => $group ? [
                    'id'             => $group->id,
                    'name'           => $group->name,
                    'members'        => $group->memberCount(),
                    'plan'           => $group->plan->name,
                    'base_discount'  => $baseDiscount,
                    'total_discount' => $totalDiscount,
                ] : null,
                'message' => $user->hasGamificationBonus()
                    ? "Você tem {$user->points} pontos → +{$bonusDiscount}% de desconto em plano conjunto!"
                    : "Você tem {$user->points} pontos. Faltam {$user->pointsToNextReward()} para o próximo bônus.",
            ],
        ]);
    }

    public function createGroup(Request $request)
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'plan_id' => ['required', 'exists:plans,id'],
        ], [
            'name.required'    => 'Informe o nome do grupo',
            'plan_id.required' => 'Selecione um plano',
            'plan_id.exists'   => 'Plano inválido',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->planGroups()->exists()) {
            return response()->json([
                'message' => 'Você já participa de um grupo. Saia do grupo atual para criar um novo.',
            ], 422);
        }

        $group = PlanGroup::create([
            'name'     => $request->name,
            'owner_id' => $user->id,
            'plan_id'  => $request->plan_id,
        ]);

        $group->members()->attach($user->id);

        return response()->json([
            'message' => 'Grupo criado com sucesso!',
            'data'    => [
                'id'       => $group->id,
                'name'     => $group->name,
                'plan'     => $group->plan->name,
                'members'  => $group->memberCount(),
                'discount' => $group->baseDiscount(),
            ],
        ], 201);
    }

    public function joinGroup($id)
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $group = PlanGroup::with('plan', 'members')->findOrFail($id);

        if ($group->members->contains($user->id)) {
            return response()->json(['message' => 'Você já está neste grupo.'], 422);
        }

        if ($user->planGroups()->exists()) {
            return response()->json([
                'message' => 'Você já participa de outro grupo. Saia primeiro para entrar em um novo.',
            ], 422);
        }

        if (!$group->hasVacancy()) {
            return response()->json(['message' => 'Este grupo já está cheio (máximo 5 membros).'], 422);
        }

        $group->members()->attach($user->id);

        $baseDiscount  = $group->baseDiscount();
        $bonusDiscount = $user->gamificationBonus();
        $totalDiscount = min($baseDiscount + $bonusDiscount, 25.0);

        return response()->json([
            'message' => 'Você entrou no grupo com sucesso!',
            'data'    => [
                'group'          => $group->name,
                'members'        => $group->memberCount(),
                'base_discount'  => $baseDiscount,
                'bonus_discount' => $bonusDiscount,
                'total_discount' => $totalDiscount,
            ],
        ]);
    }

    public function leaveGroup($id)
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $group = PlanGroup::with('members')->findOrFail($id);

        if (!$group->members->contains($user->id)) {
            return response()->json(['message' => 'Você não faz parte deste grupo.'], 422);
        }

        $group->members()->detach($user->id);

        if ($group->owner_id === $user->id && $group->memberCount() === 0) {
            $group->delete();
            return response()->json(['message' => 'Grupo encerrado pois o responsável saiu.']);
        }

        return response()->json(['message' => 'Você saiu do grupo.']);
    }

    public function listGroups()
    {
        $groups = PlanGroup::with(['plan', 'members'])
            ->get()
            ->map(function ($group) {
                return [
                    'id'            => $group->id,
                    'name'          => $group->name,
                    'plan'          => $group->plan->name,
                    'members'       => $group->memberCount(),
                    'has_vacancy'   => $group->hasVacancy(),
                    'base_discount' => $group->baseDiscount(),
                ];
            });

        return response()->json(['data' => $groups]);
    }
}