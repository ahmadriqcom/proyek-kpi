<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ApplicationAndRegionSeeder extends Seeder
{
    /**
     * Run the database seeds for Application, Region, and Mapping.
     */
    public function run(): void
    {
        $this->call([
            ApplicationSeeder::class,
            RegionSeeder::class,
            KpiAppRegionMappingSeeder::class,
        ]);
    }
}
