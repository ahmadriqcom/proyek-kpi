<?php

namespace Database\Seeders;

use App\Models\KpiCriteria;
use App\Models\KpiGrade;
use App\Models\KpiScoreInterpretation;
use Illuminate\Database\Seeder;

class KpiScoreInterpretationSeeder extends Seeder
{
    public function run(): void
    {
        $grades = KpiGrade::all();
        $criterias = KpiCriteria::all();

        $interpretationsMap = [
            1 => [
                'narasi' => 'Menunjukkan pemahaman dasar namun masih memerlukan bimbingan rutin dalam pelaksanaan tugas operasional harian.',
                'area' => 'Perlu meningkatkan ketelitian teknis, konsistensi waktu respons, dan pemahaman alur kerja aplikasi.',
                'rekomendasi' => 'Diberikan pendampingan (mentoring) dari senior dan diikutsertakan dalam pelatihan modul operasional dasar.',
            ],
            2 => [
                'narasi' => 'Mampu mengeskalasi dan menyelesaikan tugas teknis umum sesuai instruksi kerja standar dengan supervisi berkala.',
                'area' => 'Pengembangan pada kecepatan analisis akar masalah (root cause) dan kerapian dokumentasi teknis.',
                'rekomendasi' => 'Diberikan studi kasus penanganan kendala riil dan diminta menyusun draf petunjuk penyelesaian masalah.',
            ],
            3 => [
                'narasi' => 'Memenuhi standar operasional secara mandiri, mengidentifikasi kendala teknis dengan baik, dan memberikan kepuasan layanan yang stabil.',
                'area' => 'Perlu memperluas perspektif tata kelola bisnis dan keterhubungan antar modul aplikasi.',
                'rekomendasi' => 'Diikutsertakan dalam proyek implementasi tingkat menengah dan diberikan tanggung jawab pengujian sistem independen.',
            ],
            4 => [
                'narasi' => 'Menunjukkan efisiensi kerja tinggi, proaktif memitigasi risiko kendala, serta mampu memberikan komunikasi teknis yang solutif.',
                'area' => 'Pengembangan kemampuan manajemen pemangku kepentingan (stakeholder) dan pembimbingan junior.',
                'rekomendasi' => 'Diberikan peran sebagai pendamping teknis utama pada akun instansi serta memimpin diskusi penyelarasan kebutuhan.',
            ],
            5 => [
                'narasi' => 'Menunjukkan penguasaan tingkat tinggi (istimewa), inovatif, melampaui target kualitas, dan menjadi rujukan teknis organisasi.',
                'area' => 'Pengembangan pada perumusan inovasi arsitektur jangka panjang dan kontribusi pada standar organisasi.',
                'rekomendasi' => 'Diusulkan untuk promosi kenaikan jenjang grade dan ditugaskan menyusun standar praktik terbaik (best practices).',
            ],
        ];

        foreach ($grades as $grade) {
            foreach ($criterias as $crit) {
                for ($score = 1; $score <= 5; $score++) {
                    $base = $interpretationsMap[$score];

                    $narasi = "[{$grade->nama_grade} - {$crit->nama_kriteria}] {$base['narasi']}";
                    $area = "Area Fokus ({$crit->nama_kriteria}): {$base['area']}";
                    $rekomendasi = "Tindakan Rekomendasi ({$crit->nama_kriteria}): {$base['rekomendasi']}";

                    KpiScoreInterpretation::updateOrCreate([
                        'kpi_grade_id' => $grade->id,
                        'kpi_criteria_id' => $crit->id,
                        'score' => $score,
                    ], [
                        'narasi_interpretasi' => $narasi,
                        'area_pengembangan' => $area,
                        'rekomendasi_otomatis' => $rekomendasi,
                    ]);
                }
            }
        }
    }
}
