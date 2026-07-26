<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKpiGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('grade_schemes', 'create'));
    }

    public function rules(): array
    {
        return [
            'kode_grade' => 'required|string|max:20|unique:kpi_grades,kode_grade',
            'nama_grade' => 'required|string|max:100',
            'career_path' => 'nullable|string|max:150',
            'deskripsi_kompetensi' => 'nullable|string|max:2000',
            'tujuan_grade' => 'nullable|string|max:2000',
            'ekspektasi_kompetensi' => 'nullable|string|max:2000',
            'level' => 'required|integer|min:1|max:10',
            'urutan_grade' => 'required|integer|min:1',
            'status_aktif' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'kode_grade.required' => 'Kode Grade wajib diisi.',
            'kode_grade.unique' => 'Kode Grade sudah digunakan.',
            'nama_grade.required' => 'Nama Grade wajib diisi.',
            'level.required' => 'Level Grade wajib diisi.',
            'urutan_grade.required' => 'Urutan Grade wajib diisi.',
        ];
    }
}
