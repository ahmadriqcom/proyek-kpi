<?php

namespace App\Services;

use App\Models\KpiGrade;
use App\Models\KpiGradeScheme;
use App\Models\KpiLevel;
use App\Models\KpiScoreInterpretation;
use App\Models\User;
use App\Repositories\Contracts\KpiAppraisalRepositoryInterface;
use App\Repositories\Contracts\KpiGradeSchemeRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use InvalidArgumentException;

class KpiAppraisalService
{
    public function __construct(
        protected KpiAppraisalRepositoryInterface $appraisalRepo,
        protected KpiGradeSchemeRepositoryInterface $schemeRepo
    ) {}

    public function getPaginatedAppraisals(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->appraisalRepo->getPaginatedAppraisals($filters, $perPage);
    }

    public function getAppraisalById(int $id)
    {
        return $this->appraisalRepo->findById($id);
    }

    /**
     * Hitung otomatis Penilaian Pegawai berdasarkan Skor Input 1-5, Bobot Grade, dan Dynamic Interpretation Matrix f(Grade, Kriteria, Skor).
     */
    public function calculateAndStoreAppraisal(int $userId, array $scores, ?int $evaluatorId = null, ?string $justification = null)
    {
        $user = User::findOrFail($userId);
        $grade = KpiGrade::where('level', $user->grade_level)->first() ?? KpiGrade::first();
        $weights = $this->schemeRepo->getWeightsByGrade($grade->id);
        $levels = $this->schemeRepo->getLevels()->keyBy('score');

        // Validasi total bobot wajib 100%
        $totalWeight = $weights->sum('weight_percent');
        if (abs($totalWeight - 100.00) > 0.01) {
            throw new InvalidArgumentException("Total bobot kriteria untuk Grade {$grade->nama_grade} adalah {$totalWeight}%, wajib tepat 100%.");
        }

        $strongestList = [];
        $weakestList = [];
        $detailsData = [];
        $totalFinalScore = 0.00;

        foreach ($weights as $w) {
            $criteriaId = $w->kpi_criteria_id;
            $critName = $w->criteria->nama_kriteria ?? 'Kriteria';
            $scoreInput = (int) ($scores[$criteriaId] ?? 3);
            $level = $levels->get($scoreInput);
            $convertedValue = $level ? $level->converted_value : ($scoreInput * 20);

            // Rumus Perhitungan Otomatis: Nilai Kriteria = (Bobot (%) * Nilai Konversi) / 100
            $weightedScore = round(($w->weight_percent * $convertedValue) / 100, 2);
            $totalFinalScore += $weightedScore;

            // Lookup Dynamic Interpretation Matrix f(Grade, Kriteria, Skor)
            $interp = KpiScoreInterpretation::where('kpi_grade_id', $grade->id)
                ->where('kpi_criteria_id', $criteriaId)
                ->where('score', $scoreInput)
                ->first();

            $scheme = KpiGradeScheme::where('kpi_grade_id', $grade->id)
                ->where('kpi_criteria_id', $criteriaId)
                ->where('score', $scoreInput)
                ->first();

            $indicatorText = $interp ? $interp->narasi_interpretasi : ($scheme ? $scheme->indicator_description : "Skor {$scoreInput} untuk {$critName}");
            $itemNotes = $interp ? $interp->rekomendasi_otomatis : "Skor {$scoreInput} - Memenuhi standar dasar.";

            $detailsData[] = [
                'kpi_criteria_id' => $criteriaId,
                'weight_percent' => $w->weight_percent,
                'score_input' => $scoreInput,
                'converted_value' => $convertedValue,
                'weighted_score' => $weightedScore,
                'indicator_snapshot' => $indicatorText,
                'notes' => $itemNotes,
            ];

            if ($scoreInput >= 4) {
                $strongestList[] = $critName;
            } else {
                $weakestList[] = $critName;
            }
        }

        $strongestStr = !empty($strongestList) ? implode(', ', $strongestList) : 'Kriteria standar (stabil)';
        $weakestStr = !empty($weakestList) ? implode(', ', $weakestList) : 'Tidak ada (Performa sangat optimal)';

        // Narasi Ringkasan Eksekutif Otomatis
        $execSummary = "Berdasarkan hasil penilaian, konsultan menunjukkan kompetensi yang baik dalam {$strongestStr}. ";
        if (!empty($weakestList)) {
            $execSummary .= "Pengembangan selanjutnya dapat difokuskan pada {$weakestStr} agar kualitas pelaksanaan proyek semakin konsisten.";
        } else {
            $execSummary .= "Pertahankan kinerja istimewa ini untuk mendukung pencapaian target organisasi.";
        }

        // Penentuan Predikat Otomatis & Rekomendasi
        $predicateObj = $this->schemeRepo->getPredicateForScore($totalFinalScore);
        $predicate = $predicateObj ? $predicateObj->predicate : 'Baik';
        $recommendation = $predicateObj ? $predicateObj->recommendation : 'Dipertahankan pada posisi saat ini.';

        $appraisalData = [
            'appraisal_number' => $this->appraisalRepo->generateAppraisalNumber(),
            'user_id' => $user->id,
            'kpi_grade_id' => $grade->id,
            'evaluator_id' => $evaluatorId ?? auth()->id(),
            'total_score' => round($totalFinalScore, 2),
            'predicate' => $predicate,
            'recommendation' => $recommendation,
            'approval_status' => 'submitted',
            'evaluator_justification' => $justification,
            'strongest_competency' => $strongestStr,
            'weakest_competency' => $weakestStr,
            'executive_summary' => $execSummary,
            'scheme_version' => 1,
        ];

        return $this->appraisalRepo->create($appraisalData, $detailsData);
    }

    /**
     * Persetujuan (Approval) Appraisal oleh Management.
     */
    public function approveAppraisal(int $id, string $notes, int $evaluatorId)
    {
        return $this->appraisalRepo->updateStatus($id, 'approved', $notes, $evaluatorId);
    }

    /**
     * Penolakan Appraisal oleh Management.
     */
    public function rejectAppraisal(int $id, string $notes, int $evaluatorId)
    {
        return $this->appraisalRepo->updateStatus($id, 'rejected', $notes, $evaluatorId);
    }

    /**
     * Export Appraisal Report to PDF.
     */
    public function downloadPdf(int $id)
    {
        $appraisal = $this->appraisalRepo->findById($id);
        if (!$appraisal) {
            throw new InvalidArgumentException("Data Penilaian KPI tidak ditemukan.");
        }

        $pdf = Pdf::loadView('appraisals.pdf_report', compact('appraisal'));
        $fileName = 'Laporan_Appraisal_KPI_' . str_replace(' ', '_', $appraisal->user->name) . '_' . date('Ymd') . '.pdf';

        return $pdf->download($fileName);
    }
}
