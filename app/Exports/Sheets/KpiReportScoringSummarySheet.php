<?php

namespace App\Exports\Sheets;

use App\Models\User;
use App\Repositories\Eloquent\KpiReportRepository;
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

class KpiReportScoringSummarySheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        protected ?array $filters = null
    ) {}

    public function collection(): Collection
    {
        $repository = new KpiReportRepository();
        $userId = $this->filters['user_id'] ?? null;

        if ($userId) {
            $scoreData = $repository->getEmployeeKpiScore((int) $userId);
            return collect($scoreData['matrix'] ?? []);
        }

        // Default collection of default 6 criteria rubrics if no user selected
        $user = User::first();
        if ($user) {
            $scoreData = $repository->getEmployeeKpiScore($user->id);
            return collect($scoreData['matrix'] ?? []);
        }

        return collect([]);
    }

    public function headings(): array
    {
        return [
            'Kriteria Penilaian',
            'Bobot (%)',
            'Skor (Skala 1 - 5)',
            'Nilai (Bobot x Skor)',
            'Catatan Evaluator (Indikator Rubrik Penilaian)',
        ];
    }

    public function map($row): array
    {
        return [
            $row['kriteria'] ?? '',
            ($row['bobot_persen'] ?? 0) . '%',
            $row['skor'] ?? 0,
            number_format($row['nilai'] ?? 0, 2),
            $row['catatan'] ?? '',
        ];
    }

    public function title(): string
    {
        return 'Ringkasan Penilaian KPI';
    }

    public function styles(Worksheet $sheet): array
    {
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1B365D'], // Slate Navy
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        if ($highestRow > 1) {
            $sheet->getStyle("A1:E{$highestRow}")->applyFromArray([
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
