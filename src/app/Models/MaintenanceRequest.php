<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Equipment;

class MaintenanceRequest extends Model
{
    protected $fillable = [
        'equipment_id',
        'reported_by',
        'description',
        'status', // pending, in_progress, completed, cancelled
        'completed_at',
        'solution_notes',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // Relacionamento com equipamento
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    // Evento: quando o status mudar para 'completed', atualiza a última manutenção do equipamento
    protected static function booted()
    {
        static::updated(function ($maintenanceRequest) {
            // Verifica se o status foi alterado para 'completed'
            if ($maintenanceRequest->wasChanged('status') && $maintenanceRequest->status === 'completed') {
                $equipment = $maintenanceRequest->equipment;
                if ($equipment) {
                    $equipment->update([
                        'last_maintenance_date' => now()->toDateString()
                    ]);
                }
            }
        });
    }
}