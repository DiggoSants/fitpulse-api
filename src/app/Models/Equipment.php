<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}