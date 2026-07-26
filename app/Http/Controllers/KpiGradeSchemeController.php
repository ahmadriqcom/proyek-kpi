<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIndicatorRequest;
use App\Http\Requests\StoreKpiGradeRequest;
use App\Http\Requests\UpdateGradeWeightsRequest;
use App\Http\Requests\UpdateKpiGradeRequest;
use App\Services\KpiGradeSchemeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class KpiGradeSchemeController extends Controller
{
    public function __construct(
        protected KpiGradeSchemeService $schemeService
    ) {}

    /**
     * Tampilkan daftar Master Skema Penilaian Grade.
     */
    public function index(): View
    {
        $grades = $this->schemeService->getAllGrades();
        $criterias = $this->schemeService->getAllCriterias();
        $levels = $this->schemeService->getLevels();
        $predicates = $this->schemeService->getPredicates();

        return view('grade_schemes.index', compact('grades', 'criterias', 'levels', 'predicates'));
    }

    /**
     * Simpan Master Grade Baru (Superadmin).
     */
    public function store(StoreKpiGradeRequest $request): RedirectResponse
    {
        $this->schemeService->storeGrade($request->validated());

        return redirect()->route('grade-schemes.index')
            ->with('success', 'Master Grade baru berhasil ditambahkan.');
    }

    /**
     * Detail Skema Indikator per Grade & Bobot.
     */
    public function show(int $gradeId): View
    {
        $grade = $this->schemeService->getGradeById($gradeId);
        if (!$grade) {
            abort(404, 'Data Grade tidak ditemukan.');
        }

        $criterias = $this->schemeService->getAllCriterias();
        $weights = $this->schemeService->getWeightsByGrade($gradeId)->keyBy('kpi_criteria_id');
        $schemes = $this->schemeService->getSchemesByGrade($gradeId);

        return view('grade_schemes.show', compact('grade', 'criterias', 'weights', 'schemes'));
    }

    /**
     * Update Master Grade (Superadmin).
     */
    public function update(UpdateKpiGradeRequest $request, int $id): RedirectResponse
    {
        $this->schemeService->updateGrade($id, $request->validated());

        return redirect()->route('grade-schemes.index')
            ->with('success', 'Data Master Grade berhasil diperbarui.');
    }

    /**
     * Hapus Master Grade (Superadmin).
     */
    public function destroy(int $id): RedirectResponse
    {
        if (auth()->check() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Superadmin yang berwenang menghapus Master Grade.');
        }

        $this->schemeService->deleteGrade($id);

        return redirect()->route('grade-schemes.index')
            ->with('success', 'Master Grade beserta bobot dan indikatornya berhasil dihapus.');
    }

    /**
     * Update Bobot Penilaian Grade (Khusus Superadmin).
     */
    public function updateWeights(UpdateGradeWeightsRequest $request, int $gradeId): RedirectResponse
    {
        $this->schemeService->updateGradeWeights($gradeId, $request->input('weights', []));

        return redirect()->route('grade-schemes.show', $gradeId)
            ->with('success', 'Konfigurasi bobot kriteria berhasil diperbarui.');
    }

    /**
     * Tambah / Ubah Teks Indikator Rubrik Penilaian.
     */
    public function storeIndicator(StoreIndicatorRequest $request, int $gradeId): RedirectResponse
    {
        $this->schemeService->storeIndicator($gradeId, $request->validated());

        return redirect()->route('grade-schemes.show', $gradeId)
            ->with('success', 'Indikator rubrik penilaian berhasil disimpan.');
    }

    /**
     * Hapus Indikator Rubrik Penilaian.
     */
    public function destroyIndicator(int $indicatorId): RedirectResponse
    {
        if (auth()->check() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Superadmin yang berwenang menghapus indikator rubrik.');
        }

        $gradeId = $this->schemeService->destroyIndicator($indicatorId);

        return redirect()->route('grade-schemes.show', $gradeId)
            ->with('success', 'Indikator rubrik penilaian berhasil dihapus.');
    }

    /**
     * Cetak Ringkasan Skema Penilaian Grade ke PDF.
     */
    public function exportPdf(): Response
    {
        $grades = $this->schemeService->getAllGrades();
        $criterias = $this->schemeService->getAllCriterias();
        $levels = $this->schemeService->getLevels();
        $predicates = $this->schemeService->getPredicates();

        $pdf = Pdf::loadView('grade_schemes.pdf', compact('grades', 'criterias', 'levels', 'predicates'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Skema_Penilaian_Grade_KPI_' . date('Ymd_His') . '.pdf');
    }
}
