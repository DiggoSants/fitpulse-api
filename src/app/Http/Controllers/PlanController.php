<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller
{
    // Lista todos os planos (usada internamente pelo dashboard via $plans)
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

    // NOVO: abre a página de criar plano (resources/views/plans/create.blade.php)
    public function create()
    {
        return view('plans.create');
    }

    // Salva o novo plano e redireciona para o dashboard
    public function store(Request $request)
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'price'         => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'benefits'      => ['nullable', 'string'],
            'status'        => ['nullable', 'in:active,inactive'],
        ], [
            'name.required'          => 'O nome do plano é obrigatório.',
            'price.required'         => 'O preço é obrigatório.',
            'price.min'              => 'O preço não pode ser negativo.',
            'duration_days.required' => 'A duração é obrigatória.',
            'duration_days.min'      => 'A duração deve ser de pelo menos 1 dia.',
        ]);

        Plan::create([
            'name'          => $request->name,
            'description'   => $request->description,
            'price'         => $request->price,
            'duration_days' => $request->duration_days,
            'benefits'      => $request->benefits,
            'status'        => $request->status ?? 'active',
        ]);

        return redirect()->route('dashboard')->with('success', 'Plano criado com sucesso!');
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

    // NOVO: abre a página de editar plano (resources/views/plans/edit.blade.php)
    public function edit($id)
    {
        $plan = Plan::findOrFail($id);
        return view('plans.edit', compact('plan'));
    }

    // Salva a edição e redireciona para o dashboard
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'          => ['sometimes', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'price'         => ['sometimes', 'numeric', 'min:0'],
            'duration_days' => ['sometimes', 'integer', 'min:1'],
            'benefits'      => ['nullable', 'string'],
            'status'        => ['nullable', 'in:active,inactive'],
        ], [
            'price.min'          => 'O preço não pode ser negativo.',
            'duration_days.min'  => 'A duração deve ser de pelo menos 1 dia.',
        ]);

        $plan = Plan::findOrFail($id);

        $plan->update($request->only([
            'name',
            'description',
            'price',
            'duration_days',
            'benefits',
            'status',
        ]));

        return redirect()->route('dashboard')->with('success', 'Plano atualizado com sucesso!');
    }

    // Inativação lógica (preserva histórico)
    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);

        $activeStudents = $plan->enrollments()
            ->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString())
            ->count();

        if ($activeStudents > 0) {
            return back()->withErrors([
                'plan' => "Este plano possui {$activeStudents} aluno(s) ativo(s) e não pode ser inativado.",
            ]);
        }

        $plan->update(['status' => 'inactive']);

        return redirect()->route('dashboard')->with('success', 'Plano inativado com sucesso! O histórico foi preservado.');
    }

    // Reativa um plano inativo
    public function restore($id)
    {
        $plan = Plan::findOrFail($id);

        if ($plan->status === 'active') {
            return back()->withErrors(['plan' => 'Este plano já está ativo.']);
        }

        $plan->update(['status' => 'active']);

        return redirect()->route('dashboard')->with('success', 'Plano reativado com sucesso!');
    }
}