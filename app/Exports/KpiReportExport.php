<?php

namespace App\Exports;

use App\Exports\Sheets\KpiReportDataSheet;
use App\Exports\Sheets\KpiReportScoringSummarySheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class KpiReportExport implements WithMultipleSheets
{
    public function __construct(
        protected ?array $filters = null
    ) {}

    public function sheets(): array
    {
        $sheets = [
            new KpiReportDataSheet($this->filters),
            new KpiReportScoringSummarySheet($this->filters),
        ];

        return $sheets;
    }
}
