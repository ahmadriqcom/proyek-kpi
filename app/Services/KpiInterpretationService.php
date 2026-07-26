<?php

namespace App\Services;

use App\Models\KpiCategory;
use App\Models\KpiImpactLevel;
use App\Models\KpiPriority;

class KpiInterpretationService
{
    /**
     * Interpretasi otomatis Kategori, Prioritas, dan Dampak berdasarkan teks problem dan menu.
     *
     * @param string $problem
     * @param string $menu
     * @return array
     */
    public function interpret(string $problem, string $menu = ''): array
    {
        $text = strtolower($problem . ' ' . $menu);

        // 1. Tentukan Prioritas
        $priorityName = $this->determinePriorityName($text);
        $priorityModel = KpiPriority::where('name', $priorityName)->first()
            ?? KpiPriority::where('name', 'Medium')->first()
            ?? KpiPriority::first();

        // 2. Tentukan Kategori
        $categoryName = $this->determineCategoryName($text);
        $categoryModel = KpiCategory::where('name', $categoryName)->first()
            ?? KpiCategory::where('name', 'Technical / Bug System')->first()
            ?? KpiCategory::first();

        // 3. Tentukan Dampak
        $impactName = $this->determineImpactName($text, $priorityName);
        $impactModel = KpiImpactLevel::where('name', $impactName)->first()
            ?? KpiImpactLevel::where('name', 'Unit Kerja')->first()
            ?? KpiImpactLevel::first();

        return [
            'kpi_category_id' => $categoryModel->id ?? null,
            'category' => $categoryModel->name ?? 'Technical / Bug System',
            'kpi_priority_id' => $priorityModel->id ?? null,
            'priority' => $priorityModel->name ?? 'Medium',
            'kpi_impact_level_id' => $impactModel->id ?? null,
            'impact_level' => $impactModel->name ?? 'Unit Kerja',
            'is_auto_interpreted' => true,
            'data_origin' => 'AUTO_INTERPRETED',
        ];
    }

    private function determinePriorityName(string $text): string
    {
        $criticalKeywords = ['crash', 'down', 'mati', 'gagal total', 'server error', '500 internal', 'error 500', 'darurat', 'emergency', 'database error', 'tidak bisa diakses', 'putus'];
        foreach ($criticalKeywords as $kw) {
            if (str_contains($text, $kw)) {
                return 'Critical';
            }
        }

        $highKeywords = ['gagal simpan', 'error', 'bug', 'lambat', 'gagal kirim', 'penyesuaian data', 'salah perhitungan', 'rkas', 'apbd', 'api', 'transaksi'];
        foreach ($highKeywords as $kw) {
            if (str_contains($text, $kw)) {
                return 'High';
            }
        }

        $lowKeywords = ['tanya', 'panduan', 'bantuan sederhana', 'lupa password', 'reset password'];
        foreach ($lowKeywords as $kw) {
            if (str_contains($text, $kw)) {
                return 'Low';
            }
        }

        return 'Medium';
    }

    private function determineCategoryName(string $text): string
    {
        if (str_contains($text, 'integrasi') || str_contains($text, 'api') || str_contains($text, 'web service') || str_contains($text, 'konektivitas') || str_contains($text, 'sync')) {
            return 'Integrasi Sistem';
        }

        if (str_contains($text, 'permintaan data') || str_contains($text, 'ekstraksi') || str_contains($text, 'kueri') || str_contains($text, 'query') || str_contains($text, 'rekapitulasi data')) {
            return 'Permintaan Data';
        }

        if (str_contains($text, 'penambahan') || str_contains($text, 'fitur baru') || str_contains($text, 'enhancement') || str_contains($text, 'modul baru')) {
            return 'Enhancement Feature';
        }

        if (str_contains($text, 'training') || str_contains($text, 'pelatihan') || str_contains($text, 'sosialisasi') || str_contains($text, 'workshop')) {
            return 'Training / Knowledge Sharing';
        }

        if (str_contains($text, 'konsultasi') || str_contains($text, 'pendampingan') || str_contains($text, 'bimbingan')) {
            return 'Konsultasi / Pendampingan';
        }

        return 'Technical / Bug System';
    }

    private function determineImpactName(string $text, string $priorityName): string
    {
        if ($priorityName === 'Critical' || str_contains($text, 'seluruh instansi') || str_contains($text, 'seluruh opd') || str_contains($text, 'seprovinsi') || str_contains($text, 'se-kabupaten')) {
            return 'Seluruh Instansi';
        }

        if (str_contains($text, 'lintas opd') || str_contains($text, 'integrasi') || str_contains($text, 'api')) {
            return 'Lintas OPD';
        }

        if (str_contains($text, 'satu opd') || str_contains($text, 'opd') || str_contains($text, 'dinas') || str_contains($text, 'badan')) {
            return 'Satu OPD';
        }

        if (str_contains($text, '1 orang') || str_contains($text, 'perorangan') || str_contains($text, 'lupa password')) {
            return 'Individual';
        }

        return 'Unit Kerja';
    }
}
