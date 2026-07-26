<?php

namespace App\Exports\Sheets;

use App\Models\KpiReport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KpiReportDataSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        protected ?array $filters = null
    ) {}

    public function collection(): Collection
    {
        $query = KpiReport::with(['application', 'region', 'creator'])->latest();

        if ($this->filters) {
            if (!empty($this->filters['user_id'])) {
                $query->where('created_by', $this->filters['user_id']);
            }
            if (!empty($this->filters['application_id'])) {
                $query->where('application_id', $this->filters['application_id']);
            }
            if (!empty($this->filters['region_id'])) {
                $query->where('region_id', $this->filters['region_id']);
            }
            if (!empty($this->filters['status'])) {
                $query->where('status', $this->filters['status']);
            }
            if (!empty($this->filters['search'])) {
                $search = $this->filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('ticket_number', 'like', "%{$search}%")
                      ->orWhere('app_region_label', 'like', "%{$search}%")
                      ->orWhere('menu', 'like', "%{$search}%")
                      ->orWhere('problem', 'like', "%{$search}%")
                      ->orWhere('solution', 'like', "%{$search}%");
                });
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No (Nomor Registrasi)',
            'Nama Aplikasi & Daerah',
            'Pegawai Pembuat',
            'Menu',
            'Tgl Mulai',
            'Permasalahan yang Dihadapi',
            'Solusi',
            'Tgl Selesai',
            'Keterangan',
            'Status',
            'Durasi SLA (Hari)',
            'Nilai Score',
        ];
    }

    public function map($report): array
    {
        $statusText = match ($report->status) {
            'completed' => 'SELESAI',
            'cancelled' => 'DIBATALKAN',
            default => 'DALAM PROSES (PENDING)',
        };

        return [
            $report->ticket_number,
            $report->app_region_label,
            $report->creator->name ?? 'System',
            $report->menu,
            $report->start_date ? $report->start_date->format('d/m/Y') : '',
            $report->problem,
            $report->solution ?? '-',
            $report->end_date ? $report->end_date->format('d/m/Y') : '-',
            $report->remarks ?? '-',
            $statusText,
            $report->status === 'completed' ? ($report->sla_duration_days . ' Hari') : ($report->running_sla_days . ' Hari (Running)'),
            $report->score !== null ? number_format($report->score, 1) : '-',
        ];
    }

    public function title(): string
    {
        return 'Data Laporan KPI';
    }

    public function styles(Worksheet $sheet): array
    {
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F2C59'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        if ($highestRow > 1) {
            $sheet->getStyle("A1:L{$highestRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);
        }

        return [];
    }
}
