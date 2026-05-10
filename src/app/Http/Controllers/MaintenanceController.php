<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Equipment;
use App\Models\MaintenanceRequest;

class MaintenanceController extends Controller
{
    /**
     * Renderiza a VIEW da tela de manutenção (só para gerente).*/
    public function view()
    {
        return view('maintenance.index');
    }


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
        $hasOpen = DB::transaction(function () use ($equipment, $request) {
            $existing = MaintenanceRequest::where('equipment_id', $equipment->id)
                ->where('status', 'aberto')
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return true;
            }

            MaintenanceRequest::create([
                'equipment_id' => $equipment->id,
                'description'  => $request->description,
                'status'       => 'aberto',
            ]);

            $equipment->update(['status' => 'manutencao']);

            return false;
        });

        if ($hasOpen) {
            return response()->json([
                'message' => 'Este equipamento já possui uma solicitação aberta.',
            ], 422);
        }

        $maintenanceRequest = MaintenanceRequest::where('equipment_id', $equipment->id)
            ->where('status', 'aberto')
            ->latest()
            ->first();

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

        $maintenanceRequest->update(['status' => 'resolvido']);

        $stillHasOpenRequests = $maintenanceRequest->equipment
            ->maintenanceRequests()
            ->where('status', 'aberto')
            ->exists();

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