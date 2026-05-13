<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    protected $fillable = [
        'type',
        'amount',
        'source',
        'reference_id',
        'reference_type',
        'description',
        'flow_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'flow_date' => 'datetime',
    ];
}
