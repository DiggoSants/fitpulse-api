<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'unique_code',
    ];

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    /** Disponível para uso — apenas status ativo */
    public function isAvailable(): bool
    {
        return $this->status === 'ativo';
    }

    /** Está em manutenção */
    public function isUnderMaintenance(): bool
    {
        return $this->status === 'manutencao';
    }

    /** Inativo (desativado pelo gerente) */
    public function isInactive(): bool
    {
        return $this->status === 'inativo';
    }

    public function hasOpenRequest(): bool
    {
        return $this->maintenanceRequests()
            ->where('status', 'aberto')
            ->exists();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($equipment) {
            if (empty($equipment->unique_code)) {
                $equipment->unique_code = self::generateUniqueCode();
            }
            // Garante status padrão
            if (empty($equipment->status)) {
                $equipment->status = 'ativo';
            }
        });
    }

    public static function generateUniqueCode(): string
    {
        $date          = now()->format('Ymd');
        $lastEquipment = self::orderBy('id', 'desc')->first();
        $nextId        = $lastEquipment ? $lastEquipment->id + 1 : 1;
        $sequential    = str_pad($nextId, 3, '0', STR_PAD_LEFT);
        return "#EQ-{$date}-{$sequential}";
    }
}