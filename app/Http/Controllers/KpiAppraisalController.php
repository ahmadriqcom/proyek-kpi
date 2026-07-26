<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKpiAppraisalRequest;
use App\Repositories\Contracts\KpiGradeSchemeRepositoryInterface;
use App\Services\KpiAppraisalService;
use App\Services\KpiReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KpiAppraisalController extends Controller
{
    public function __construct(
        protected KpiAppraisalService $appraisalService,
        protected KpiGradeSchemeRepositoryInterface $schemeRepo,
        protected KpiReportService $reportService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'user_id', 'approval_status']);
        $appraisals = $this->appraisalService->getPaginatedAppraisals($filters, 15);
        $employees = $this->reportService->getEmployeesList();

        return view('appraisals.index', compact('appraisals', 'employees', 'filters'));
    }

    public function create(Request $request): View
    {
        $employees = $this->reportService->getEmployeesList();

        if (auth()->check() && auth()->user()->isOperator()) {
            $selectedUserId = auth()->id();
        } else {
            $selectedUserId = $request->input('user_id') ? (int) $request->input('user_id') : ($employees->first()->id ?? 1);
        }

        $selectedUser = $employees->find($selectedUserId) ?? auth()->user();

        $grade = $this->schemeRepo->getAllGrades()->where('level', $selectedUser->grade_level ?? 1)->first() ?? $this->schemeRepo->getAllGrades()->first();
        $weights = $this->schemeRepo->getWeightsByGrade($grade->id);
        $schemes = $this->schemeRepo->getSchemesByGrade($grade->id);

        return view('appraisals.create', compact('employees', 'selectedUser', 'grade', 'weights', 'schemes'));
    }

    public function store(StoreKpiAppraisalRequest $request): RedirectResponse
    {
        $userId = (auth()->check() && auth()->user()->isOperator())
            ? auth()->id()
            : (int) $request->input('user_id');
        $scores = $request->input('scores', []); // [criteria_id => score (1-5)]
        $justification = $request->input('evaluator_justification');

        try {
            $appraisal = $this->appraisalService->calculateAndStoreAppraisal($userId, $scores, auth()->id() ?? 1, $justification);

            return redirect()->route('appraisals.show', $appraisal->id)
                ->with('success', "Penilaian KPI Pegawai [{$appraisal->appraisal_number}] berhasil disimpan dan dihitung otomatis.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(int $id): View
    {
        $appraisal = $this->appraisalService->getAppraisalById($id);
        if (!$appraisal) {
            abort(404, 'Data Penilaian KPI tidak ditemukan.');
        }

        return view('appraisals.show', compact('appraisal'));
    }

    public function approve(Request $request, int $id): RedirectResponse
    {
        if (auth()->check() && auth()->user()->role === 'operator') {
            abort(403, 'Operator tidak memiliki akses persetujuan (approval).');
        }

        $notes = $request->input('approval_notes', 'Disetujui oleh Management.');
        $this->appraisalService->approveAppraisal($id, $notes, auth()->id() ?? 1);

        return redirect()->route('appraisals.show', $id)
            ->with('success', 'Penilaian KPI pegawai berhasil disetujui (Approved).');
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        if (auth()->check() && auth()->user()->role === 'operator') {
            abort(403, 'Operator tidak memiliki akses persetujuan (approval).');
        }

        $notes = $request->input('approval_notes', 'Memerlukan perbaikan penilaian.');
        $this->appraisalService->rejectAppraisal($id, $notes, auth()->id() ?? 1);

        return redirect()->route('appraisals.show', $id)
            ->with('warning', 'Penilaian KPI pegawai ditolak (Rejected) dan dikembalikan ke draft.');
    }

    public function downloadPdf(int $id)
    {
        return $this->appraisalService->downloadPdf($id);
    }
}
