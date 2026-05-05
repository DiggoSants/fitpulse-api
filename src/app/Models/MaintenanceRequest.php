<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    protected $fillable = [
        'equipment_id',
        'description',
        'status',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'aberto';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolvido';
    }
}