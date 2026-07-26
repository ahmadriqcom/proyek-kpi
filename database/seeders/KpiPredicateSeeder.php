<?php

namespace Database\Seeders;

use App\Models\KpiPredicate;
use Illuminate\Database\Seeder;

class KpiPredicateSeeder extends Seeder
{
    public function run(): void
    {
        $predicates = [
            [
                'min_score' => 90.00,
                'max_score' => 100.00,
                'predicate' => 'Sangat Baik',
                'recommendation' => 'Diusulkan Promosi Grade / Bonus Kinerja Utama & Apresiasi Direksi.',
            ],
            [
                'min_score' => 80.00,
                'max_score' => 89.99,
                'predicate' => 'Baik',
                'recommendation' => 'Dipertahankan pada posisi saat ini / Bonus Kinerja Standard.',
            ],
            [
                'min_score' => 70.00,
                'max_score' => 79.99,
                'predicate' => 'Cukup',
                'recommendation' => 'Dipertahankan dengan Catatan Pelatihan Teknis & Sertifikasi Tambahan.',
            ],
            [
                'min_score' => 60.00,
                'max_score' => 69.99,
                'predicate' => 'Kurang',
                'recommendation' => 'Pembinaan Kinerja Khusus (PIP) / Pendampingan oleh Senior Consultant.',
            ],
            [
                'min_score' => 0.00,
                'max_score' => 59.99,
                'predicate' => 'Sangat Kurang',
                'recommendation' => 'Penerbitan Surat Peringatan (SP) & Evaluasi Ulang Masa Kerja Konsultan.',
            ],
        ];

        foreach ($predicates as $pred) {
            KpiPredicate::updateOrCreate(['predicate' => $pred['predicate']], $pred);
        }
    }
}
