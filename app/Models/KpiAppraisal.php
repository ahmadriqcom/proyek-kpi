<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiAppraisal extends Model
{
    use HasFactory;

    protected $fillable = [
        'appraisal_number',
        'user_id',
        'kpi_grade_id',
        'evaluator_id',
        'total_score',
        'predicate',
        'recommendation',
        'approval_status',
        'approval_notes',
        'evaluator_justification',
        'strongest_competency',
        'weakest_competency',
        'executive_summary',
        'scheme_version',
    ];

    protected function casts(): array
    {
        return [
            'total_score' => 'float',
            'scheme_version' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(KpiGrade::class, 'kpi_grade_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(KpiAppraisalDetail::class, 'kpi_appraisal_id');
    }
}
