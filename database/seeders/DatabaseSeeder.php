<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            KpiGradeSeeder::class,
            UserSeeder::class,
            ApplicationSeeder::class,
            RegionSeeder::class,
            KpiAppRegionMappingSeeder::class,
            KpiScoringRuleSeeder::class,
            KpiReportSeeder::class,
            KpiCriteriaSeeder::class,
            KpiWeightSeeder::class,
            KpiLevelSeeder::class,
            KpiGradeSchemeSeeder::class,
            KpiPredicateSeeder::class,
            PermissionSeeder::class,
        ]);
    }
}
