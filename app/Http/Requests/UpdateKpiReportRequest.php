<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKpiReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('kpi_reports', 'update'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'application_id' => 'sometimes|exists:applications,id',
            'region_id' => 'sometimes|exists:regions,id',
            'menu' => 'sometimes|string|max:255',
            'start_date' => 'sometimes|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'category' => 'nullable|string',
            'priority' => 'nullable|string',
            'impact_level' => 'nullable|string',
            'kpi_category_id' => 'nullable|exists:kpi_categories,id',
            'kpi_priority_id' => 'nullable|exists:kpi_priorities,id',
            'kpi_impact_level_id' => 'nullable|exists:kpi_impact_levels,id',
            'problem' => 'sometimes|string',
            'solution' => 'nullable|string',
            'solution_log' => 'nullable|string',
            'remarks' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,png,jpg,jpeg,xlsx|max:5120',
            'status' => 'sometimes|in:pending,pending_approval,on_progress,completed,cancelled',
        ];
    }

    /**
     * Custom messages for validation errors in Indonesian.
     */
    public function messages(): array
    {
        return [
            'application_id.exists' => 'Aplikasi yang dipilih tidak valid.',
            'region_id.exists' => 'Daerah yang dipilih tidak valid.',
            'end_date.after_or_equal' => 'Tanggal Selesai tidak boleh lebih awal dari Tanggal Mulai.',
            'attachment.mimes' => 'Format file lampiran harus PDF, PNG, JPG, JPEG, atau XLSX.',
            'attachment.max' => 'Ukuran file lampiran maksimal 5MB.',
        ];
    }
}
