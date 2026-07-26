<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface KpiGradeSchemeRepositoryInterface
{
    public function getAllGrades(): Collection;
    public function getGradeById(int $gradeId);
    public function getAllCriterias(): Collection;
    public function getWeightsByGrade(int $gradeId): Collection;
    public function getSchemesByGrade(int $gradeId): Collection;
    public function updateGradeWeights(int $gradeId, array $weights): bool;
    public function createGrade(array $data);
    public function updateGrade(int $gradeId, array $data);
    public function deleteGrade(int $gradeId): bool;
    public function getLevels(): Collection;
    public function getPredicates(): Collection;
    public function getPredicateForScore(float $score);
}
