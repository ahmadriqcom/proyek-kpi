<?php

namespace App\Http\Requests;

use App\Models\KpiAppRegionMapping;
use Illuminate\Foundation\Http\FormRequest;

class UpdateKpiAppRegionMappingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('app_region_mappings', 'update'));
    }

    public function rules(): array
    {
        return [
            'application_id' => 'required|exists:applications,id',
            'region_id' => 'required|exists:regions,id',
        ];
    }

    public function messages(): array
    {
        return [
            'application_id.required' => 'Aplikasi wajib dipilih.',
            'application_id.exists' => 'Aplikasi tidak valid.',
            'region_id.required' => 'Daerah wajib dipilih.',
            'region_id.exists' => 'Daerah tidak valid.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $mappingId = $this->route('app_region_mapping') ?? $this->route('id');
            $appId = $this->input('application_id');
            $regId = $this->input('region_id');

            if ($appId && $regId) {
                $exists = KpiAppRegionMapping::where('application_id', $appId)
                    ->where('region_id', $regId)
                    ->where('id', '!=', $mappingId)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('region_id', 'Relasi mapping untuk Aplikasi dan Daerah ini sudah ada (Duplicate Entry).');
                }
            }
        });
    }
}
