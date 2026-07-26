<?php

namespace Database\Seeders;

use App\Models\KpiLevel;
use Illuminate\Database\Seeder;

class KpiLevelSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['score' => 1, 'label' => 'Sangat Kurang', 'converted_value' => 20.00],
            ['score' => 2, 'label' => 'Kurang', 'converted_value' => 40.00],
            ['score' => 3, 'label' => 'Cukup', 'converted_value' => 60.00],
            ['score' => 4, 'label' => 'Baik', 'converted_value' => 80.00],
            ['score' => 5, 'label' => 'Sangat Baik', 'converted_value' => 100.00],
        ];

        foreach ($levels as $lvl) {
            KpiLevel::updateOrCreate(['score' => $lvl['score']], $lvl);
        }
    }
}
