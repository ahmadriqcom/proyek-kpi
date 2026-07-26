<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('regions', 'create'));
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:regions,code',
            'name' => 'required|string|max:150',
            'province' => 'nullable|string|max:150',
            'uraian_provinsi' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode daerah wajib diisi.',
            'code.unique' => 'Kode daerah ini sudah terdaftar.',
            'name.required' => 'Nama daerah wajib diisi.',
        ];
    }
}
