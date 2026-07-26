<?php

namespace App\Services;

use App\Models\Application;
use App\Models\KpiCategory;
use App\Models\KpiFormulaConfig;
use App\Models\KpiImpactLevel;
use App\Models\KpiPriority;
use App\Models\KpiReport;
use App\Models\KpiReportHistory;
use App\Models\Region;
use App\Models\User;
use App\Repositories\Contracts\KpiReportRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class KpiReportService
{
    public function __construct(
        protected KpiReportRepositoryInterface $kpiReportRepository,
        protected KpiInterpretationService $kpiInterpretationService
    ) {}

    public function getPaginatedReports(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->kpiReportRepository->getFilteredPaginated($filters, $perPage);
    }

    public function getAllReports(array $filters): Collection
    {
        return $this->kpiReportRepository->getAllFiltered($filters);
    }

    public function getReportById(int $id): ?KpiReport
    {
        return $this->kpiReportRepository->findById($id);
    }

    public function calculateCompositeKpiScore(array &$data): float
    {
        $config = KpiFormulaConfig::getActiveConfig();

        if (!empty($data['kpi_category_id'])) {
            $catModel = KpiCategory::find($data['kpi_category_id']);
            if ($catModel) {
                $data['category'] = $catModel->name;
            }
        } elseif (!empty($data['category'])) {
            $catModel = KpiCategory::where('name', $data['category'])->first();
            if ($catModel) {
                $data['kpi_category_id'] = $catModel->id;
            }
        } else {
            $catModel = null;
        }

        if (!empty($data['kpi_priority_id'])) {
            $prioModel = KpiPriority::find($data['kpi_priority_id']);
            if ($prioModel) {
                $data['priority'] = $prioModel->name;
            }
        } elseif (!empty($data['priority'])) {
            $prioModel = KpiPriority::where('name', $data['priority'])->first();
            if ($prioModel) {
                $data['kpi_priority_id'] = $prioModel->id;
            }
        } else {
            $prioModel = null;
        }

        if (!empty($data['kpi_impact_level_id'])) {
            $impModel = KpiImpactLevel::find($data['kpi_impact_level_id']);
            if ($impModel) {
                $data['impact_level'] = $impModel->name;
            }
        } elseif (!empty($data['impact_level'])) {
            $impModel = KpiImpactLevel::where('name', $data['impact_level'])->first();
            if ($impModel) {
                $data['kpi_impact_level_id'] = $impModel->id;
            }
        } else {
            $impModel = null;
        }

        $weights = [];
        if ($config->use_category_weight && $catModel) {
            $weights[] = (float) $catModel->complexity_weight;
        }
        if ($config->use_priority_weight && $prioModel) {
            $weights[] = (float) $prioModel->urgency_weight;
        }
        if ($config->use_impact_weight && $impModel) {
            $weights[] = (float) $impModel->impact_weight;
        }

        $compositeWeight = count($weights) > 0 ? (array_sum($weights) / count($weights)) : 1.0;
        $adjustedBase = 100.00 * $compositeWeight;

        if (empty($data['end_date'])) {
            $data['sla_duration_days'] = null;
            $data['score'] = 0.00;
            return 0.00;
        }

        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $slaDays = $startDate->diffInDays($endDate);
        $data['sla_duration_days'] = $slaDays;

        if (empty($data['status']) || $data['status'] === 'pending') {
            $data['status'] = 'completed';
        }

        $targetSla = $prioModel->target_sla_days ?? 2;
        $bonus = 0;
        $penalty = 0;

        if ($slaDays <= $targetSla) {
            if ($config->use_sla_bonus) {
                $bonus = (float) $config->sla_bonus_early;
            }
        } else {
            if ($config->use_sla_penalty) {
                $overdueDays = $slaDays - $targetSla;
                $urgencyWeight = $prioModel ? (float) $prioModel->urgency_weight : 1.0;
                $penalty = $overdueDays * (float) $config->sla_penalty_per_day * $urgencyWeight;
            }
        }

        $rawScore = $adjustedBase - $penalty + $bonus;

        if ($config->cap_max_score) {
            $finalScore = min((float) $config->max_score_cap, max(0, $rawScore));
        } else {
            $finalScore = max(0, $rawScore);
        }

        $data['score'] = round($finalScore, 2);
        return $data['score'];
    }

    public function createReport(array $data, int $userId): KpiReport
    {
        return DB::transaction(function () use ($data, $userId) {
            $app = Application::findOrFail($data['application_id']);
            $region = Region::findOrFail($data['region_id']);

            if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $data['attachment_path'] = $data['attachment']->store('attachments', 'public');
                unset($data['attachment']);
            }

            $user = User::find($userId);
            $nik = $user->nik ?? '202607';

            $data['ticket_number'] = $this->kpiReportRepository->generateNextTicketNumber($nik, $app->code, $region->code);
            $data['app_region_label'] = $app->code . ' - ' . $region->name;
            $data['created_by'] = $userId;
            $data['status'] = $data['status'] ?? 'pending';

            // Auto-Interpretation Engine Trigger for Operator or if parameters missing
            if (empty($data['kpi_category_id']) || empty($data['kpi_priority_id']) || empty($data['kpi_impact_level_id'])) {
                $interpreted = $this->kpiInterpretationService->interpret($data['problem'] ?? '', $data['menu'] ?? '');
                $data = array_merge($interpreted, $data);
            } else {
                $data['is_auto_interpreted'] = $data['is_auto_interpreted'] ?? false;
                $data['data_origin'] = $data['data_origin'] ?? ($data['is_auto_interpreted'] ? 'AUTO_INTERPRETED' : 'MANUAL_OVERRIDE');
            }

            $this->calculateCompositeKpiScore($data);

            $report = $this->kpiReportRepository->create($data);

            $originTag = $report->data_origin === 'MANUAL_OVERRIDE' ? '[MANUAL_OVERRIDE]' : '[AUTO_INTERPRETED]';

            KpiReportHistory::create([
                'kpi_report_id' => $report->id,
                'user_id' => $userId,
                'previous_status' => 'new',
                'new_status' => $report->status,
                'solution_log' => 'Laporan kendala baru dicatat oleh ' . ($user->name ?? 'Operator') . " {$originTag} [Status: " . strtoupper($report->status) . ']',
            ]);

            return $report;
        });
    }

    public function updateReport(int $id, array $data, int $userId): KpiReport
    {
        return DB::transaction(function () use ($id, $data, $userId) {
            $report = $this->kpiReportRepository->findById($id);
            if (!$report) {
                throw new \InvalidArgumentException("Laporan KPI dengan ID {$id} tidak ditemukan.");
            }

            if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $data['attachment_path'] = $data['attachment']->store('attachments', 'public');
                unset($data['attachment']);
            }

            $previousStatus = $report->status;
            $previousSolution = $report->solution;

            if (!empty($data['application_id']) && !empty($data['region_id'])) {
                $app = Application::findOrFail($data['application_id']);
                $region = Region::findOrFail($data['region_id']);
                $data['app_region_label'] = $app->code . ' - ' . $region->name;
            }

            $user = User::find($userId);
            $data['updated_by'] = $userId;
            $data['start_date'] = !empty($data['start_date']) ? $data['start_date'] : $report->start_date->format('Y-m-d');
            $data['end_date'] = array_key_exists('end_date', $data) ? $data['end_date'] : ($report->end_date ? $report->end_date->format('Y-m-d') : null);

            // Manual Override Flag Check
            if (
                (!empty($data['kpi_category_id']) && $data['kpi_category_id'] != $report->kpi_category_id) ||
                (!empty($data['kpi_priority_id']) && $data['kpi_priority_id'] != $report->kpi_priority_id) ||
                (!empty($data['kpi_impact_level_id']) && $data['kpi_impact_level_id'] != $report->kpi_impact_level_id)
            ) {
                $data['is_auto_interpreted'] = false;
                $data['data_origin'] = 'MANUAL_OVERRIDE';
            }

            $this->calculateCompositeKpiScore($data);

            $this->kpiReportRepository->update($report, $data);
            $report->refresh();

            $newStatus = $data['status'] ?? $previousStatus;
            $newSolution = $data['solution'] ?? $previousSolution;
            $solutionLog = $data['solution_log'] ?? null;

            if ($previousStatus !== $newStatus || $previousSolution !== $newSolution || $solutionLog || $report->data_origin === 'MANUAL_OVERRIDE') {
                $originInfo = $report->data_origin === 'MANUAL_OVERRIDE' ? '[MANUAL_OVERRIDE]' : '[AUTO_INTERPRETED]';
                KpiReportHistory::create([
                    'kpi_report_id' => $report->id,
                    'user_id' => $userId,
                    'previous_status' => $previousStatus,
                    'new_status' => $newStatus,
                    'solution_log' => $solutionLog ?? "Pembaruan data/solusi oleh {$user->name} {$originInfo}. Solusi: {$newSolution}",
                ]);
            }

            return $report;
        });
    }

    public function approveClassification(int $id, array $data, int $userId): KpiReport
    {
        return DB::transaction(function () use ($id, $data, $userId) {
            $report = $this->kpiReportRepository->findById($id);
            if (!$report) {
                throw new \InvalidArgumentException("Laporan KPI dengan ID {$id} tidak ditemukan.");
            }

            $prevScore = $report->score;
            $prevStatus = $report->status;
            $user = User::find($userId);

            if (!empty($data['kpi_category_id'])) {
                $cat = KpiCategory::find($data['kpi_category_id']);
                if ($cat) {
                    $report->kpi_category_id = $cat->id;
                    $report->category = $cat->name;
                }
            }
            if (!empty($data['kpi_priority_id'])) {
                $prio = KpiPriority::find($data['kpi_priority_id']);
                if ($prio) {
                    $report->kpi_priority_id = $prio->id;
                    $report->priority = $prio->name;
                }
            }
            if (!empty($data['kpi_impact_level_id'])) {
                $imp = KpiImpactLevel::find($data['kpi_impact_level_id']);
                if ($imp) {
                    $report->kpi_impact_level_id = $imp->id;
                    $report->impact_level = $imp->name;
                }
            }

            $report->is_auto_interpreted = false;
            $report->data_origin = 'MANUAL_OVERRIDE';
            $report->approval_reason = $data['approval_reason'] ?? 'Disetujui & Disesuaikan oleh Management';
            $report->status = !empty($report->end_date) ? 'completed' : 'pending';

            // Recalculate score without requiring a separate approval step
            $config = KpiFormulaConfig::getActiveConfig();
            $catModel = $report->kpiCategory;
            $prioModel = $report->kpiPriority;
            $impModel = $report->kpiImpactLevel;

            $weights = [];
            if ($config->use_category_weight && $catModel) {
                $weights[] = (float) $catModel->complexity_weight;
            }
            if ($config->use_priority_weight && $prioModel) {
                $weights[] = (float) $prioModel->urgency_weight;
            }
            if ($config->use_impact_weight && $impModel) {
                $weights[] = (float) $impModel->impact_weight;
            }

            $compositeWeight = count($weights) > 0 ? (array_sum($weights) / count($weights)) : 1.0;
            $adjustedBase = 100.00 * $compositeWeight;

            if ($report->end_date) {
                $slaDays = $report->start_date->diffInDays($report->end_date);
                $targetSla = $prioModel->target_sla_days ?? 2;
                $bonus = 0;
                $penalty = 0;

                if ($slaDays <= $targetSla) {
                    if ($config->use_sla_bonus) {
                        $bonus = (float) $config->sla_bonus_early;
                    }
                } else {
                    if ($config->use_sla_penalty) {
                        $overdueDays = $slaDays - $targetSla;
                        $urgencyWeight = $prioModel ? (float) $prioModel->urgency_weight : 1.0;
                        $penalty = $overdueDays * (float) $config->sla_penalty_per_day * $urgencyWeight;
                    }
                }

                $rawScore = $adjustedBase - $penalty + $bonus;

                if ($config->cap_max_score) {
                    $finalScore = min((float) $config->max_score_cap, max(0, $rawScore));
                } else {
                    $finalScore = max(0, $rawScore);
                }
                $report->score = round($finalScore, 2);
            } else {
                $report->score = 0.00;
            }

            $report->save();

            KpiReportHistory::create([
                'kpi_report_id' => $report->id,
                'user_id' => $userId,
                'previous_status' => $prevStatus,
                'new_status' => $report->status,
                'solution_log' => "MANUAL OVERRIDE BY MANAGEMENT ({$user->name}). Skor sebelumnya: {$prevScore} ➔ Skor baru: {$report->score}. Alasan: {$report->approval_reason}",
            ]);

            return $report;
        });
    }

    public function deleteReport(int $id): bool
    {
        $report = $this->kpiReportRepository->findById($id);
        return $report ? $this->kpiReportRepository->delete($report) : false;
    }

    public function getDashboardSummary(?int $userId = null): array
    {
        return $this->kpiReportRepository->getDashboardSummary($userId);
    }

    public function getDashboardAnalytics(?int $userId = null): array
    {
        return $this->kpiReportRepository->getDashboardAnalytics($userId);
    }

    public function getEmployeeKpiScore(int $userId): array
    {
        return $this->kpiReportRepository->getEmployeeKpiScore($userId);
    }

    public function getEmployeesList(): Collection
    {
        return $this->kpiReportRepository->getEmployeesList();
    }

    public function exportReports(array $filters): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $fileName = 'Laporan_KPI_Mingguan_' . date('Ymd') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\KpiReportExport($filters), $fileName);
    }

    public function importReports(\Illuminate\Http\UploadedFile $file, int $userId): array
    {
        $import = new \App\Imports\KpiReportImport($userId);
        \Maatwebsite\Excel\Facades\Excel::import($import, $file);

        return [
            'count' => $import->importedCount,
            'errors' => $import->errors,
            'detailed_errors' => $import->detailedErrors,
        ];
    }

    public function previewImport(\Illuminate\Http\UploadedFile $file): array
    {
        $array = \Maatwebsite\Excel\Facades\Excel::toArray(new \stdClass(), $file);
        $rows = $array[0] ?? [];
        $header = array_shift($rows) ?? [];
        $previewRows = array_slice($rows, 0, 10);

        return [
            'header' => $header,
            'preview_rows' => $previewRows,
            'total_rows' => count($rows),
        ];
    }
}
