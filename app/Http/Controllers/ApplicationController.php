<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(): View
    {
        $applications = Application::orderBy('id', 'asc')->paginate(15);
        return view('applications.index', compact('applications'));
    }

    public function create(): View
    {
        return view('applications.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:applications,code',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ], [
            'code.required' => 'Kode aplikasi wajib diisi.',
            'code.unique' => 'Kode aplikasi ini sudah terdaftar.',
            'name.required' => 'Nama aplikasi wajib diisi.',
        ]);

        Application::create($validated);

        return redirect()->route('applications.index')
            ->with('success', "Master Aplikasi [{$validated['code']} - {$validated['name']}] berhasil ditambahkan oleh Superadmin.");
    }

    public function edit(int $id): View
    {
        $application = Application::findOrFail($id);
        return view('applications.edit', compact('application'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $application = Application::findOrFail($id);

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('applications')->ignore($application->id)],
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $application->update($validated);

        return redirect()->route('applications.index')
            ->with('success', "Master Aplikasi [{$application->code}] berhasil diperbarui.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $application = Application::findOrFail($id);
        $application->delete();

        return redirect()->route('applications.index')
            ->with('success', 'Master Aplikasi berhasil dihapus.');
    }
}
