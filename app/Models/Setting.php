<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'store_name',
        'store_address',
        'store_phone',
        'print_type',
        'printer_name',
        'store_logo',
    ];

    protected $casts = [
        'print_type' => 'string',
    ];

    /**
     * Get current setting or return default
     */
    public static function getCurrent(): self
    {
        return static::first() ?? new static();
    }
}
