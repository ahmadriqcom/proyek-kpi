<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\KpiReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected KpiReportService $kpiReportService
    ) {}

    public function index(Request $request): View
    {
        $user = auth()->user();

        // Operator Restriction: HANYA bisa memilih dan melihat data dirinya sendiri
        if ($user && $user->isOperator()) {
            $employees = collect([$user]);
            $selectedUserId = $user->id;
        } else {
            $employees = $this->kpiReportService->getEmployeesList();
            $selectedUserId = $request->input('user_id') ? (int) $request->input('user_id') : null;
        }

        $summary = $this->kpiReportService->getDashboardSummary($selectedUserId);
        $analytics = $this->kpiReportService->getDashboardAnalytics($selectedUserId);

        $employeeScore = null;
        if ($selectedUserId) {
            $employeeScore = $this->kpiReportService->getEmployeeKpiScore($selectedUserId);
        }

        $filters = ['user_id' => $selectedUserId];
        $recentReports = $this->kpiReportService->getPaginatedReports($filters, 5);

        return view('dashboard.index', compact('summary', 'analytics', 'recentReports', 'employees', 'selectedUserId', 'employeeScore'));
    }

    /**
     * Download PDF Laporan KPI Pegawai lengkap dengan Tabel Matriks 6 Kriteria (Skala 1-5).
     */
    public function downloadPdf(Request $request)
    {
        $user = auth()->user();

        if ($user && $user->isOperator()) {
            $userId = $user->id;
        } else {
            $userId = $request->input('user_id');
            if (!$userId) {
                $firstEmp = $this->kpiReportService->getEmployeesList()->first();
                $userId = $firstEmp ? $firstEmp->id : ($user ? $user->id : 1);
            }
        }

        $employeeScore = $this->kpiReportService->getEmployeeKpiScore((int) $userId);
        if (empty($employeeScore)) {
            return redirect()->back()->with('error', 'Data pegawai tidak ditemukan untuk pengunduhan PDF.');
        }

        $reports = $this->kpiReportService->getAllReports(['user_id' => $userId]);

        $pdf = Pdf::loadView('dashboard.pdf_report', compact('employeeScore', 'reports'));
        $fileName = 'Laporan_KPI_Pegawai_' . str_replace(' ', '_', $employeeScore['user']->name) . '_' . date('Ymd') . '.pdf';

        return $pdf->download($fileName);
    }
}
