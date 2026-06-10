<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $fillable = [
        'name',
        'category',
        'description',
        'image',
        'price',
        'cost',
        'status',
        'stock',
        'min_stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost'  => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
    ];

    // ─────────────────────────────────────────────────────────
    // Relacionamentos
    // ─────────────────────────────────────────────────────────
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Verifica se o produto está disponível para venda.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'active' && $this->stock > 0;
    }

    /**
     * Verifica se o estoque está baixo (<= estoque mínimo).
     */
    public function isLowStock(): bool
    {
        return $this->min_stock > 0 && $this->stock <= $this->min_stock;
    }

    /**
     * Diminui o estoque – usado no momento da venda.
     * @throws \Exception
     */
    public function decreaseStock(int $quantity): void
    {
        if ($quantity > $this->stock) {
            throw new \Exception("Estoque insuficiente para o produto {$this->name}. Disponível: {$this->stock}");
        }
        $this->decrement('stock', $quantity);
    }

    /**
     * Aumenta o estoque – usado para reposição.
     */
    public function increaseStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }
}
