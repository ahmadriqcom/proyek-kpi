<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            'dashboard' => 'Dashboard Executive KPI',
            'kpi_reports' => 'Data Laporan KPI Mingguan',
            'appraisals' => 'Penilaian KPI Pegawai (Appraisal)',
            'grade_schemes' => 'Master Skema Penilaian Grade',
            'applications' => 'Master Aplikasi',
            'regions' => 'Master Daerah',
            'app_region_mappings' => 'Master Data Mapping Aplikasi & Daerah',
            'users' => 'Manajemen User & Hak Akses',
        ];

        $actions = [
            'read' => 'Lihat (Read)',
            'create' => 'Tambah (Create)',
            'update' => 'Ubah (Update)',
            'delete' => 'Hapus (Delete)',
            'print' => 'Cetak (Print / Export PDF/Excel)',
        ];

        foreach ($menus as $menuKey => $menuLabel) {
            foreach ($actions as $actionKey => $actionLabel) {
                Permission::firstOrCreate([
                    'name' => "{$menuKey}.{$actionKey}",
                ], [
                    'menu_key' => $menuKey,
                    'action_key' => $actionKey,
                    'label' => "{$actionLabel} - {$menuLabel}",
                ]);
            }
        }
    }
}
