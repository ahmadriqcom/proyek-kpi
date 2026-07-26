<?php

namespace App\Http\Controllers;

use App\Models\KpiCriteria;
use App\Models\KpiGrade;
use App\Models\KpiScoreInterpretation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KpiScoreInterpretationController extends Controller
{
    public function index(Request $request): View
    {
        $query = KpiScoreInterpretation::with(['grade', 'criteria'])->orderBy('kpi_grade_id', 'asc')->orderBy('kpi_criteria_id', 'asc')->orderBy('score', 'asc');

        if ($request->filled('grade_id')) {
            $query->where('kpi_grade_id', $request->input('grade_id'));
        }

        if ($request->filled('criteria_id')) {
            $query->where('kpi_criteria_id', $request->input('criteria_id'));
        }

        if ($request->filled('score')) {
            $query->where('score', $request->input('score'));
        }

        $interpretations = $query->paginate(20);
        $grades = KpiGrade::orderBy('level', 'asc')->get();
        $criterias = KpiCriteria::all();

        return view('score_interpretations.index', compact('interpretations', 'grades', 'criterias'));
    }

    public function create(): View
    {
        $grades = KpiGrade::orderBy('level', 'asc')->get();
        $criterias = KpiCriteria::all();

        return view('score_interpretations.create', compact('grades', 'criterias'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kpi_grade_id' => 'required|exists:kpi_grades,id',
            'kpi_criteria_id' => 'required|exists:kpi_criterias,id',
            'score' => 'required|integer|between:1,5',
            'narasi_interpretasi' => 'required|string|max:2000',
            'area_pengembangan' => 'nullable|string|max:2000',
            'rekomendasi_otomatis' => 'nullable|string|max:2000',
        ]);

        $exists = KpiScoreInterpretation::where('kpi_grade_id', $validated['kpi_grade_id'])
            ->where('kpi_criteria_id', $validated['kpi_criteria_id'])
            ->where('score', $validated['score'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Kombinasi Grade, Kriteria, dan Skor tersebut sudah memiliki Master Interpretasi.')
                ->withInput();
        }

        KpiScoreInterpretation::create($validated);

        return redirect()->route('score-interpretations.index')
            ->with('success', 'Master Interpretasi Penilaian Dinamis berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $interpretation = KpiScoreInterpretation::findOrFail($id);
        $grades = KpiGrade::orderBy('level', 'asc')->get();
        $criterias = KpiCriteria::all();

        return view('score_interpretations.edit', compact('interpretation', 'grades', 'criterias'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $interpretation = KpiScoreInterpretation::findOrFail($id);

        $validated = $request->validate([
            'kpi_grade_id' => 'required|exists:kpi_grades,id',
            'kpi_criteria_id' => 'required|exists:kpi_criterias,id',
            'score' => 'required|integer|between:1,5',
            'narasi_interpretasi' => 'required|string|max:2000',
            'area_pengembangan' => 'nullable|string|max:2000',
            'rekomendasi_otomatis' => 'nullable|string|max:2000',
        ]);

        $exists = KpiScoreInterpretation::where('kpi_grade_id', $validated['kpi_grade_id'])
            ->where('kpi_criteria_id', $validated['kpi_criteria_id'])
            ->where('score', $validated['score'])
            ->where('id', '!=', $interpretation->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Kombinasi Grade, Kriteria, dan Skor tersebut sudah memiliki Master Interpretasi lain.')
                ->withInput();
        }

        $interpretation->update($validated);

        return redirect()->route('score-interpretations.index')
            ->with('success', 'Master Interpretasi Penilaian Dinamis berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $interpretation = KpiScoreInterpretation::findOrFail($id);
        $interpretation->delete();

        return redirect()->route('score-interpretations.index')
            ->with('success', 'Master Interpretasi Penilaian berhasil dihapus.');
    }
}
