<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Report extends Model
{
    use HasFactory;

    public const TYPE_INCOME = 'in';
    public const TYPE_EXPENSE = 'out';
    public const TYPE_SALES = 'sales';

    protected $fillable = [
        'code',
        'name',
        'type',
        'from_date',
        'to_date',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_INCOME => 'Uang Masuk',
            self::TYPE_EXPENSE => 'Uang Keluar',
            self::TYPE_SALES => 'Penjualan',
            default => ucfirst($this->type),
        };
    }

    public function getNameOrCodeAttribute(): string
    {
        return $this->name ?: $this->code;
    }

    public static function createReportName(string $type, string $fromDate, string $toDate): string
    {
        $label = match ($type) {
            self::TYPE_INCOME => 'Uang Masuk',
            self::TYPE_EXPENSE => 'Uang Keluar',
            self::TYPE_SALES => 'Penjualan',
            default => ucfirst($type),
        };

        return sprintf(
            'Laporan %s %s - %s',
            $label,
            Carbon::parse($fromDate)->format('d/m/Y'),
            Carbon::parse($toDate)->format('d/m/Y')
        );
    }

    public static function generateReportCode(string $type): string
    {
        return sprintf('RPT-%s-%s', strtoupper(substr($type, 0, 3)), now()->format('YmdHis'));
    }
}
