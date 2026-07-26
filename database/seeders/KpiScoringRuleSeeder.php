<?php

namespace Database\Seeders;

use App\Models\KpiScoringRule;
use Illuminate\Database\Seeder;

class KpiScoringRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'grade_level' => 1,
                'grade_name' => 'Grade 1 - Junior Support',
                'target_sla_days' => 1,
                'base_score' => 100.00,
                'sla_penalty_per_day' => 10.00,
            ],
            [
                'grade_level' => 2,
                'grade_name' => 'Grade 2 - Support Specialist',
                'target_sla_days' => 2,
                'base_score' => 100.00,
                'sla_penalty_per_day' => 8.00,
            ],
            [
                'grade_level' => 3,
                'grade_name' => 'Grade 3 - Senior Support',
                'target_sla_days' => 3,
                'base_score' => 100.00,
                'sla_penalty_per_day' => 6.00,
            ],
            [
                'grade_level' => 4,
                'grade_name' => 'Grade 4 - Technical Lead',
                'target_sla_days' => 4,
                'base_score' => 100.00,
                'sla_penalty_per_day' => 5.00,
            ],
            [
                'grade_level' => 5,
                'grade_name' => 'Grade 5 - System Analyst',
                'target_sla_days' => 5,
                'base_score' => 100.00,
                'sla_penalty_per_day' => 4.00,
            ],
            [
                'grade_level' => 6,
                'grade_name' => 'Grade 6 - System Architect / Executive',
                'target_sla_days' => 7,
                'base_score' => 100.00,
                'sla_penalty_per_day' => 2.00,
            ],
        ];

        foreach ($rules as $rule) {
            KpiScoringRule::firstOrCreate(['grade_level' => $rule['grade_level']], $rule);
        }
    }
}
