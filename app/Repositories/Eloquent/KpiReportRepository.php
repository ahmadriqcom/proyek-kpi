<?php

namespace App\Repositories\Eloquent;

use App\Models\KpiReport;
use App\Models\User;
use App\Repositories\Contracts\KpiReportRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class KpiReportRepository implements KpiReportRepositoryInterface
{
    protected function applyFilters(Builder $query, array $filters): Builder
    {
        // Isolasi Data Operator: Jika role Operator, HANYA BISA melihat data buatannya sendiri
        if (auth()->check() && auth()->user()->isOperator()) {
            $query->where('created_by', auth()->id());
        } elseif (!empty($filters['user_id'])) {
            $query->where('created_by', $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('app_region_label', 'like', "%{$search}%")
                  ->orWhere('menu', 'like', "%{$search}%")
                  ->orWhere('problem', 'like', "%{$search}%")
                  ->orWhere('solution', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['user_id'])) {
            $query->where('created_by', $filters['user_id']);
        }

        if (!empty($filters['application_id'])) {
            $query->where('application_id', $filters['application_id']);
        }

        if (!empty($filters['region_id'])) {
            $query->where('region_id', $filters['region_id']);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['impact_level'])) {
            $query->where('impact_level', $filters['impact_level']);
        }

        if (isset($filters['min_score']) && $filters['min_score'] !== '') {
            $query->where('score', '>=', $filters['min_score']);
        }

        if (isset($filters['max_score']) && $filters['max_score'] !== '') {
            $query->where('score', '<=', $filters['max_score']);
        }

        return $query;
    }

    public function getFilteredPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $sortBy = $filters['sort_by'] ?? 'id';
        $sortDir = strtolower($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedSorts = ['id', 'ticket_number', 'start_date', 'end_date', 'score', 'status', 'priority', 'category'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $query = KpiReport::with(['application', 'region', 'creator', 'updater'])->orderBy($sortBy, $sortDir);
        return $this->applyFilters($query, $filters)->paginate($perPage)->withQueryString();
    }

    public function getAllFiltered(array $filters): Collection
    {
        $sortBy = $filters['sort_by'] ?? 'id';
        $sortDir = strtolower($filters['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $query = KpiReport::with(['application', 'region', 'creator', 'updater'])->orderBy($sortBy, $sortDir);
        return $this->applyFilters($query, $filters)->get();
    }

    public function findById(int $id): ?KpiReport
    {
        return KpiReport::with(['application', 'region', 'creator', 'updater', 'histories.user'])->find($id);
    }

    public function create(array $data): KpiReport
    {
        return KpiReport::create($data);
    }

    public function update(KpiReport $report, array $data): bool
    {
        return $report->update($data);
    }

    public function delete(KpiReport $report): bool
    {
        return $report->delete();
    }

    public function getDashboardSummary(?int $userId = null): array
    {
        $query = KpiReport::query();
        if ($userId) {
            $query->where('created_by', $userId);
        }

        $totalReports = (clone $query)->count();
        $completedCount = (clone $query)->where('status', 'completed')->count();
        $pendingCount = (clone $query)->whereIn('status', ['pending', 'on_progress'])->count();
        $cancelledCount = (clone $query)->where('status', 'cancelled')->count();

        $avgSla = (clone $query)->where('status', 'completed')->avg('sla_duration_days');

        return [
            'total_reports' => $totalReports,
            'completed_count' => $completedCount,
            'pending_count' => $pendingCount,
            'cancelled_count' => $cancelledCount,
            'completion_rate' => $totalReports > 0 ? round(($completedCount / $totalReports) * 100, 1) : 0,
            'avg_sla_days' => round($avgSla ?? 0, 1),
        ];
    }

    public function getEmployeeKpiScore(int $userId): array
    {
        $user = User::with('scoringRule')->find($userId);
        if (!$user) {
            return [];
        }

        $reports = KpiReport::where('created_by', $userId)->get();
        $total = $reports->count();
        $completed = $reports->where('status', 'completed')->count();
        $pending = $reports->whereIn('status', ['pending', 'on_progress'])->count();

        $rule = $user->scoringRule;
        $targetSla = $rule->target_sla_days ?? 2;

        // Dynamic 6 Criteria Scoring Matrix Calculations (Skala 1 - 5)
        // 1. Implementasi (20%): Evaluasi SLA & backlog
        $oneDayCompletedCount = $reports->where('status', 'completed')->where('sla_duration_days', '<=', 1)->count();
        $oneDayRate = $total > 0 ? ($oneDayCompletedCount / $total) : 0;
        $scoreImplementasi = match (true) {
            $oneDayRate >= 0.90 => 5,
            $oneDayRate >= 0.70 => 4,
            $oneDayRate >= 0.50 => 3,
            $total > 0 && ($completed / $total) >= 0.40 => 2,
            default => 1,
        };

        // 2. Data Management (15%): Evaluasi kelengkapan data & keterangan
        $completeDataCount = $reports->filter(fn($r) => !empty($r->remarks) && !empty($r->solution))->count();
        $dataRate = $total > 0 ? ($completeDataCount / $total) : 0;
        $scoreDataMgmt = match (true) {
            $dataRate >= 0.85 => 5,
            $dataRate >= 0.65 => 4,
            $total > 0 => 3,
            default => 2,
        };

        // 3. Problem Analysis (20%): Ketajaman identifikasi masalah & pencegahan isu berulang
        $uniqueProblems = $reports->pluck('problem')->unique()->count();
        $problemUniquenessRate = $total > 0 ? ($uniqueProblems / $total) : 0;
        $scoreProblemAnalysis = match (true) {
            $problemUniquenessRate >= 0.80 => 5,
            $problemUniquenessRate >= 0.60 => 4,
            $total > 0 => 3,
            default => 2,
        };

        // 4. Understand Regulations (15%): Ketepatan solusi sesuai alur proses bisnis & regulasi
        $completionRate = $total > 0 ? ($completed / $total) : 0;
        $scoreRegulations = match (true) {
            $completionRate >= 0.85 => 5,
            $completionRate >= 0.65 => 4,
            $total > 0 => 3,
            default => 2,
        };

        // 5. Expert Excel Usage (15%): Format data, validasi, dan efisiensi pengolahan data
        $scoreExcelUsage = $total > 0 ? 5 : 3;

        // 6. Mockup/UI/UX (15%): Masukan & perhatian pada kenyamanan alur pengguna
        $scoreUiUx = $total > 0 ? 4 : 3;

        $matrix = [
            [
                'kriteria' => 'Implementasi',
                'bobot_persen' => 20,
                'skor' => $scoreImplementasi,
                'nilai' => round(0.20 * $scoreImplementasi, 2),
                'catatan' => match ($scoreImplementasi) {
                    5 => 'Sangat Baik (>90% tugas selesai dalam 1 hari, tanpa penumpukan).',
                    4 => 'Baik (sebagian besar tugas selesai tepat waktu, keterlambatan 1-2 hari).',
                    3 => 'Cukup (ada beberapa keterlambatan / penumpukan 2-3 tugas).',
                    2 => 'Kurang (sering terjadi keterlambatan >3 hari).',
                    default => 'Sangat Kurang (tugas hampir selalu terlambat dan backlog tidak terkelola).',
                },
            ],
            [
                'kriteria' => 'Data Management',
                'bobot_persen' => 15,
                'skor' => $scoreDataMgmt,
                'nilai' => round(0.15 * $scoreDataMgmt, 2),
                'catatan' => match ($scoreDataMgmt) {
                    5 => 'Sangat Baik (semua kolom terisi lengkap, rapi, dan konsisten).',
                    4 => 'Baik (sebagian besar data rapi & kolom Keterangan informatif).',
                    3 => 'Cukup (cukup informatif, ada beberapa keterangan diisi seadanya).',
                    2 => 'Kurang (banyak data tidak diisi dan tidak konsisten).',
                    default => 'Sangat Kurang (data sangat berantakan & tidak lengkap).',
                },
            ],
            [
                'kriteria' => 'Problem Analysis',
                'bobot_persen' => 20,
                'skor' => $scoreProblemAnalysis,
                'nilai' => round(0.20 * $scoreProblemAnalysis, 2),
                'catatan' => match ($scoreProblemAnalysis) {
                    5 => 'Sangat Baik (uraian masalah sangat tajam & spesifik ke akar masalah).',
                    4 => 'Baik (mampu menjelaskan masalah dengan baik, minim pengulangan isu).',
                    3 => 'Cukup (masalah dijelaskan di permukaan, cenderung umum).',
                    2 => 'Kurang (penjelasan masalah tidak jelas / sulit dipahami).',
                    default => 'Sangat Kurang (masalah tidak relevan dengan solusi).',
                },
            ],
            [
                'kriteria' => 'Understand Regulations',
                'bobot_persen' => 15,
                'skor' => $scoreRegulations,
                'nilai' => round(0.15 * $scoreRegulations, 2),
                'catatan' => match ($scoreRegulations) {
                    5 => 'Sangat Baik (solusi sangat tepat, efektif, dan sepenuhnya sesuai regulasi).',
                    4 => 'Baik (solusi benar & menunjukkan pemahaman baik terhadap regulasi).',
                    3 => 'Cukup (paham alur dasar, ada kekeliruan minor yang tidak fatal).',
                    2 => 'Kurang (solusi menunjukkan kesalahpahaman alur bisnis).',
                    default => 'Sangat Kurang (solusi seringkali salah / melanggar aturan).',
                },
            ],
            [
                'kriteria' => 'Expert Excel Usage',
                'bobot_persen' => 15,
                'skor' => $scoreExcelUsage,
                'nilai' => round(0.15 * $scoreExcelUsage, 2),
                'catatan' => match ($scoreExcelUsage) {
                    5 => 'Sangat Baik (file sangat rapi, penggunaan fitur Excel efisien).',
                    4 => 'Baik (format data rapi & terstruktur dengan baik).',
                    3 => 'Cukup (penggunaan sebatas input data manual standar).',
                    2 => 'Kurang (format file tidak teratur & sulit dibaca).',
                    default => 'Sangat Kurang (file sangat berantakan & dikerjakan asal-asalan).',
                },
            ],
            [
                'kriteria' => 'Mockup/UI/UX',
                'bobot_persen' => 15,
                'skor' => $scoreUiUx,
                'nilai' => round(0.15 * $scoreUiUx, 2),
                'catatan' => match ($scoreUiUx) {
                    5 => 'Sangat Baik (secara proaktif memberikan masukan desain/alur solutif).',
                    4 => 'Baik (memberikan masukan ide relevan untuk perbaikan UI/UX).',
                    3 => 'Cukup (sedikit menyinggung kemudahan penggunaan dalam solusi).',
                    2 => 'Kurang (tidak menunjukkan kepedulian aspek UI/UX).',
                    default => 'Sangat Kurang (usulan memperburuk pengalaman pengguna).',
                },
            ],
        ];

        // Total Nilai (Skala 1.00 - 5.00)
        $totalScore5 = array_sum(array_column($matrix, 'nilai'));
        // Converted KPI Score (Skala 100)
        $totalScore100 = round($totalScore5 * 20, 1);

        // Cek jika terdapat Appraisal KPI ter-Approve dari Management untuk real-time update
        $approvedAppraisal = \App\Models\KpiAppraisal::where('user_id', $userId)
            ->where('approval_status', 'approved')
            ->latest()
            ->first();

        if ($approvedAppraisal) {
            $totalScore100 = round($approvedAppraisal->total_score, 1);
            $totalScore5 = round($totalScore100 / 20, 2);
        }

        $performanceCategory = match (true) {
            $totalScore100 >= 90 => 'SANGAT BAIK (A)',
            $totalScore100 >= 80 => 'BAIK (B)',
            $totalScore100 >= 70 => 'CUKUP (C)',
            default => 'PERLU EVALUASI (D)',
        };

        return [
            'user' => $user,
            'grade_level' => $user->grade_level,
            'grade_name' => $rule->grade_name ?? "Grade {$user->grade_level}",
            'target_sla_days' => $targetSla,
            'total_tasks' => $total,
            'completed_tasks' => $completed,
            'pending_tasks' => $pending,
            'matrix' => $matrix,
            'total_score_5' => round($totalScore5, 2),
            'kpi_score' => $totalScore100,
            'performance_category' => $performanceCategory,
        ];
    }

    public function getEmployeesList(): Collection
    {
        if (auth()->check() && auth()->user()->isOperator()) {
            return User::where('id', auth()->id())->get();
        }

        return User::whereIn('role', ['operator', 'super_admin'])
            ->orWhereHas('scoringRule')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getDashboardAnalytics(?int $userId = null): array
    {
        $query = KpiReport::query();
        if ($userId) {
            $query->where('created_by', $userId);
        }

        // 1. Monthly Trend Data (Last 6 Months)
        $months = [];
        $trendCompleted = [];
        $trendTotal = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subMonths($i);
            $monthName = $date->translatedFormat('M');
            $months[] = $monthName;

            $monthQuery = (clone $query)->whereYear('start_date', $date->year)->whereMonth('start_date', $date->month);
            $trendTotal[] = (clone $monthQuery)->count();
            $trendCompleted[] = (clone $monthQuery)->where('status', 'completed')->count();
        }

        // 2. Status Distribution
        $completed = (clone $query)->where('status', 'completed')->count();
        $pending = (clone $query)->whereIn('status', ['pending', 'on_progress'])->count();
        $cancelled = (clone $query)->where('status', 'cancelled')->count();

        // 3. Top 3 Applications with most issues
        $topApps = KpiReport::select('application_id', DB::raw('count(*) as total_issues'))
            ->when($userId, fn($q) => $q->where('created_by', $userId))
            ->whereNotNull('application_id')
            ->groupBy('application_id')
            ->orderByDesc('total_issues')
            ->with('application')
            ->take(3)
            ->get();

        // 4. Priority Alerts (Pending / On Progress)
        $priorityAlerts = (clone $query)
            ->whereIn('status', ['pending', 'on_progress'])
            ->orderBy('start_date', 'asc')
            ->take(4)
            ->get();

        return [
            'monthly_trend' => [
                'labels' => $months,
                'completed' => $trendCompleted,
                'total' => $trendTotal,
            ],
            'status_distribution' => [
                'completed' => $completed,
                'pending' => $pending,
                'cancelled' => $cancelled,
            ],
            'top_applications' => $topApps,
            'priority_alerts' => $priorityAlerts,
        ];
    }

    public function generateNextTicketNumber(?string $nik = null, ?string $appCode = null, ?string $regionCode = null): string
    {
        $nikStr = $nik ?: (auth()->check() ? (auth()->user()->nik ?: '202607') : '202607');
        $appStr = $appCode ? strtoupper(trim($appCode)) : 'APP';
        $regStr = $regionCode ? strtoupper(trim($regionCode)) : 'PST';
        $year = date('Y');

        $prefix = "KPI-{$nikStr}-{$appStr}-{$regStr}-";

        $latest = KpiReport::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNum = 1;
        if ($latest && preg_match('/-(\d{5})$/', $latest->ticket_number, $matches)) {
            $nextNum = ((int) $matches[1]) + 1;
        }

        return $prefix . sprintf('%05d', $nextNum);
    }
}
