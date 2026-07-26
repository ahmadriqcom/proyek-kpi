<?php

namespace App\Repositories\Eloquent;

use App\Models\KpiAppraisal;
use App\Models\KpiAppraisalDetail;
use App\Repositories\Contracts\KpiAppraisalRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class KpiAppraisalRepository implements KpiAppraisalRepositoryInterface
{
    public function getPaginatedAppraisals(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = KpiAppraisal::with(['user', 'grade', 'evaluator', 'details.criteria'])->latest();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('appraisal_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['approval_status'])) {
            $query->where('approval_status', $filters['approval_status']);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function findById(int $id): ?KpiAppraisal
    {
        return KpiAppraisal::with(['user', 'grade', 'evaluator', 'details.criteria'])->find($id);
    }

    public function create(array $appraisalData, array $detailsData): KpiAppraisal
    {
        return DB::transaction(function () use ($appraisalData, $detailsData) {
            $appraisal = KpiAppraisal::create($appraisalData);

            foreach ($detailsData as $detail) {
                $detail['kpi_appraisal_id'] = $appraisal->id;
                KpiAppraisalDetail::create($detail);
            }

            return $appraisal->load(['user', 'grade', 'evaluator', 'details.criteria']);
        });
    }

    public function update(int $id, array $appraisalData, array $detailsData): KpiAppraisal
    {
        return DB::transaction(function () use ($id, $appraisalData, $detailsData) {
            $appraisal = KpiAppraisal::findOrFail($id);
            $appraisal->update($appraisalData);

            // Re-create details for clean update
            $appraisal->details()->delete();
            foreach ($detailsData as $detail) {
                $detail['kpi_appraisal_id'] = $appraisal->id;
                KpiAppraisalDetail::create($detail);
            }

            return $appraisal->load(['user', 'grade', 'evaluator', 'details.criteria']);
        });
    }

    public function updateStatus(int $id, string $status, ?string $notes = null, ?int $evaluatorId = null): bool
    {
        $appraisal = KpiAppraisal::findOrFail($id);
        $updateData = ['approval_status' => $status];
        if ($notes !== null) {
            $updateData['approval_notes'] = $notes;
        }
        if ($evaluatorId !== null) {
            $updateData['evaluator_id'] = $evaluatorId;
        }

        return $appraisal->update($updateData);
    }

    public function delete(int $id): bool
    {
        $appraisal = KpiAppraisal::find($id);
        return $appraisal ? $appraisal->delete() : false;
    }

    public function generateAppraisalNumber(): string
    {
        $prefix = 'APR-' . date('Ym') . '-';
        $latest = KpiAppraisal::where('appraisal_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNum = $latest ? ((int) substr($latest->appraisal_number, strlen($prefix)) + 1) : 1;

        return $prefix . sprintf('%04d', $nextNum);
    }
}
