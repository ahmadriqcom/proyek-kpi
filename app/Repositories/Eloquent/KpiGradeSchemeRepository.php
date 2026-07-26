<?php

namespace App\Repositories\Eloquent;

use App\Models\KpiCriteria;
use App\Models\KpiGrade;
use App\Models\KpiGradeScheme;
use App\Models\KpiLevel;
use App\Models\KpiPredicate;
use App\Models\KpiWeight;
use App\Repositories\Contracts\KpiGradeSchemeRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class KpiGradeSchemeRepository implements KpiGradeSchemeRepositoryInterface
{
    public function getAllGrades(): Collection
    {
        return KpiGrade::with('weights.criteria')->orderBy('urutan_grade', 'asc')->get();
    }

    public function getGradeById(int $gradeId)
    {
        return KpiGrade::with(['weights.criteria', 'schemes.criteria'])->find($gradeId);
    }

    public function getAllCriterias(): Collection
    {
        return KpiCriteria::where('status_aktif', true)->get();
    }

    public function getWeightsByGrade(int $gradeId): Collection
    {
        return KpiWeight::with('criteria')
            ->where('kpi_grade_id', $gradeId)
            ->get();
    }

    public function getSchemesByGrade(int $gradeId): Collection
    {
        return KpiGradeScheme::with('criteria')
            ->where('kpi_grade_id', $gradeId)
            ->get();
    }

    public function updateGradeWeights(int $gradeId, array $weights): bool
    {
        return DB::transaction(function () use ($gradeId, $weights) {
            foreach ($weights as $criteriaId => $weightPercent) {
                KpiWeight::updateOrCreate([
                    'kpi_grade_id' => $gradeId,
                    'kpi_criteria_id' => $criteriaId,
                ], [
                    'weight_percent' => $weightPercent,
                ]);
            }
            return true;
        });
    }

    public function createGrade(array $data)
    {
        return DB::transaction(function () use ($data) {
            $grade = KpiGrade::create([
                'kode_grade' => $data['kode_grade'],
                'nama_grade' => $data['nama_grade'],
                'career_path' => $data['career_path'] ?? null,
                'deskripsi_kompetensi' => $data['deskripsi_kompetensi'] ?? null,
                'tujuan_grade' => $data['tujuan_grade'] ?? null,
                'ekspektasi_kompetensi' => $data['ekspektasi_kompetensi'] ?? null,
                'level' => $data['level'],
                'urutan_grade' => $data['urutan_grade'],
                'status_aktif' => $data['status_aktif'] ?? true,
            ]);

            // Assign default weights from criterias
            $criterias = $this->getAllCriterias();
            foreach ($criterias as $criteria) {
                KpiWeight::create([
                    'kpi_grade_id' => $grade->id,
                    'kpi_criteria_id' => $criteria->id,
                    'weight_percent' => $criteria->bobot_default ?? 0,
                ]);
            }

            return $grade;
        });
    }

    public function updateGrade(int $gradeId, array $data)
    {
        $grade = KpiGrade::findOrFail($gradeId);
        $grade->update([
            'kode_grade' => $data['kode_grade'],
            'nama_grade' => $data['nama_grade'],
            'career_path' => $data['career_path'] ?? $grade->career_path,
            'deskripsi_kompetensi' => $data['deskripsi_kompetensi'] ?? $grade->deskripsi_kompetensi,
            'tujuan_grade' => $data['tujuan_grade'] ?? $grade->tujuan_grade,
            'ekspektasi_kompetensi' => $data['ekspektasi_kompetensi'] ?? $grade->ekspektasi_kompetensi,
            'level' => $data['level'],
            'urutan_grade' => $data['urutan_grade'],
            'status_aktif' => $data['status_aktif'] ?? true,
        ]);
        return $grade;
    }

    public function deleteGrade(int $gradeId): bool
    {
        return DB::transaction(function () use ($gradeId) {
            KpiWeight::where('kpi_grade_id', $gradeId)->delete();
            KpiGradeScheme::where('kpi_grade_id', $gradeId)->delete();
            $grade = KpiGrade::findOrFail($gradeId);
            return $grade->delete();
        });
    }

    public function getLevels(): Collection
    {
        return KpiLevel::orderBy('score', 'asc')->get();
    }

    public function getPredicates(): Collection
    {
        return KpiPredicate::orderBy('min_score', 'desc')->get();
    }

    public function getPredicateForScore(float $score)
    {
        return KpiPredicate::where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();
    }
}
