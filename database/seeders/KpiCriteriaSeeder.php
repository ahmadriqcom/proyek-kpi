<?php

namespace Database\Seeders;

use App\Models\KpiCriteria;
use Illuminate\Database\Seeder;

class KpiCriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $criterias = [
            [
                'kode_kriteria' => 'KRIT-01',
                'nama_kriteria' => 'Implementasi',
                'deskripsi' => 'Ketepatan waktu penyelesaian tugas, pengelolaan backlog, dan SLA operasional.',
                'bobot_default' => 25.00,
                'status_aktif' => true,
            ],
            [
                'kode_kriteria' => 'KRIT-02',
                'nama_kriteria' => 'Problem Analysis',
                'deskripsi' => 'Ketajaman analisis akar masalah dan pencegahan isu berulang.',
                'bobot_default' => 20.00,
                'status_aktif' => true,
            ],
            [
                'kode_kriteria' => 'KRIT-03',
                'nama_kriteria' => 'Data Management',
                'deskripsi' => 'Kelengkapan, konsistensi, dan kerapihan pencatatan data laporan.',
                'bobot_default' => 15.00,
                'status_aktif' => true,
            ],
            [
                'kode_kriteria' => 'KRIT-04',
                'nama_kriteria' => 'Understand Regulations',
                'deskripsi' => 'Kesesuaian solusi dengan regulasi teknis dan alur proses bisnis instansi.',
                'bobot_default' => 15.00,
                'status_aktif' => true,
            ],
            [
                'kode_kriteria' => 'KRIT-05',
                'nama_kriteria' => 'Expert Excel Usage',
                'deskripsi' => 'Penguasaan fitur pengolahan spreadsheet, rumus, dan validasi data.',
                'bobot_default' => 10.00,
                'status_aktif' => true,
            ],
            [
                'kode_kriteria' => 'KRIT-06',
                'nama_kriteria' => 'Mockup/UI/UX',
                'deskripsi' => 'Kepedulian dan usulan masukan desain/alur kerja solutif untuk kenyamanan user.',
                'bobot_default' => 5.00,
                'status_aktif' => true,
            ],
            [
                'kode_kriteria' => 'KRIT-07',
                'nama_kriteria' => 'Communication & Client Handling',
                'deskripsi' => 'Kemampuan komunikasi, penanganan keluhan klien, dan koordinasi tim.',
                'bobot_default' => 5.00,
                'status_aktif' => true,
            ],
            [
                'kode_kriteria' => 'KRIT-08',
                'nama_kriteria' => 'Leadership & Knowledge Sharing',
                'deskripsi' => 'Kepemimpinan, mentoring junior, dan pembagian dokumentasi pengetahuan.',
                'bobot_default' => 5.00,
                'status_aktif' => true,
            ],
        ];

        foreach ($criterias as $crit) {
            KpiCriteria::updateOrCreate(['kode_kriteria' => $crit['kode_kriteria']], $crit);
        }
    }
}
