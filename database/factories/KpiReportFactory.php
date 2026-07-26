<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\KpiReport;
use App\Models\Region;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KpiReport>
 */
class KpiReportFactory extends Factory
{
    protected $model = KpiReport::class;

    public function definition(): array
    {
        $app = Application::inRandomOrder()->first() ?? Application::factory()->create();
        $region = Region::inRandomOrder()->first() ?? Region::factory()->create();
        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        $startDate = Carbon::now()->subDays(rand(1, 30));
        $isCompleted = $this->faker->boolean(70);
        $endDate = $isCompleted ? (clone $startDate)->addDays(rand(0, 5)) : null;
        $status = $isCompleted ? 'completed' : 'pending';
        $sla = $endDate ? $startDate->diffInDays($endDate) : null;

        $menus = [
            'Daftar Notifikasi Transfer',
            'RKAS / RAPBS',
            'Modul Verifikasi Realisasi',
            'Pengajuan Pencairan Dana',
            'Laporan Pertanggungjawaban (LPJ)',
            'Master Data Sekolah & Siswa',
            'Sync Data Kemendikbud',
        ];

        $problems = [
            'Gagal melakukan kirim data notifikasi transfer bank daerah',
            'Pesan error 500 saat simpan rincian RKAS belanja pegawai',
            'Tampilan verifikasi realisasi tidak memuat data pencairan',
            'User lupa password dan email akun pengelola sekolah terblokir',
            'Laporan LPJ Triwulan 2 bernilai nihil saat diunduh PDF',
        ];

        $solutions = [
            'Pembersihan cache server & sinkronisasi ulang API Gateway bank',
            'Perbaikan query constraint pada modul RKAS dan patch update',
            'Melakukan reset token autentikasi web service pusat',
            'Prosedur reset credential user via helpdesk admin',
            'Re-generate ulang template PDF pada server laporan',
        ];

        return [
            'ticket_number' => 'KPI-' . date('Ym') . '-' . sprintf('%04d', $this->faker->unique()->numberBetween(100, 9999)),
            'application_id' => $app->id,
            'region_id' => $region->id,
            'app_region_label' => $app->code . ' - ' . $region->name,
            'menu' => $this->faker->randomElement($menus),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
            'problem' => $this->faker->randomElement($problems),
            'solution' => $isCompleted ? $this->faker->randomElement($solutions) : null,
            'remarks' => $isCompleted ? 'Penanganan selesai sesuai SLA' : 'Menunggu respon konfirmasi dari dinas terkait',
            'status' => $status,
            'sla_duration_days' => $sla,
            'created_by' => $user->id,
            'updated_by' => $isCompleted ? $user->id : null,
        ];
    }
}
