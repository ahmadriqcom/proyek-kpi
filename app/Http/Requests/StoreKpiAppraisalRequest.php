<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKpiAppraisalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('appraisals', 'create'));
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'scores' => 'required|array',
            'scores.*' => 'required|integer|between:1,5',
            'evaluator_justification' => 'required|string|min:100',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Pegawai yang dinilai wajib dipilih.',
            'scores.required' => 'Skor kriteria penilaian wajib diisi.',
            'evaluator_justification.required' => 'Catatan Penilai / Justifikasi Evaluator wajib diisi.',
            'evaluator_justification.min' => 'Catatan Penilai / Justifikasi Evaluator wajib berisi minimal 100 karakter untuk mencatat pertimbangan objektif.',
        ];
    }
}
