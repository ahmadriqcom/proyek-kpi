<?php

namespace App\Repositories\Contracts;

use App\Models\KpiAppraisal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface KpiAppraisalRepositoryInterface
{
    public function getPaginatedAppraisals(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?KpiAppraisal;
    public function create(array $appraisalData, array $detailsData): KpiAppraisal;
    public function update(int $id, array $appraisalData, array $detailsData): KpiAppraisal;
    public function updateStatus(int $id, string $status, ?string $notes = null, ?int $evaluatorId = null): bool;
    public function delete(int $id): bool;
    public function generateAppraisalNumber(): string;
}
