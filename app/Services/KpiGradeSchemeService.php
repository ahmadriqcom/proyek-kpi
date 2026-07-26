<?php

namespace App\Services;

use App\Models\KpiGradeScheme;
use App\Repositories\Contracts\KpiGradeSchemeRepositoryInterface;
use Illuminate\Support\Collection;

class KpiGradeSchemeService
{
    public function __construct(
        protected KpiGradeSchemeRepositoryInterface $schemeRepo
    ) {}

    public function getAllGrades(): Collection
    {
        return $this->schemeRepo->getAllGrades();
    }

    public function getGradeById(int $gradeId)
    {
        return $this->schemeRepo->getGradeById($gradeId);
    }

    public function getAllCriterias(): Collection
    {
        return $this->schemeRepo->getAllCriterias();
    }

    public function getLevels(): Collection
    {
        return $this->schemeRepo->getLevels();
    }

    public function getPredicates(): Collection
    {
        return $this->schemeRepo->getPredicates();
    }

    public function getWeightsByGrade(int $gradeId): Collection
    {
        return $this->schemeRepo->getWeightsByGrade($gradeId);
    }

    public function getSchemesByGrade(int $gradeId): Collection
    {
        return $this->schemeRepo->getSchemesByGrade($gradeId);
    }

    public function storeGrade(array $data)
    {
        return $this->schemeRepo->createGrade($data);
    }

    public function updateGrade(int $gradeId, array $data)
    {
        return $this->schemeRepo->updateGrade($gradeId, $data);
    }

    public function deleteGrade(int $gradeId): bool
    {
        return $this->schemeRepo->deleteGrade($gradeId);
    }

    public function updateGradeWeights(int $gradeId, array $weights): bool
    {
        return $this->schemeRepo->updateGradeWeights($gradeId, $weights);
    }

    public function storeIndicator(int $gradeId, array $data): KpiGradeScheme
    {
        return KpiGradeScheme::updateOrCreate([
            'kpi_grade_id' => $gradeId,
            'kpi_criteria_id' => $data['kpi_criteria_id'],
            'score' => $data['score'],
        ], [
            'indicator_description' => $data['indicator_description'],
        ]);
    }

    public function destroyIndicator(int $indicatorId): int
    {
        $scheme = KpiGradeScheme::findOrFail($indicatorId);
        $gradeId = $scheme->kpi_grade_id;
        $scheme->delete();
        return $gradeId;
    }
}
