<?php

namespace App\Http\Controllers;

use App\Models\KpiPriority;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KpiPriorityController extends Controller
{
    public function index(): View
    {
        $priorities = KpiPriority::orderBy('id', 'asc')->get();
        return view('kpi_priorities.index', compact('priorities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:kpi_priorities,name',
            'description' => 'nullable|string',
            'urgency_weight' => 'required|numeric|min:0.1|max:5.0',
            'target_sla_days' => 'required|integer|min:1|max:30',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        KpiPriority::create($validated);

        return redirect()->route('kpi-priorities.index')->with('success', 'Master Prioritas berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $priority = KpiPriority::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:kpi_priorities,name,' . $id,
            'description' => 'nullable|string',
            'urgency_weight' => 'required|numeric|min:0.1|max:5.0',
            'target_sla_days' => 'required|integer|min:1|max:30',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $priority->update($validated);

        return redirect()->route('kpi-priorities.index')->with('success', "Master Prioritas [{$priority->name}] berhasil diperbarui.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $priority = KpiPriority::findOrFail($id);
        $priority->delete();

        return redirect()->route('kpi-priorities.index')->with('success', 'Master Prioritas berhasil dihapus.');
    }
}
