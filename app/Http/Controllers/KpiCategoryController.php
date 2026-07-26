<?php

namespace App\Http\Controllers;

use App\Models\KpiCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KpiCategoryController extends Controller
{
    public function index(): View
    {
        $categories = KpiCategory::orderBy('id', 'asc')->get();
        return view('kpi_categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:kpi_categories,name',
            'description' => 'nullable|string',
            'complexity_weight' => 'required|numeric|min:0.1|max:5.0',
            'requires_approval' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['requires_approval'] = $request->has('requires_approval');
        $validated['is_active'] = $request->has('is_active');

        KpiCategory::create($validated);

        return redirect()->route('kpi-categories.index')->with('success', 'Master Kategori Kendala berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $category = KpiCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:kpi_categories,name,' . $id,
            'description' => 'nullable|string',
            'complexity_weight' => 'required|numeric|min:0.1|max:5.0',
            'requires_approval' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['requires_approval'] = $request->has('requires_approval');
        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()->route('kpi-categories.index')->with('success', "Master Kategori [{$category->name}] berhasil diperbarui.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $category = KpiCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('kpi-categories.index')->with('success', 'Master Kategori Kendala berhasil dihapus.');
    }
}
