<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeWeightsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('grade_schemes', 'update'));
    }

    public function rules(): array
    {
        return [
            'weights' => 'required|array',
            'weights.*' => 'required|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'weights.required' => 'Data bobot wajib diisi.',
            'weights.*.numeric' => 'Nilai bobot harus berupa angka.',
            'weights.*.min' => 'Nilai bobot minimal 0%.',
            'weights.*.max' => 'Nilai bobot maksimal 100%.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $weights = $this->input('weights', []);
            $total = array_sum($weights);
            if (abs($total - 100.00) > 0.01) {
                $validator->errors()->add('weights', "Total persentase bobot adalah {$total}%. Wajib bernilai tepat 100%.");
            }
        });
    }
}
