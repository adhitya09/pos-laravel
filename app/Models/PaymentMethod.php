<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'logo',
        'description',
        'is_active',
        'is_cash',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_cash' => 'boolean',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the full path to the logo image
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    /**
     * Determine if this is a cash payment method
     */
    public function isCashPayment(): bool
    {
        return $this->is_cash === true;
    }
}
