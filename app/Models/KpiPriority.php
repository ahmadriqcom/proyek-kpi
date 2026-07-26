<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiPriority extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'urgency_weight',
        'target_sla_days',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'urgency_weight' => 'decimal:2',
            'target_sla_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function reports(): HasMany
    {
        return $this->hasMany(KpiReport::class, 'kpi_priority_id');
    }
}
