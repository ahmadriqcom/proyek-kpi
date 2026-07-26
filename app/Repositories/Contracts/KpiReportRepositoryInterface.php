<?php

namespace App\Repositories\Contracts;

use App\Models\KpiReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface KpiReportRepositoryInterface
{
    public function getFilteredPaginated(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function getAllFiltered(array $filters): Collection;

    public function findById(int $id): ?KpiReport;

    public function create(array $data): KpiReport;

    public function update(KpiReport $report, array $data): bool;

    public function delete(KpiReport $report): bool;

    public function getDashboardSummary(?int $userId = null): array;

    public function getDashboardAnalytics(?int $userId = null): array;

    public function getEmployeeKpiScore(int $userId): array;

    public function getEmployeesList(): Collection;

    public function generateNextTicketNumber(?string $nik = null, ?string $appCode = null, ?string $regionCode = null): string;
}
