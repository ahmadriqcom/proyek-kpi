<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegionRequest;
use App\Http\Requests\UpdateRegionRequest;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegionController extends Controller
{
    public function index(): View
    {
        $regions = Region::orderBy('id', 'asc')->paginate(15);
        return view('regions.index', compact('regions'));
    }

    public function create(): View
    {
        return view('regions.create');
    }

    public function store(StoreRegionRequest $request): RedirectResponse
    {
        try {
            $region = Region::create($request->validated());

            return redirect()->route('regions.index')
                ->with('success', "Master Daerah [{$region->code} - {$region->name}] berhasil ditambahkan.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan data daerah: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(int $id): View
    {
        $region = Region::findOrFail($id);
        return view('regions.edit', compact('region'));
    }

    public function update(UpdateRegionRequest $request, int $id): RedirectResponse
    {
        try {
            $region = Region::findOrFail($id);
            $region->update($request->validated());

            return redirect()->route('regions.index')
                ->with('success', "Master Daerah [{$region->name}] berhasil diperbarui.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data daerah: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $region = Region::findOrFail($id);
            $region->delete();

            return redirect()->route('regions.index')
                ->with('success', 'Master Daerah berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data daerah: ' . $e->getMessage());
        }
    }
}
