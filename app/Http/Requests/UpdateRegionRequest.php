<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('regions', 'update'));
    }

    public function rules(): array
    {
        $regionId = $this->route('region') ?? $this->route('id');

        return [
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('regions', 'code')->ignore($regionId),
            ],
            'name' => 'required|string|max:150',
            'province' => 'nullable|string|max:150',
            'uraian_provinsi' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode daerah wajib diisi.',
            'code.unique' => 'Kode daerah ini sudah digunakan oleh daerah lain.',
            'name.required' => 'Nama daerah wajib diisi.',
        ];
    }
}
