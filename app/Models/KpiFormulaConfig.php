<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiFormulaConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'use_category_weight',
        'use_priority_weight',
        'use_impact_weight',
        'use_sla_penalty',
        'use_sla_bonus',
        'sla_penalty_per_day',
        'sla_bonus_early',
        'cap_max_score',
        'max_score_cap',
    ];

    protected function casts(): array
    {
        return [
            'use_category_weight' => 'boolean',
            'use_priority_weight' => 'boolean',
            'use_impact_weight' => 'boolean',
            'use_sla_penalty' => 'boolean',
            'use_sla_bonus' => 'boolean',
            'sla_penalty_per_day' => 'decimal:2',
            'sla_bonus_early' => 'decimal:2',
            'cap_max_score' => 'boolean',
            'max_score_cap' => 'decimal:2',
        ];
    }

    public static function getActiveConfig(): self
    {
        return self::firstOrCreate([], [
            'use_category_weight' => true,
            'use_priority_weight' => true,
            'use_impact_weight' => true,
            'use_sla_penalty' => true,
            'use_sla_bonus' => true,
            'sla_penalty_per_day' => 10.00,
            'sla_bonus_early' => 5.00,
            'cap_max_score' => true,
            'max_score_cap' => 100.00,
        ]);
    }
}
