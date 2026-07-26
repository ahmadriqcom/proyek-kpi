<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\KpiReport;
use App\Models\Region;
use App\Models\User;
use Illuminate\Database\Seeder;

class KpiReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $operatorA = User::where('email', 'operatora@kpi.go.id')->first() ?? User::first();
        $operatorB = User::where('email', 'operatorb@kpi.go.id')->first() ?? User::first();
        $bop = Application::where('code', 'BOP')->first();
        $rkas = Application::where('code', 'RKAS')->first();
        $batam = Region::where('code', 'BTM')->first();
        $jakarta = Region::where('code', 'JKT')->first();

        // Sample Data 1: BOP - Batam (Selesai - Operator A)
        if ($bop && $batam) {
            KpiReport::firstOrCreate(
                ['ticket_number' => 'KPI-202607-0001'],
                [
                    'application_id' => $bop->id,
                    'region_id' => $batam->id,
                    'app_region_label' => 'BOP - Batam',
                    'menu' => 'Daftar Notifikasi Transfer',
                    'start_date' => '2026-07-01',
                    'end_date' => '2026-07-03',
                    'problem' => 'Notifikasi transfer dana BOP Tahap 1 gagal terikirim ke rekening sekolah penerima di wilayah Batam Kota.',
                    'solution' => 'Melakukan restart service API Gateway Bank Kepri Riau dan menginisiasi ulang kirim ulang payload notifikasi.',
                    'remarks' => 'Seluruh 42 sekolah penerima sudah mengonfirmasi saldo masuk.',
                    'status' => 'completed',
                    'sla_duration_days' => 2,
                    'score' => 100.00,
                    'created_by' => $operatorA->id,
                    'updated_by' => $operatorA->id,
                ]
            );
        }

        // Sample Data 2: RKAS / RAPBS - Jakarta (Pending - Operator B)
        if ($rkas && $jakarta) {
            KpiReport::firstOrCreate(
                ['ticket_number' => 'KPI-202607-0002'],
                [
                    'application_id' => $rkas->id,
                    'region_id' => $jakarta->id,
                    'app_region_label' => 'RKAS / RAPBS - Jakarta',
                    'menu' => 'RKAS / RAPBS',
                    'start_date' => '2026-07-15',
                    'end_date' => null,
                    'problem' => 'Gagal validasi posting penganggaran barang dan jasa pada modul RKAS APBD perubahan.',
                    'solution' => 'Pemeriksaan struktur skema database anggaran dan perbaikan aturan validasi saldo.',
                    'remarks' => 'Dalam proses penanganan oleh tim Backend Developer.',
                    'status' => 'pending',
                    'sla_duration_days' => null,
                    'score' => 85.00,
                    'created_by' => $operatorB->id,
                    'updated_by' => null,
                ]
            );
        }

        // Additional generated sample reports assigned across operators
        foreach ([$operatorA, $operatorB] as $op) {
            KpiReport::factory()->count(8)->create([
                'created_by' => $op->id,
                'score' => rand(75, 100),
            ]);
        }
    }
}
