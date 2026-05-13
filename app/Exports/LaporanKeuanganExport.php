<?php
namespace App\Exports;

use App\Models\Transaction;
use App\Models\CashboxFlow;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanKeuanganExport implements WithMultipleSheets
{
    protected $month;
    protected $year;

    public function __construct(int $month, int $year)
    {
        $this->month = $month;
        $this->year  = $year;
    }

    public function sheets(): array
    {
        return [
            new TransaksiSheet($this->month, $this->year),
            new CashFlowSheet($this->month, $this->year),
        ];
    }
}

class TransaksiSheet implements FromCollection, WithHeadings, WithTitle,
                                WithStyles, ShouldAutoSize
{
    protected $month, $year;
    public function __construct($m, $y) { $this->month=$m; $this->year=$y; }

    public function collection()
    {
        return Transaction::with('paymentMethod')
            ->whereMonth('created_at', $this->month)
            ->whereYear('created_at', $this->year)
            ->get()
            ->map(fn($t) => [
                'No. Invoice'      => $t->invoice_no,
                'Tanggal'          => $t->created_at->format('d/m/Y H:i'),
                'Pelanggan'        => $t->customer_name ?: 'Umum',
                'Metode Bayar'     => $t->paymentMethod->name ?? '-',
                'Total'            => $t->total_amount,
                'Bayar'            => $t->paid_amount,
                'Kembali'          => $t->change_amount,
                'Status'           => ucfirst($t->status),
            ]);
    }

    public function headings(): array
    {
        return ['No. Invoice','Tanggal','Pelanggan','Metode Bayar',
                'Total (Rp)','Bayar (Rp)','Kembali (Rp)','Status'];
    }

    public function title(): string { return 'Transaksi'; }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID,
                       'startColor' => ['rgb' => '0D9488']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }
}

class CashFlowSheet implements FromCollection, WithHeadings, WithTitle,
                               WithStyles, ShouldAutoSize
{
    protected $month, $year;
    public function __construct($m, $y) { $this->month=$m; $this->year=$y; }

    public function collection()
    {
        return CashboxFlow::with('source')
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->get()
            ->map(fn($f) => [
                'Tanggal'  => \Carbon\Carbon::parse($f->date)->format('d/m/Y'),
                'Tipe'     => $f->type === 'in' ? 'Masuk' : 'Keluar',
                'Sumber'   => $f->source->name ?? '-',
                'Nominal'  => $f->amount,
                'Catatan'  => $f->notes ?: '-',
                'Auto'     => $f->is_auto ? 'Otomatis' : 'Manual',
            ]);
    }

    public function headings(): array
    {
        return ['Tanggal','Tipe','Sumber','Nominal (Rp)','Catatan','Jenis Input'];
    }

    public function title(): string { return 'Cash Flow'; }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID,
                       'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }
}
