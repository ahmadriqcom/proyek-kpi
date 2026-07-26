<?php

namespace Database\Seeders;

use App\Models\KpiCriteria;
use App\Models\KpiGrade;
use App\Models\KpiWeight;
use Illuminate\Database\Seeder;

class KpiWeightSeeder extends Seeder
{
    public function run(): void
    {
        $grades = KpiGrade::all();
        $criterias = KpiCriteria::all()->keyBy('kode_kriteria');

        $weightsMapping = [
            'KRIT-01' => 25.00, // Implementasi
            'KRIT-02' => 20.00, // Problem Analysis
            'KRIT-03' => 15.00, // Data Management
            'KRIT-04' => 15.00, // Understand Regulations
            'KRIT-05' => 10.00, // Expert Excel Usage
            'KRIT-06' => 5.00,  // Mockup/UI/UX
            'KRIT-07' => 5.00,  // Communication
            'KRIT-08' => 5.00,  // Leadership
        ];

        foreach ($grades as $grade) {
            foreach ($weightsMapping as $code => $weight) {
                if (isset($criterias[$code])) {
                    KpiWeight::updateOrCreate([
                        'kpi_grade_id' => $grade->id,
                        'kpi_criteria_id' => $criterias[$code]->id,
                    ], [
                        'weight_percent' => $weight,
                    ]);
                }
            }
        }
    }
}
