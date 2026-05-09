<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Plan;
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
        $totalDiscount = min($baseDiscount + $bonusDiscount, 25.0);

        // Detecta se é requisição JSON (API) ou carregamento de página
        if (request()->expectsJson()) {
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

        // Carregamento de página — retorna a view com dados necessários
        $plans = Plan::where('status', 'active')->get();

        if ($group) {
            $group->load('members', 'plan');
        }

        return view('gamification', compact('user', 'group', 'plans'));
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
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Você já participa de um grupo. Saia do grupo atual para criar um novo.',
                ], 422);
            }
            return redirect()->route('gamification.index')
                ->with('error', 'Você já participa de um grupo. Saia primeiro para criar um novo.');
        }

        $group = PlanGroup::create([
            'name'     => $request->name,
            'owner_id' => $user->id,
            'plan_id'  => $request->plan_id,
        ]);

        $group->members()->attach($user->id);

        if (request()->expectsJson()) {
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

        return redirect()->route('gamification.index')
            ->with('success', 'Grupo "' . $group->name . '" criado com sucesso!');
    }

    public function joinGroup($id)
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $group = PlanGroup::with('plan', 'members')->findOrFail($id);

        if ($group->members->contains($user->id)) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Você já está neste grupo.'], 422);
            }
            return redirect()->route('gamification.index')->with('error', 'Você já está neste grupo.');
        }

        if ($user->planGroups()->exists()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Você já participa de outro grupo. Saia primeiro para entrar em um novo.',
                ], 422);
            }
            return redirect()->route('gamification.index')
                ->with('error', 'Você já participa de outro grupo. Saia primeiro para entrar em um novo.');
        }

        if (!$group->hasVacancy()) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Este grupo já está cheio (máximo 5 membros).'], 422);
            }
            return redirect()->route('gamification.index')
                ->with('error', 'Este grupo já está cheio (máximo 5 membros).');
        }

        $group->members()->attach($user->id);

        $baseDiscount  = $group->baseDiscount();
        $bonusDiscount = $user->gamificationBonus();
        $totalDiscount = min($baseDiscount + $bonusDiscount, 25.0);

        if (request()->expectsJson()) {
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

        return redirect()->route('gamification.index')
            ->with('success', 'Você entrou no grupo "' . $group->name . '" com sucesso!');
    }

    public function leaveGroup($id)
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $group = PlanGroup::with('members')->findOrFail($id);

        if (!$group->members->contains($user->id)) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'Você não faz parte deste grupo.'], 422);
            }
            return redirect()->route('gamification.index')
                ->with('error', 'Você não faz parte deste grupo.');
        }

        $group->members()->detach($user->id);

        if ($group->owner_id === $user->id && $group->memberCount() === 0) {
            $group->delete();

            if (request()->expectsJson()) {
                return response()->json(['message' => 'Grupo encerrado pois o responsável saiu.']);
            }
            return redirect()->route('gamification.index')
                ->with('success', 'Você saiu e o grupo foi encerrado por estar vazio.');
        }

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Você saiu do grupo.']);
        }

        return redirect()->route('gamification.index')
            ->with('success', 'Você saiu do grupo com sucesso.');
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

    public function showGroup($id)
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $group = PlanGroup::with(['plan', 'members'])->findOrFail($id);

        $baseDiscount  = $group->baseDiscount();
        $bonusDiscount = $user->gamificationBonus();
        $totalDiscount = min($baseDiscount + $bonusDiscount, 25.0);

        return response()->json([
            'data' => [
                'id'             => $group->id,
                'name'           => $group->name,
                'plan'           => $group->plan->name,
                'owner_id'       => $group->owner_id,
                'members'        => $group->memberCount(),
                'has_vacancy'    => $group->hasVacancy(),
                'base_discount'  => $baseDiscount,
                'bonus_discount' => $bonusDiscount,
                'total_discount' => $totalDiscount,
                'members_list'   => $group->members->map(fn($m) => [
                    'id'        => $m->id,
                    'name'      => $m->name,
                    'points'    => $m->points,
                    'has_bonus' => $m->hasGamificationBonus(),
                    'is_owner'  => $m->id === $group->owner_id,
                ]),
            ],
        ]);
    }
}