<?php

namespace Database\Seeders;

use App\Models\KpiCriteria;
use App\Models\KpiGrade;
use App\Models\KpiGradeScheme;
use Illuminate\Database\Seeder;

class KpiGradeSchemeSeeder extends Seeder
{
    public function run(): void
    {
        $grades = KpiGrade::all()->keyBy('kode_grade');
        $criterias = KpiCriteria::all();

        $rubricsByGrade = [
            'GRADE-1' => [
                1 => 'Memiliki peluang untuk meningkatkan kecepatan dan ketelitian dalam mencatat serta mengategorikan laporan kendala pengguna, serta masih memerlukan pendampingan.',
                2 => 'Menunjukkan kemampuan dasar dalam mencatat laporan dan memberikan respons awal kepada pengguna sesuai petunjuk kerja yang ada.',
                3 => 'Mampu menyelesaikan penanganan kendala dasar secara mandiri dan mengeskalasi tiket teknis secara tepat waktu.',
                4 => 'Mampu merespons kendala dengan cepat, efisien, dan memberikan komunikasi yang ramah serta solutif kepada pengguna.',
                5 => 'Menunjukkan efisiensi kerja yang sangat tinggi, konsisten melampaui target respons, serta aktif merekomendasikan perbaikan alur penerimaan laporan.',
            ],
            'GRADE-2' => [
                1 => 'Memerlukan penyesuaian dalam memahami penanganan masalah aplikasi dan memerlukan bimbingan berkala saat mengidentifikasi sumber kendala.',
                2 => 'Mampu menangani kendala teknis umum serta memberikan instruksi pemecahan masalah dasar kepada pengguna secara cukup akurat.',
                3 => 'Mampu mendiagnosis kendala dengan cepat, menyelesaikan masalah sesuai kriteria layanan, dan mendokumentasikan hasilnya.',
                4 => 'Memiliki pemahaman teknis yang kuat, mampu menangani volume kendala tinggi dengan tingkat kepuasan pengguna yang baik.',
                5 => 'Menunjukkan keahlian tinggi dalam analisis kendala berulang serta aktif memperbarui basis pengetahuan (knowledge base) tim dukungan teknis.',
            ],
            'GRADE-3' => [
                1 => 'Perlu memperkuat pemahaman konfigurasi teknis dan masih memerlukan supervisi dalam pelaksanaan pengujian sistem di lapangan.',
                2 => 'Mampu melaksanakan tahapan implementasi standar dan membantu pengujian fungsionalitas sistem sesuai instruksi kerja.',
                3 => 'Mampu mengonfigurasi sistem secara mandiri, mengidentifikasi kendala penerapan, dan memberikan pendampingan teknis bagi pengguna.',
                4 => 'Menguasai alur implementasi secara komprehensif, menyelesaikan kendala integrasi secara responsif, serta memastikan kesiapan operasional sistem.',
                5 => 'Menunjukkan keunggulan dalam manajemen implementasi, mampu mengantisipasi potensi kendala teknis lapangan, dan memberikan pelatihan bagi pengguna secara sangat efektif.',
            ],
            'GRADE-4' => [
                1 => 'Masih memerlukan pengembangan dalam kemampuan analisis proses bisnis dan perlu pendampingan saat menyusun dokumen kebutuhan sistem.',
                2 => 'Mampu mengumpulkan data kebutuhan pengguna dan menyusun laporan evaluasi dasar dengan pengawasan terbatas.',
                3 => 'Mampu mengidentifikasi kesenjangan proses bisnis (gap analysis) secara mandiri dan menyusun dokumentasi teknis yang jelas.',
                4 => 'Mampu memberikan rekomendasi penyesuaian fitur yang bernilai tambah serta memfasilitasi diskusi teknis dengan pemangku kepentingan secara efektif.',
                5 => 'Menunjukkan ketajaman analisis bisnis yang tinggi, menghasilkan dokumentasi yang sangat terstruktur, dan diakui keahliannya oleh pengguna.',
            ],
            'GRADE-5' => [
                1 => 'Memiliki peluang untuk meningkatkan efektivitas komunikasi dengan pemangku kepentingan tingkat menengah dan memerlukan konsultasi dalam penanganan masalah kompleks.',
                2 => 'Mampu memimpin pengumpulan kebutuhan pengguna dan merumuskan solusi standar sesuai lingkup proyek.',
                3 => 'Mampu mengelola konsultasi proyek secara independen, memberikan solusi atas dinamika kebutuhan pengguna, dan menjaga stabilitas implementasi.',
                4 => 'Mampu memitigasi risiko proyek dari sisi fungsional serta memberikan solusi inovatif yang meningkatkan efisiensi operasional pengguna.',
                5 => 'Menunjukkan kepemimpinan konsultatif yang matang, menjadi penasihat tepercaya bagi pengguna, dan secara konsisten memastikan keberhasilan adopsi sistem.',
            ],
            'GRADE-6' => [
                1 => 'Perlu memperkuat wawasan strategis tata kelola IT dan masih memerlukan arahan dalam mengelola konflik kepentingan teknis.',
                2 => 'Mampu mengarahkan anggota tim dalam tugas konsultasi dan menyelesaikan kendala fungsional proyek yang tidak terstruktur.',
                3 => 'Mampu merancang solusi fungsional untuk proyek kompleks, memimpin presentasi teknis, dan memberikan pembimbingan (mentoring) bagi anggota tim.',
                4 => 'Menunjukkan kepemimpinan teknis yang proaktif, efisien dalam pengambilan keputusan, serta dipercaya mengelola pemangku kepentingan tingkat senior.',
                5 => 'Menjadi acuan keahlian (subject matter expert) di organisasi, mampu merumuskan standar baru konsultasi, dan memberikan dampak strategis pada keberhasilan proyek.',
            ],
            'GRADE-7' => [
                1 => 'Masih memerlukan pemantauan dalam mengevaluasi dampak strategis lintas aplikasi dan perlu memperkuat manajemen risiko tingkat portofolio.',
                2 => 'Mampu melakukan supervisi terhadap beberapa jalur implementasi dan memastikan keselarasan antar modul teknis.',
                3 => 'Mampu mengidentifikasi risiko sistemik secara presisi, merumuskan solusi lintas sektor, dan menjaga mutu seluruh keluaran tim konsultasi.',
                4 => 'Menunjukkan kemampuan dalam menyelaraskan kebutuhan teknis dengan regulasi pemerintah/instansi serta memimpin negosiasi teknis bernilai tinggi.',
                5 => 'Menunjukkan keunggulan strategis dalam penataan alur kerja digitalisasi, menginspirasi tim melalui praktik terbaik, dan menghasilan standar layanan berkategori tinggi.',
            ],
            'GRADE-8' => [
                1 => 'Memiliki peluang untuk memperluas perspektif transformasi jangka panjang serta memerlukan keselarasan lebih erat dengan manajemen puncak klien.',
                2 => 'Mampu mengoordinasikan tim konsultan senior dan menyelaraskan berbagai sasaran teknis menjadi satu strategi implementasi yang utuh.',
                3 => 'Mampu mengarahkan visi proyek secara berkesinambungan, mengatasi hambatan transformasi tingkat organisasi, dan mengawal kepuasan pemangku kepentingan eksekutif.',
                4 => 'Menunjukkan kepemimpinan visioner, mampu menyusun tata kelola implementasi yang tangguh, serta memastikan efisiensi sumber daya secara menyeluruh.',
                5 => 'Menjadi penggerak utama transformasi digital, menghasilkan inovasi skema layanan yang diakui secara luas, dan membangun standar keunggulan konsultasi organisasi.',
            ],
            'GRADE-9' => [
                1 => 'Masih memerlukan penyesuaian dalam merumuskan rencana induk (master plan) IT jangka panjang dan perlu memperkuat sinergi dengan pemangku kebijakan utama.',
                2 => 'Mampu menyusun kerangka arsitektur solusi umum dan memimpin validasi kelayakan teknis pada proyek-proyek strategis.',
                3 => 'Mampu merancang cetak biru solusi enterprise yang terintegrasi, efisien, serta aman, dan diakui sebagai otoritas teknis tertinggi dalam proyek.',
                4 => 'Menunjukkan keahlian luar biasa dalam merumuskan inovasi arsitektur teknologi yang berdampak nasional/instansional dan mengoptimalkan return on investment teknologi.',
                5 => 'Menjadi tokoh pemikir (thought leader) dalam bidang solusi IT pemerintah/instansi, menentukan standar arsitektur masa depan, dan menciptakan nilai transformasi yang sangat signifikan.',
            ],
        ];

        foreach ($rubricsByGrade as $gradeCode => $scoreMap) {
            if (!isset($grades[$gradeCode])) {
                continue;
            }

            $grade = $grades[$gradeCode];

            foreach ($criterias as $crit) {
                for ($s = 1; $s <= 5; $s++) {
                    KpiGradeScheme::updateOrCreate([
                        'kpi_grade_id' => $grade->id,
                        'kpi_criteria_id' => $crit->id,
                        'score' => $s,
                    ], [
                        'indicator_description' => $scoreMap[$s] ?? "Deskripsi indikator skor {$s} untuk {$grade->nama_grade}.",
                    ]);
                }
            }
        }
    }
}
