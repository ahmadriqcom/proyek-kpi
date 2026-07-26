<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiScoreInterpretation extends Model
{
    use HasFactory;

    protected $fillable = [
        'kpi_grade_id',
        'kpi_criteria_id',
        'score',
        'narasi_interpretasi',
        'area_pengembangan',
        'rekomendasi_otomatis',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
        ];
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(KpiGrade::class, 'kpi_grade_id');
    }

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(KpiCriteria::class, 'kpi_criteria_id');
    }
}
