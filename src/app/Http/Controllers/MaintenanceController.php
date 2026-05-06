<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipment;
use App\Models\MaintenanceRequest;

class MaintenanceController extends Controller
{
    public function index()
    {
        $requests = MaintenanceRequest::with('equipment')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($request) {
                return [
                    'id'          => $request->id,
                    'equipment'   => $request->equipment->name,
                    'description' => $request->description,
                    'status'      => $request->status,
                    'created_at'  => $request->created_at->format('d/m/Y H:i'),
                ];
            });

        // Equipamentos em manutenção para o modal do dashboard
        $inMaintenance = Equipment::where('status', 'manutencao')->get()
            ->map(fn($e) => ['id' => $e->id, 'name' => $e->name]);

        return response()->json([
            'data'           => $requests,
            'in_maintenance' => $inMaintenance,
            'summary'        => [
                'total_open'           => $requests->where('status', 'aberto')->count(),
                'total_resolved'       => $requests->where('status', 'resolvido')->count(),
                'total_in_maintenance' => $inMaintenance->count(),
            ],
        ]);
    }

    public function equipment()
    {
        $equipment = Equipment::all()->map(function ($item) {
            return [
                'id'     => $item->id,
                'name'   => $item->name,
                'status' => $item->status,
            ];
        });

        return response()->json(['data' => $equipment]);
    }

    public function storeEquipment(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'O nome do equipamento é obrigatório',
        ]);

        $equipment = Equipment::create([
            'name'   => $request->name,
            'status' => 'ativo',
        ]);

        return response()->json([
            'message' => 'Equipamento cadastrado com sucesso!',
            'data'    => $equipment,
        ], 201);
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipment_id' => ['required', 'exists:equipment,id'],
            'description'  => ['required', 'string'],
        ], [
            'equipment_id.required' => 'Selecione o equipamento',
            'equipment_id.exists'   => 'Equipamento não encontrado',
            'description.required'  => 'Descreva o problema',
        ]);

        $equipment = Equipment::findOrFail($request->equipment_id);

        // Não permite múltiplas solicitações abertas para o mesmo equipamento
        if ($equipment->hasOpenRequest()) {
            return response()->json([
                'message' => 'Este equipamento já possui uma solicitação aberta.',
            ], 422);
        }

        // Cria a solicitação
        $maintenanceRequest = MaintenanceRequest::create([
            'equipment_id' => $equipment->id,
            'description'  => $request->description,
            'status'       => 'aberto',
        ]);

        // Atualiza o status do equipamento automaticamente
        $equipment->update(['status' => 'manutencao']);

        return response()->json([
            'message' => 'Solicitação registrada! Equipamento marcado como em manutenção.',
            'data'    => [
                'id'          => $maintenanceRequest->id,
                'equipment'   => $equipment->name,
                'description' => $maintenanceRequest->description,
                'status'      => $maintenanceRequest->status,
                'created_at'  => $maintenanceRequest->created_at->format('d/m/Y H:i'),
            ],
        ], 201);
    }

    public function resolve($id)
    {
        $maintenanceRequest = MaintenanceRequest::with('equipment')->findOrFail($id);

        if ($maintenanceRequest->isResolved()) {
            return response()->json([
                'message' => 'Esta solicitação já foi resolvida.',
            ], 422);
        }

        // Marca como resolvida
        $maintenanceRequest->update(['status' => 'resolvido']);

        // Verifica se ainda tem outras solicitações abertas para o mesmo equipamento
        $stillHasOpenRequests = $maintenanceRequest->equipment
            ->maintenanceRequests()
            ->where('status', 'aberto')
            ->exists();

        // Se não tiver mais solicitações abertas, volta o equipamento para ativo
        if (!$stillHasOpenRequests) {
            $maintenanceRequest->equipment->update(['status' => 'ativo']);
        }

        return response()->json([
            'message' => 'Solicitação resolvida! Equipamento voltou para ativo.',
            'data'    => [
                'id'        => $maintenanceRequest->id,
                'equipment' => $maintenanceRequest->equipment->name,
                'status'    => $maintenanceRequest->status,
            ],
        ]);
    }
}