<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $fillable = [
        'reference_no',
        'type',
        'source',
        'notes',
        'inventory_date',
        'total_modal',
    ];

    protected $casts = [
        'inventory_date' => 'datetime',
        'total_modal' => 'decimal:2',
    ];

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }
}
