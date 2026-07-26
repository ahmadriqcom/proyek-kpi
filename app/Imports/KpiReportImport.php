<?php

namespace App\Imports;

use App\Models\Application;
use App\Models\KpiReport;
use App\Models\KpiReportHistory;
use App\Models\Region;
use App\Services\KpiInterpretationService;
use App\Services\KpiReportService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class KpiReportImport implements ToCollection, WithHeadingRow
{
    public int $importedCount = 0;
    public array $errors = [];
    public array $detailedErrors = [];

    public function __construct(
        protected int $userId
    ) {}

    public function collection(Collection $rows): void
    {
        $interpreter = new KpiInterpretationService();
        $reportService = app(KpiReportService::class);

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            $appRegionRaw = trim($row['nama_aplikasi_daerah'] ?? $row['nama_aplikasi_dan_daerah'] ?? $row[1] ?? '');
            $menu = trim($row['menu'] ?? $row[2] ?? '');
            $startDateRaw = $row['tgl_mulai'] ?? $row['tanggal_mulai'] ?? $row[3] ?? null;
            $problem = trim($row['permasalahan_yang_dihadapi'] ?? $row['permasalahan'] ?? $row[4] ?? '');
            $solution = trim($row['solusi'] ?? $row[5] ?? '');
            $endDateRaw = $row['tgl_selesai'] ?? $row['tanggal_selesai'] ?? $row[6] ?? null;
            $remarks = trim($row['keterangan'] ?? $row[7] ?? '');

            if (empty($appRegionRaw) && empty($menu) && empty($problem)) {
                continue;
            }

            if (empty($appRegionRaw) || empty($problem) || empty($startDateRaw)) {
                $reason = 'Data tidak lengkap (Nama Aplikasi & Daerah, Permasalahan, dan Tgl Mulai wajib diisi).';
                $this->errors[] = "Baris {$rowNumber}: {$reason}";
                $this->detailedErrors[] = [
                    'row' => $rowNumber,
                    'data_name' => $appRegionRaw ?: 'Baris Kosong',
                    'reason' => $reason,
                ];
                continue;
            }

            $parts = explode('-', $appRegionRaw, 2);
            $appCode = strtoupper(trim($parts[0]));
            $regionName = count($parts) > 1 ? trim($parts[1]) : 'Pusat';

            $app = Application::firstOrCreate(
                ['code' => $appCode],
                ['name' => 'Aplikasi ' . $appCode, 'description' => 'Diimpor dari spreadsheet']
            );

            $region = Region::firstOrCreate(
                ['code' => strtoupper(substr($regionName, 0, 3))],
                ['name' => $regionName]
            );

            $startDate = $this->parseDate($startDateRaw);
            if (!$startDate) {
                $reason = "Format Tanggal Mulai [{$startDateRaw}] tidak valid.";
                $this->errors[] = "Baris {$rowNumber}: {$reason}";
                $this->detailedErrors[] = [
                    'row' => $rowNumber,
                    'data_name' => $appRegionRaw,
                    'reason' => $reason,
                ];
                continue;
            }

            $endDate = !empty($endDateRaw) ? $this->parseDate($endDateRaw) : null;

            $prefix = 'KPI-' . date('Ym') . '-';
            $latest = KpiReport::where('ticket_number', 'like', "{$prefix}%")->orderBy('id', 'desc')->first();
            $nextNum = $latest ? ((int) substr($latest->ticket_number, strlen($prefix)) + 1) : 1;
            $ticketNumber = $prefix . sprintf('%04d', $nextNum);

            // Auto-Interpretation Batch Engine
            $interpreted = $interpreter->interpret($problem, $menu);

            $reportData = array_merge([
                'ticket_number' => $ticketNumber,
                'application_id' => $app->id,
                'region_id' => $region->id,
                'app_region_label' => $app->code . ' - ' . $region->name,
                'menu' => !empty($menu) ? $menu : 'Umum',
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
                'problem' => $problem,
                'solution' => !empty($solution) ? $solution : null,
                'remarks' => !empty($remarks) ? $remarks : null,
                'created_by' => $this->userId,
                'status' => $endDate ? 'completed' : 'pending',
            ], $interpreted);

            // Calculate Score
            $reportService->calculateCompositeKpiScore($reportData);

            $report = KpiReport::create($reportData);

            KpiReportHistory::create([
                'kpi_report_id' => $report->id,
                'user_id' => $this->userId,
                'previous_status' => 'new',
                'new_status' => $report->status,
                'solution_log' => 'Laporan diimpor dari spreadsheet Excel & diinterpretasi otomatis oleh Auto-Interpretation Engine [AUTO_INTERPRETED].',
            ]);

            $this->importedCount++;
        }
    }

    protected function parseDate(mixed $val): ?Carbon
    {
        if (empty($val)) {
            return null;
        }

        if (is_numeric($val)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject($val));
        }

        try {
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $val)) {
                return Carbon::createFromFormat('d/m/Y', $val);
            }
            return Carbon::parse($val);
        } catch (\Exception $e) {
            return null;
        }
    }
}
