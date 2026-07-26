<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportKpiReportRequest;
use App\Http\Requests\StoreKpiReportRequest;
use App\Http\Requests\UpdateKpiReportRequest;
use App\Models\Application;
use App\Models\KpiReport;
use App\Models\Region;
use App\Models\User;
use App\Services\KpiReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KpiReportController extends Controller
{
    public function __construct(
        protected KpiReportService $kpiReportService
    ) {}

    /**
     * Tampilkan data grid laporan KPI mingguan dengan filter multi-parameter, pagination, dan analytics.
     */
    public function index(Request $request): View
    {
        $filters = $request->only([
            'search', 'user_id', 'application_id', 'region_id', 'status',
            'start_date', 'end_date', 'category', 'priority', 'impact_level',
            'min_score', 'max_score', 'sort_by', 'sort_dir'
        ]);

        $reports = $this->kpiReportService->getPaginatedReports($filters, 15);
        $applications = Application::where('is_active', true)->get();
        $regions = Region::all();
        $employees = $this->kpiReportService->getEmployeesList();
        $summary = $this->kpiReportService->getDashboardSummary($filters['user_id'] ?? null);
        $analytics = $this->kpiReportService->getDashboardAnalytics($filters['user_id'] ?? null);

        return view('kpi_reports.index', compact('reports', 'applications', 'regions', 'employees', 'filters', 'summary', 'analytics'));
    }

    /**
     * Form tambah kendala KPI baru.
     */
    public function create(): View
    {
        $applications = Application::where('is_active', true)->get();
        $regions = Region::all();
        $categories = \App\Models\KpiCategory::where('is_active', true)->get();
        $priorities = \App\Models\KpiPriority::where('is_active', true)->get();
        $impacts = \App\Models\KpiImpactLevel::where('is_active', true)->get();

        return view('kpi_reports.create', compact('applications', 'regions', 'categories', 'priorities', 'impacts'));
    }

    /**
     * Simpan kendala KPI baru.
     */
    public function store(StoreKpiReportRequest $request): RedirectResponse
    {
        $report = $this->kpiReportService->createReport($request->validated(), auth()->id() ?? 1);

        $msg = "Laporan kendala [{$report->ticket_number}] berhasil ditambahkan.";
        if ($report->status === 'pending_approval') {
            $msg .= ' Status saat ini: [Pending Approval Management] karena prioritas/kategori bernilai tinggi.';
        }

        return redirect()
            ->route('kpi-reports.index')
            ->with('success', $msg);
    }

    /**
     * Tampilkan detail laporan KPI beserta riwayat penanganan solusi.
     */
    public function show(int $id): View
    {
        $report = $this->kpiReportService->getReportById($id);
        if (!$report) {
            abort(404, 'Laporan KPI tidak ditemukan.');
        }

        $categories = \App\Models\KpiCategory::where('is_active', true)->get();
        $priorities = \App\Models\KpiPriority::where('is_active', true)->get();
        $impacts = \App\Models\KpiImpactLevel::where('is_active', true)->get();

        return view('kpi_reports.show', compact('report', 'categories', 'priorities', 'impacts'));
    }

    /**
     * Form edit laporan KPI & update solusi.
     */
    public function edit(int $id): View
    {
        $report = $this->kpiReportService->getReportById($id);
        if (!$report) {
            abort(404, 'Laporan KPI tidak ditemukan.');
        }

        $applications = Application::where('is_active', true)->get();
        $regions = Region::all();
        $categories = \App\Models\KpiCategory::where('is_active', true)->get();
        $priorities = \App\Models\KpiPriority::where('is_active', true)->get();
        $impacts = \App\Models\KpiImpactLevel::where('is_active', true)->get();

        return view('kpi_reports.edit', compact('report', 'applications', 'regions', 'categories', 'priorities', 'impacts'));
    }

    /**
     * Update laporan KPI, tanggal selesai, dan solusi.
     */
    public function update(UpdateKpiReportRequest $request, int $id): RedirectResponse
    {
        $report = $this->kpiReportService->updateReport($id, $request->validated(), auth()->id() ?? 1);

        return redirect()
            ->route('kpi-reports.show', $report->id)
            ->with('success', "Laporan kendala [{$report->ticket_number}] dan solusi berhasil diperbarui.");
    }

    /**
     * Hapus laporan KPI (Soft Delete).
     */
    public function destroy(int $id): RedirectResponse
    {
        $deleted = $this->kpiReportService->deleteReport($id);

        if (!$deleted) {
            return redirect()->back()->with('error', 'Laporan KPI tidak ditemukan atau gagal dihapus.');
        }

        return redirect()
            ->route('kpi-reports.index')
            ->with('success', 'Laporan KPI berhasil dihapus.');
    }

    /**
     * Unduh Laporan KPI dalam format Spreadsheet Excel (.xlsx).
     */
    public function export(Request $request)
    {
        $filters = $request->only(['search', 'user_id', 'application_id', 'region_id', 'status', 'start_date', 'end_date', 'category', 'priority']);
        return $this->kpiReportService->exportReports($filters);
    }

    /**
     * Tampilkan halaman khusus unggah modul impor spreadsheet.
     */
    public function importView(): View
    {
        return view('kpi_reports.import');
    }

    /**
     * API JSON Preview Grid 10 Baris Pertama Spreadsheet sebelum Diimpor
     */
    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $previewData = $this->kpiReportService->previewImport($request->file('file'));
        return response()->json($previewData);
    }

    /**
     * Impor data Laporan KPI dari berkas Spreadsheet Excel (.xlsx / .csv).
     */
    public function import(ImportKpiReportRequest $request): RedirectResponse
    {
        $result = $this->kpiReportService->importReports($request->file('file'), auth()->id() ?? 1);

        $msg = "Berhasil mengimpor {$result['count']} baris laporan KPI dari spreadsheet.";
        if (!empty($result['errors'])) {
            $msg .= ' Beberapa baris dilewati/gagal diimpor.';
            session()->flash('import_detailed_errors', $result['detailed_errors']);
        }

        return redirect()->route('kpi-reports.index')->with('success', $msg);
    }

    /**
     * Hak Akses Override Nilai: Mengizinkan Superadmin mengedit Nilai KPI secara manual.
     */
    public function overrideScore(Request $request, int $id): RedirectResponse
    {
        if (auth()->check() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Superadmin yang berwenang melakukan override Nilai KPI secara manual.');
        }

        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $report = $this->kpiReportService->getReportById($id);
        if (!$report) {
            return redirect()->back()->with('error', 'Laporan KPI tidak ditemukan.');
        }

        $report->update(['score' => $request->score]);

        return redirect()->back()->with('success', "Nilai KPI laporan [{$report->ticket_number}] berhasil diperbarui secara manual oleh Superadmin menjadi {$request->score}.");
    }

    public function approveClassification(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'kpi_category_id' => 'nullable|exists:kpi_categories,id',
            'kpi_priority_id' => 'nullable|exists:kpi_priorities,id',
            'kpi_impact_level_id' => 'nullable|exists:kpi_impact_levels,id',
            'approval_reason' => 'required|string|min:5',
        ], [
            'approval_reason.required' => 'Alasan approval / penyesuaian klasifikasi wajib diisi.',
            'approval_reason.min' => 'Alasan approval minimal 5 karakter.',
        ]);

        $report = $this->kpiReportService->approveClassification($id, $validated, auth()->id() ?? 1);

        return redirect()->route('kpi-reports.show', $report->id)
            ->with('success', "Laporan [{$report->ticket_number}] berhasil disetujui & disesuaikan oleh Management. Skor baru: {$report->score}.");
    }

    /**
     * API JSON Dynamic Dependent Dropdown: Ambil daftar daerah yang ter-mapping dengan aplikasi.
     */
    public function getMappedRegions(int $id)
    {
        $application = Application::with('regions')->find($id);
        if (!$application) {
            return response()->json([]);
        }

        return response()->json($application->regions);
    }

    public function globalSearch(Request $request)
    {
        $q = trim($request->query('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = [];

        // 1. Laporan KPI
        $reports = KpiReport::where('ticket_number', 'like', "%{$q}%")
            ->orWhere('problem', 'like', "%{$q}%")
            ->orWhere('solution', 'like', "%{$q}%")
            ->orWhere('app_region_label', 'like', "%{$q}%")
            ->take(5)
            ->get();

        foreach ($reports as $rep) {
            $results[] = [
                'type' => 'Laporan KPI',
                'title' => "{$rep->ticket_number} - {$rep->app_region_label}",
                'subtitle' => \Illuminate\Support\Str::limit($rep->problem, 60),
                'url' => route('kpi-reports.show', $rep->id),
                'icon' => 'bi-file-earmark-text-fill text-primary',
            ];
        }

        // 2. Aplikasi
        $apps = Application::where('code', 'like', "%{$q}%")
            ->orWhere('name', 'like', "%{$q}%")
            ->take(3)
            ->get();

        foreach ($apps as $app) {
            $results[] = [
                'type' => 'Aplikasi',
                'title' => "{$app->code} - {$app->name}",
                'subtitle' => $app->description ?? 'Master Aplikasi',
                'url' => route('applications.index'),
                'icon' => 'bi-app-indicator text-success',
            ];
        }

        // 3. Daerah
        $regions = Region::where('code', 'like', "%{$q}%")
            ->orWhere('name', 'like', "%{$q}%")
            ->orWhere('province', 'like', "%{$q}%")
            ->take(3)
            ->get();

        foreach ($regions as $reg) {
            $results[] = [
                'type' => 'Daerah',
                'title' => "{$reg->code} - {$reg->name}",
                'subtitle' => "Provinsi {$reg->province}",
                'url' => route('regions.index'),
                'icon' => 'bi-geo-alt-fill text-warning',
            ];
        }

        // 4. Pegawai / User
        $users = User::where('name', 'like', "%{$q}%")
            ->orWhere('nik', 'like', "%{$q}%")
            ->orWhere('username', 'like', "%{$q}%")
            ->take(3)
            ->get();

        foreach ($users as $usr) {
            $results[] = [
                'type' => 'Pegawai / User',
                'title' => "{$usr->name} (NIK: {$usr->nik})",
                'subtitle' => "Role: {$usr->role} | Grade {$usr->grade_level}",
                'url' => route('users.index'),
                'icon' => 'bi-person-badge-fill text-info',
            ];
        }

        return response()->json($results);
    }
}
