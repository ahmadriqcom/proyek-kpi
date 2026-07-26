<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIndicatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('grade_schemes', 'update'));
    }

    public function rules(): array
    {
        return [
            'kpi_criteria_id' => 'required|exists:kpi_criterias,id',
            'score' => 'required|integer|min:1|max:5',
            'indicator_description' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'kpi_criteria_id.required' => 'Kriteria Penilaian wajib dipilih.',
            'score.required' => 'Skor (1-5) wajib dipilih.',
            'indicator_description.required' => 'Deskripsi Indikator Rubrik wajib diisi.',
        ];
    }
}
