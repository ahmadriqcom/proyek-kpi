<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'complexity_weight',
        'requires_approval',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'complexity_weight' => 'decimal:2',
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function reports(): HasMany
    {
        return $this->hasMany(KpiReport::class, 'kpi_category_id');
    }
}
