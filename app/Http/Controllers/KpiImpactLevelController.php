<?php

namespace App\Http\Controllers;

use App\Models\KpiImpactLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KpiImpactLevelController extends Controller
{
    public function index(): View
    {
        $impacts = KpiImpactLevel::orderBy('id', 'asc')->get();
        return view('kpi_impact_levels.index', compact('impacts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:kpi_impact_levels,name',
            'description' => 'nullable|string',
            'impact_weight' => 'required|numeric|min:0.1|max:5.0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        KpiImpactLevel::create($validated);

        return redirect()->route('kpi-impact-levels.index')->with('success', 'Master Tingkat Dampak berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $impact = KpiImpactLevel::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:kpi_impact_levels,name,' . $id,
            'description' => 'nullable|string',
            'impact_weight' => 'required|numeric|min:0.1|max:5.0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $impact->update($validated);

        return redirect()->route('kpi-impact-levels.index')->with('success', "Master Tingkat Dampak [{$impact->name}] berhasil diperbarui.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $impact = KpiImpactLevel::findOrFail($id);
        $impact->delete();

        return redirect()->route('kpi-impact-levels.index')->with('success', 'Master Tingkat Dampak berhasil dihapus.');
    }
}
