<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\MaintenanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    /**
     * Lista todos os equipamentos com código e última manutenção.
     */
    public function index()
    {
        $equipments = Equipment::with(['maintenanceRequests' => function ($q) {
            $q->where('status', 'completed')
              ->orderBy('completed_at', 'desc')
              ->limit(1);
        }])->get();

        $equipments->transform(function ($equipment) {
            $lastMaintenance = $equipment->maintenanceRequests->first();
            $equipment->last_maintenance_display = $lastMaintenance
                ? $lastMaintenance->completed_at->format('d/m/Y')
                : 'Nunca registrada';
            return $equipment;
        });

        if (request()->expectsJson()) {
            return response()->json(['data' => $equipments]);
        }

        return view('equipment.index', compact('equipments'));
    }

    public function create()
    {
        return view('equipment.create');
    }

    /**
     * Armazena um novo equipamento com código único gerado automaticamente.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => ['nullable', Rule::in(['ativo', 'manutencao', 'inativo'])],
        ]);

        $data = $request->only(['name', 'description']);
        $data['status'] = $request->input('status', 'ativo');

        $equipment = Equipment::create($data);

        $message = "Equipamento {$equipment->unique_code} cadastrado com sucesso.";

        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'equipment' => $equipment], 201);
        }

        return redirect()->route('equipment.index')->with('success', $message);
    }

    public function show($id)
    {
        $equipment = Equipment::with(['maintenanceRequests' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        $lastCompleted = $equipment->maintenanceRequests
            ->where('status', 'completed')
            ->sortByDesc('completed_at')
            ->first();

        $equipment->last_maintenance_display = $lastCompleted
            ? $lastCompleted->completed_at->format('d/m/Y')
            : 'Nunca registrada';

        if (request()->expectsJson()) {
            return response()->json($equipment);
        }

        return view('equipment.show', compact('equipment'));
    }

    public function edit($id)
    {
        $equipment = Equipment::findOrFail($id);
        return view('equipment.edit', compact('equipment'));
    }

    /**
     * Atualiza os dados do equipamento.
     * Aceita status: ativo | manutencao | inativo
     */
    public function update(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);

        $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status'      => ['nullable', Rule::in(['ativo', 'manutencao', 'inativo'])],
        ]);

        $equipment->update($request->only(['name', 'description', 'status']));

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Equipamento atualizado.', 'equipment' => $equipment->fresh()]);
        }

        return redirect()->route('equipment.index')->with('success', 'Equipamento atualizado.');
    }

    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);
        $equipment->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Equipamento removido.']);
        }

        return redirect()->route('equipment.index')->with('success', 'Equipamento removido.');
    }

    public function active()
    {
        $equipments = Equipment::where('status', 'ativo')
            ->orderBy('name')
            ->get(['id', 'name', 'unique_code']);

        return response()->json($equipments);
    }
}