<?php

namespace Database\Seeders;

use App\Models\KpiCategory;
use App\Models\KpiFormulaConfig;
use App\Models\KpiImpactLevel;
use App\Models\KpiPriority;
use Illuminate\Database\Seeder;

class KpiMasterScoringSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categories
        $categories = [
            [
                'name' => 'Technical / Bug System',
                'description' => 'Kendala kesalahan teknis, kegagalan fungsi aplikasi, atau bug sistem.',
                'complexity_weight' => 1.00,
                'requires_approval' => false,
            ],
            [
                'name' => 'Enhancement Feature',
                'description' => 'Penambahan modul atau peningkatan fitur aplikasi baru.',
                'complexity_weight' => 1.20,
                'requires_approval' => false,
            ],
            [
                'name' => 'Integrasi Sistem',
                'description' => 'Kendala API, web services, atau konektivitas integrasi antar-sistem OPD.',
                'complexity_weight' => 1.40,
                'requires_approval' => true,
            ],
            [
                'name' => 'Konsultasi / Pendampingan',
                'description' => 'Bantuan konsultasi alur proses bisnis atau pendampingan operasional.',
                'complexity_weight' => 0.80,
                'requires_approval' => false,
            ],
            [
                'name' => 'Permintaan Data',
                'description' => 'Ekstraksi, rekapitulasi, atau kueri data khusus dari database.',
                'complexity_weight' => 0.90,
                'requires_approval' => false,
            ],
            [
                'name' => 'Training / Knowledge Sharing',
                'description' => 'Pelatihan dan sosialisasi penggunaan aplikasi ke ASN/klien.',
                'complexity_weight' => 1.10,
                'requires_approval' => false,
            ],
        ];

        foreach ($categories as $cat) {
            KpiCategory::updateOrCreate(['name' => $cat['name']], $cat);
        }

        // 2. Priorities
        $priorities = [
            [
                'name' => 'Low',
                'description' => 'Kendala bersifat minor, tidak mengganggu alur operasional utama.',
                'urgency_weight' => 0.80,
                'target_sla_days' => 5,
            ],
            [
                'name' => 'Medium',
                'description' => 'Kendala operasional standar yang memiliki alternatif penanganan sementara.',
                'urgency_weight' => 1.00,
                'target_sla_days' => 3,
            ],
            [
                'name' => 'High',
                'description' => 'Kendala berdampak pada modul utama dan membutuhkan penanganan cepat.',
                'urgency_weight' => 1.30,
                'target_sla_days' => 2,
            ],
            [
                'name' => 'Critical',
                'description' => 'Kendala darurat yang menyebabkan seluruh sistem mati / tidak dapat digunakan.',
                'urgency_weight' => 1.60,
                'target_sla_days' => 1,
            ],
        ];

        foreach ($priorities as $prio) {
            KpiPriority::updateOrCreate(['name' => $prio['name']], $prio);
        }

        // 3. Impact Levels
        $impacts = [
            [
                'name' => 'Individual',
                'description' => 'Hanya berdampak pada 1 orang pengguna/operator.',
                'impact_weight' => 0.80,
            ],
            [
                'name' => 'Unit Kerja',
                'description' => 'Berdampak pada 1 unit bidang/seksi kerja.',
                'impact_weight' => 1.00,
            ],
            [
                'name' => 'Satu OPD',
                'description' => 'Berdampak pada keseluruhan 1 Organisasi Perangkat Daerah.',
                'impact_weight' => 1.20,
            ],
            [
                'name' => 'Lintas OPD',
                'description' => 'Berdampak pada hubungan koordinasi beberapa OPD sekaligus.',
                'impact_weight' => 1.40,
            ],
            [
                'name' => 'Seluruh Instansi',
                'description' => 'Berdampak pada seluruh instansi pemerintah / daerah.',
                'impact_weight' => 1.60,
            ],
        ];

        foreach ($impacts as $imp) {
            KpiImpactLevel::updateOrCreate(['name' => $imp['name']], $imp);
        }

        // 4. Formula Config Singleton
        KpiFormulaConfig::getActiveConfig();
    }
}
