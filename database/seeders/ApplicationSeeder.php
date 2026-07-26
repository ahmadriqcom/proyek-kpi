<?php

namespace Database\Seeders;

use App\Models\Application;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $apps = [
            ['name' => 'SIPBOS', 'description' => 'Sistem Informasi Pengelolaan Sistem Informasi BOSP'],
            ['name' => 'SIMPEL BOSP', 'description' => 'Aplikasi Pelaporan Bantuan Operasional Satuan Pendidikan'],
            ['name' => 'SIMAPATDA', 'description' => 'Sistem Manajemen Pendapatan Daerah'],
            ['name' => 'BEND ONLINE', 'description' => 'Aplikasi Bendahara Online Keuangan Daerah'],
            ['name' => 'E-RAPORT', 'description' => 'Sistem Informasi Rapor Elektronik Sekolah'],
            ['name' => 'ESIPUGA', 'description' => 'Sistem Informasi Pengolahan Gaji & Tunjangan'],
            ['name' => 'ESPPD / SIPADIN', 'description' => 'Sistem Informasi Surat Perintah Perjalanan Dinas'],
            ['name' => 'GAJI TPP', 'description' => 'Aplikasi Perhitungan Tambahan Penghasilan Pegawai'],
            ['name' => 'KAPITASI', 'description' => 'Aplikasi Pengelolaan Dana Kapitasi FKTP'],
            ['name' => 'KINERJA PKM', 'description' => 'Sistem Informasi Kinerja Puskesmas'],
            ['name' => 'SIREMUNDA', 'description' => 'Sistem Informasi Remunerasi Daerah'],
            ['name' => 'SIATER', 'description' => 'Sistem Informasi Terintegrasi'],
            ['name' => 'SILB', 'description' => 'Sistem Informasi Laporan Keuangan Daerah'],
            ['name' => 'SIMAKA BUMDES', 'description' => 'Sistem Informasi Akuntansi BUMDes'],
            ['name' => 'SIM GAJI DEWAN', 'description' => 'Aplikasi Penggajian DPRD'],
            ['name' => 'SIPBLUD', 'description' => 'Sistem Informasi Pengelolaan Keuangan BLUD'],
            ['name' => 'SISKEUDES LINK', 'description' => 'Aplikasi Integritas Siskeudes'],
            ['name' => 'WEBSITE DESA', 'description' => 'Portal Informasi & Pelayanan Publik Desa'],
            ['name' => 'ASB', 'description' => 'Aplikasi Analisis Standar Belanja'],
            ['name' => 'EVEHICLE', 'description' => 'Sistem Manajemen Kendaraan Dinas'],
            ['name' => 'SISKEUDES CLOUD', 'description' => 'Sistem Keuangan Desa Berbasis Cloud'],
            ['name' => 'SIMNAKER', 'description' => 'Sistem Informasi Manajemen Ketenagakerjaan'],
            ['name' => 'PEMANFAATAN ASET', 'description' => 'Sistem Pengelolaan & Pemanfaatan Barang Milik Daerah'],
            ['name' => 'TRANSFER DESA', 'description' => 'Sistem Informasi Transfer Dana Desa'],
            ['name' => 'REGISTER KEMENDAGRI', 'description' => 'Aplikasi Registrasi Berkas Kemendagri'],
        ];

        $officialNames = array_column($apps, 'name');
        Application::whereNotIn('name', $officialNames)->delete();

        foreach ($apps as $idx => $app) {
            $code = 'APP-' . str_pad($idx + 1, 2, '0', STR_PAD_LEFT);
            Application::updateOrCreate(
                ['name' => $app['name']],
                [
                    'code' => $code,
                    'description' => $app['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
