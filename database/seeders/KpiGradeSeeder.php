<?php

namespace Database\Seeders;

use App\Models\KpiGrade;
use Illuminate\Database\Seeder;

class KpiGradeSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            [
                'kode_grade' => 'GRADE-1',
                'nama_grade' => 'Grade 1',
                'career_path' => 'IT Help Desk',
                'deskripsi_kompetensi' => 'Menunjukkan kemampuan dasar dalam menerima, mencatat, dan merespons permintaan bantuan atau kendala teknis tingkat pertama dari pengguna dengan bimbingan operasional.',
                'tujuan_grade' => 'Memastikan setiap kendala awal pengguna tercatat secara sistematis dan mendapatkan respons cepat sesuai standar layanan.',
                'ekspektasi_kompetensi' => 'Mampu mengoperasikan sistem tiket/laporan kendala, melakukan verifikasi masalah dasar, serta mengarahkan masalah kompleks ke tim tingkat lanjut.',
                'career_path_requirements' => 'Persyaratan Promosi G1 ➔ G2: Skor Kriteria Implementasi ≥ 3, Problem Analysis ≥ 3, dan Kepuasan Pengguna (Komunikasi) ≥ 3.',
                'level' => 1,
                'urutan_grade' => 1,
                'status_aktif' => true,
            ],
            [
                'kode_grade' => 'GRADE-2',
                'nama_grade' => 'Grade 2',
                'career_path' => 'Junior Help Desk',
                'deskripsi_kompetensi' => 'Menunjukkan kemampuan dalam menganalisis dan menyelesaikan masalah teknis umum serta memberikan panduan operasional dasar kepada pengguna secara mandiri.',
                'tujuan_grade' => 'Meningkatkan tingkat penyelesaian kendala pada tingkat pertama (first-level resolution) dan meminimalkan eskalasi yang tidak perlu.',
                'ekspektasi_kompetensi' => 'Memahami alur kerja aplikasi utama, mampu melakukan penanganan masalah (troubleshooting) tingkat lanjut, dan mendokumentasikan langkah penanganan kendala.',
                'career_path_requirements' => 'Persyaratan Promosi G2 ➔ G3: Skor Kriteria Implementasi ≥ 4, Problem Analysis ≥ 3, dan Data Management ≥ 3.',
                'level' => 2,
                'urutan_grade' => 2,
                'status_aktif' => true,
            ],
            [
                'kode_grade' => 'GRADE-3',
                'nama_grade' => 'Grade 3',
                'career_path' => 'IT Implementator',
                'deskripsi_kompetensi' => 'Menunjukkan kemampuan mengimplementasikan, mengonfigurasi, dan menguji sistem atau aplikasi di lingkungan pengguna sesuai spesifikasi teknis yang ditetapkan.',
                'tujuan_grade' => 'Memastikan proses deployment dan integrasi sistem berjalan lancar, terstruktur, dan siap digunakan oleh pengguna akhir.',
                'ekspektasi_kompetensi' => 'Mampu melakukan instalasi, konfigurasi parameter, pengujian fungsi sistem, serta memberikan pelatihan awal kepada pengguna.',
                'career_path_requirements' => 'Persyaratan Promosi G3 ➔ G4: Skor Kriteria Implementasi ≥ 4, Problem Analysis ≥ 4, dan Regulasi/Kebijakan ≥ 3.',
                'level' => 3,
                'urutan_grade' => 3,
                'status_aktif' => true,
            ],
            [
                'kode_grade' => 'GRADE-4',
                'nama_grade' => 'Grade 4',
                'career_path' => 'Junior Consultant',
                'deskripsi_kompetensi' => 'Menunjukkan kemampuan dalam menganalisis kebutuhan dasar pengguna, menyusun dokumentasi teknis awal, dan memberikan masukan solutif terkait penerapan sistem.',
                'tujuan_grade' => 'Menghubungkan kebutuhan proses bisnis pengguna dengan spesifikasi fungsional sistem secara akurat.',
                'ekspektasi_kompetensi' => 'Memahami analisis bisnis dasar, mampu menyusun draf petunjuk penggunaan (user manual), serta memfasilitasi komunikasi teknis antara pengguna dan tim pengembang.',
                'career_path_requirements' => 'Persyaratan Promosi G4 ➔ G5: Skor Kriteria Implementasi ≥ 4, Problem Analysis ≥ 4, Regulasi ≥ 4, dan Komunikasi ≥ 4.',
                'level' => 4,
                'urutan_grade' => 4,
                'status_aktif' => true,
            ],
            [
                'kode_grade' => 'GRADE-5',
                'nama_grade' => 'Grade 5',
                'career_path' => 'Consultant',
                'deskripsi_kompetensi' => 'Menunjukkan kemampuan mengelola konsultasi implementasi secara mandiri, merumuskan solusi proses bisnis, dan mengawal kepuasan pemangku kepentingan.',
                'tujuan_grade' => 'Memastikan solusi IT yang diterapkan selaras dengan kebutuhan strategis dan operasional instansi pengguna.',
                'ekspektasi_kompetensi' => 'Menguasai perancangan skenario solusi, mendampingi proses perubahan (change management), dan memimpin diskusi analisis kebutuhan teknis.',
                'career_path_requirements' => 'Persyaratan Promosi G5 ➔ G6: Skor Kriteria Implementasi ≥ 4, Problem Analysis ≥ 4, Leadership ≥ 4, dan Regulasi ≥ 4.',
                'level' => 5,
                'urutan_grade' => 5,
                'status_aktif' => true,
            ],
            [
                'kode_grade' => 'GRADE-6',
                'nama_grade' => 'Grade 6',
                'career_path' => 'Senior Consultant Grade 1',
                'deskripsi_kompetensi' => 'Menunjukkan kemampuan memimpin perancangan arsitektur fungsional kompleks, mengelola ekspektasi pemangku kepentingan utama, serta membimbing tim konsultasi tingkat bawah.',
                'tujuan_grade' => 'Menjamin kualitas teknis dan strategis dari seluruh penyerahan (deliverables) konsultasi pada proyek berskala menengah-besar.',
                'ekspektasi_kompetensi' => 'Memahami tata kelola sistem informasi pemerintahan/instansi, mengendalikan kualifikasi kebutuhan kompleks, dan menyusun standar dokumentasi tim.',
                'career_path_requirements' => 'Persyaratan Promosi G6 ➔ G7: Skor Kriteria Problem Analysis ≥ 5, Leadership ≥ 4, Regulasi ≥ 4, dan Evaluasi Solusi ≥ 4.',
                'level' => 6,
                'urutan_grade' => 6,
                'status_aktif' => true,
            ],
            [
                'kode_grade' => 'GRADE-7',
                'nama_grade' => 'Grade 7',
                'career_path' => 'Senior Consultant Grade 2',
                'deskripsi_kompetensi' => 'Menunjukkan kemampuan mengendalikan portofolio konsultasi lintas modul, menyusun strategi mitigasi risiko sistemik, serta mendorong efisiensi proses digitalisasi secara menyeluruh.',
                'tujuan_grade' => 'Mengoptimalkan efektivitas integrasi antar sistem dan memastikan kepatuhan implementasi terhadap regulasi atau kebijakan yang berlaku.',
                'ekspektasi_kompetensi' => 'Menguasai analisis dampak perubahan kebijakan terhadap arsitektur aplikasi, memimpin validasi lintas sistem, dan mengarahkan strategi optimalisasi kinerja.',
                'career_path_requirements' => 'Persyaratan Promosi G7 ➔ G8: Skor Kriteria Leadership ≥ 5, Problem Analysis ≥ 5, Regulasi ≥ 5, dan Manajemen Integrasi ≥ 4.',
                'level' => 7,
                'urutan_grade' => 7,
                'status_aktif' => true,
            ],
            [
                'kode_grade' => 'GRADE-8',
                'nama_grade' => 'Grade 8',
                'career_path' => 'Lead Consultant',
                'deskripsi_kompetensi' => 'Menunjukkan kemampuan memimpin arah strategis implementasi teknologi, mengarahkan tim multidisiplin, serta menjadi pendorong utama transformasi digital instansi.',
                'tujuan_grade' => 'Mengarahkan visi penerapan teknologi informasi agar mampu memberikan nilai transformasi dan keberlanjutan bagi instansi/klien.',
                'ekspektasi_kompetensi' => 'Memahami metodologi transformasi digital jangka panjang, memimpin tim konsultan senior, dan menyusun strategi adopsi teknologi terbarukan.',
                'career_path_requirements' => 'Persyaratan Promosi G8 ➔ G9: Skor Kriteria Leadership ≥ 5, Strategic Transformation ≥ 5, Problem Analysis ≥ 5, dan Regulasi ≥ 5.',
                'level' => 8,
                'urutan_grade' => 8,
                'status_aktif' => true,
            ],
            [
                'kode_grade' => 'GRADE-9',
                'nama_grade' => 'Grade 9',
                'career_path' => 'Principal Consultant / Solution Architect',
                'deskripsi_kompetensi' => 'Menunjukkan penguasaan tertinggi dalam merancang cetak biru (blueprint) arsitektur solusi enterprise, menentukan arah kebijakan teknologi, dan memberikan penasihatan strategis tingkat tertinggi.',
                'tujuan_grade' => 'Memastikan seluruh ekosistem aplikasi dan teknologi terintegrasi secara aman, andal, berkelanjutan, dan adaptif terhadap perkembangan masa depan.',
                'ekspektasi_kompetensi' => 'Memerintah perancangan enterprise architecture, mengevaluasi efektivitas teknologi jangka panjang, serta menetapkan standar kualitas dan keamanan tertinggi organisasi.',
                'career_path_requirements' => 'Jenjang Otoritas Tertinggi (Subject Matter Expert & Enterprise Architect Principal). Mempertahankan skor rata-rata instansi ≥ 4.8.',
                'level' => 9,
                'urutan_grade' => 9,
                'status_aktif' => true,
            ],
        ];

        foreach ($grades as $grade) {
            KpiGrade::updateOrCreate(['kode_grade' => $grade['kode_grade']], $grade);
        }
    }
}
