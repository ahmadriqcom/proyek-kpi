<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKpiAppRegionMappingRequest;
use App\Http\Requests\UpdateKpiAppRegionMappingRequest;
use App\Models\Application;
use App\Models\KpiAppRegionMapping;
use App\Models\Region;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class KpiAppRegionMappingController extends Controller
{
    /**
     * Tampilkan daftar Master Mapping Aplikasi & Daerah.
     */
    public function index(Request $request): View
    {
        $query = KpiAppRegionMapping::with(['application', 'region']);

        if ($request->filled('application_id')) {
            $query->where('application_id', $request->application_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('application', function ($appQ) use ($search) {
                    $appQ->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
                })->orWhereHas('region', function ($regQ) use ($search) {
                    $regQ->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
                });
            });
        }

        $mappings = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $applications = Application::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('app_region_mappings.index', compact('mappings', 'applications'));
    }

    /**
     * Form Tambah Mapping Baru.
     */
    public function create(): View
    {
        $applications = Application::where('is_active', true)->orderBy('name', 'asc')->get();
        $regions = Region::orderBy('name', 'asc')->get();

        return view('app_region_mappings.create', compact('applications', 'regions'));
    }

    /**
     * Simpan Relasi Mapping Baru.
     */
    public function store(StoreKpiAppRegionMappingRequest $request): RedirectResponse
    {
        try {
            $mapping = KpiAppRegionMapping::create($request->validated());

            return redirect()->route('app-region-mappings.index')
                ->with('success', 'Relasi Mapping Aplikasi & Daerah berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan relasi mapping: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Form Edit Mapping.
     */
    public function edit(int $id): View
    {
        $mapping = KpiAppRegionMapping::with(['application', 'region'])->findOrFail($id);
        $applications = Application::where('is_active', true)->orderBy('name', 'asc')->get();
        $regions = Region::orderBy('name', 'asc')->get();

        return view('app_region_mappings.edit', compact('mapping', 'applications', 'regions'));
    }

    /**
     * Update Relasi Mapping.
     */
    public function update(UpdateKpiAppRegionMappingRequest $request, int $id): RedirectResponse
    {
        try {
            $mapping = KpiAppRegionMapping::findOrFail($id);
            $mapping->update($request->validated());

            return redirect()->route('app-region-mappings.index')
                ->with('success', 'Relasi Mapping Aplikasi & Daerah berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui relasi mapping: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Hapus Relasi Mapping.
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $mapping = KpiAppRegionMapping::findOrFail($id);
            $mapping->delete();

            return redirect()->route('app-region-mappings.index')
                ->with('success', 'Relasi Mapping Aplikasi & Daerah berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus relasi mapping: ' . $e->getMessage());
        }
    }

    /**
     * Export PDF Ringkasan Mapping Aplikasi & Daerah.
     */
    public function exportPdf(): Response
    {
        $mappings = KpiAppRegionMapping::with(['application', 'region'])->orderBy('id', 'asc')->get();

        $pdf = Pdf::loadView('app_region_mappings.pdf', compact('mappings'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Mapping_Aplikasi_Daerah_' . date('Ymd_His') . '.pdf');
    }
}
