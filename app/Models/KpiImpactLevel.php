<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiImpactLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'impact_weight',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'impact_weight' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function reports(): HasMany
    {
        return $this->hasMany(KpiReport::class, 'kpi_impact_level_id');
    }
}
