<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Equipment extends Model
{
    protected $fillable = [
        'name',
        'status',
    ];

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === 'ativo';
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
        });
    }

    public static function generateUniqueCode(): string
    {
        $date = now()->format('Ymd'); // AAAAMMDD
        $lastEquipment = self::orderBy('id', 'desc')->first();
        $nextId = $lastEquipment ? $lastEquipment->id + 1 : 1;
        $sequential = str_pad($nextId, 3, '0', STR_PAD_LEFT);
        return "#EQ-{$date}-{$sequential}";
    }
}
