<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashboxFlow extends Model
{
    use SoftDeletes;

    protected $table = 'cashbox_flows';

    protected $fillable = [
        'type',
        'source_id',
        'amount',
        'date',
        'notes',
        'reference_type',
        'reference_id',
        'is_auto',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'integer',
        'is_auto' => 'boolean',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(CashFlowSource::class, 'source_id');
    }

    /**
     * Get formatted type label (Masuk/Keluar)
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'in' ? 'Masuk' : 'Keluar';
    }

    /**
     * Get formatted amount (Rp X.XXX.XXX)
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get source type badge color
     */
    public function getTypeBadgeColorAttribute(): string
    {
        return $this->type === 'in' ? 'emerald' : 'red';
    }

    /**
     * Get formatted type badge with HTML
     */
    public function getTypeBadgeHtmlAttribute(): string
    {
        $color = $this->type === 'in' ? 'emerald' : 'red';
        $label = $this->type_label;

        if ($this->type === 'in') {
            $icon = '<path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" /><path fill-rule="evenodd" d="M3.293 9.293a1 1 0 011.414 0L9 14.586l4.293-4.293a1 1 0 111.414 1.414l-5 5a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414z" clip-rule="evenodd" />';
        } else {
            $icon = '<path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" /><path fill-rule="evenodd" d="M16.707 10.707a1 1 0 01-1.414 0L11 6.414l-4.293 4.293a1 1 0 11-1.414-1.414l5-5a1 1 0 011.414 0l5 5a1 1 0 010 1.414z" clip-rule="evenodd" />';
        }

        $bgClass = "bg-{$color}-100";
        $textClass = "text-{$color}-800";
        $darkBgClass = "dark:bg-{$color}-900";
        $darkTextClass = "dark:text-{$color}-200";

        return "<span class=\"inline-flex items-center gap-1 rounded-full {$bgClass} px-3 py-1 text-xs font-semibold {$textClass} {$darkBgClass} {$darkTextClass}\"><svg class=\"w-3 h-3\" fill=\"currentColor\" viewBox=\"0 0 20 20\">{$icon}</svg>{$label}</span>";
    }
}
