<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashFlowSource extends Model
{
    protected $fillable = [
        'name',
        'type',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function cashFlows(): HasMany
    {
        return $this->hasMany(CashboxFlow::class, 'source_id');
    }

    /**
     * Get type label display (for debugging)
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'in' => 'Masuk',
            'out' => 'Keluar',
            'both' => 'Keduanya',
            default => $this->type
        };
    }
}
