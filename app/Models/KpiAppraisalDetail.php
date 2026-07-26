<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiAppraisalDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'kpi_appraisal_id',
        'kpi_criteria_id',
        'weight_percent',
        'score_input',
        'converted_value',
        'weighted_score',
        'indicator_snapshot',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'weight_percent' => 'float',
            'score_input' => 'integer',
            'converted_value' => 'float',
            'weighted_score' => 'float',
        ];
    }

    public function appraisal(): BelongsTo
    {
        return $this->belongsTo(KpiAppraisal::class, 'kpi_appraisal_id');
    }

    public function criteria(): BelongsTo
    {
        return $this->belongsTo(KpiCriteria::class, 'kpi_criteria_id');
    }
}
