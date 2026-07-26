<?php

namespace App\Http\Controllers;

use App\Models\KpiFormulaConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KpiFormulaConfigController extends Controller
{
    public function index(): View
    {
        $config = KpiFormulaConfig::getActiveConfig();
        return view('kpi_formula_configs.index', compact('config'));
    }

    public function update(Request $request): RedirectResponse
    {
        $config = KpiFormulaConfig::getActiveConfig();

        $validated = $request->validate([
            'sla_penalty_per_day' => 'required|numeric|min:0|max:100',
            'sla_bonus_early' => 'required|numeric|min:0|max:100',
            'max_score_cap' => 'required|numeric|min:50|max:200',
        ]);

        $config->update([
            'use_category_weight' => $request->has('use_category_weight'),
            'use_priority_weight' => $request->has('use_priority_weight'),
            'use_impact_weight' => $request->has('use_impact_weight'),
            'use_sla_penalty' => $request->has('use_sla_penalty'),
            'use_sla_bonus' => $request->has('use_sla_bonus'),
            'cap_max_score' => $request->has('cap_max_score'),
            'sla_penalty_per_day' => $request->input('sla_penalty_per_day'),
            'sla_bonus_early' => $request->input('sla_bonus_early'),
            'max_score_cap' => $request->input('max_score_cap'),
        ]);

        return redirect()->route('kpi-formula-configs.index')->with('success', 'Konfigurasi Master Formula Perhitungan KPI berhasil diperbarui.');
    }
}
