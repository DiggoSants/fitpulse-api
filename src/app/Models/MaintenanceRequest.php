<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    protected $fillable = [
        'equipment_id',
        'reported_by',
        'description',
        'status',
        'completed_at',
        'solution_notes',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * Verifica se a solicitação já foi resolvida.
     * O controller usa status = 'resolvido'.
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolvido';
    }

    protected static function booted()
    {
        static::updated(function ($req) {
            if ($req->wasChanged('status') && $req->status === 'resolvido') {
                $equipment = $req->equipment;
                if ($equipment) {
                    $equipment->update([
                        'last_maintenance_date' => now()->toDateString(),
                    ]);
                }
            }
        });
    }
}