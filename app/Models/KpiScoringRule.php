<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiScoringRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_level',
        'grade_name',
        'target_sla_days',
        'base_score',
        'sla_penalty_per_day',
    ];

    protected function casts(): array
    {
        return [
            'grade_level' => 'integer',
            'target_sla_days' => 'integer',
            'base_score' => 'decimal:2',
            'sla_penalty_per_day' => 'decimal:2',
        ];
    }
}
