<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'province',
        'uraian_provinsi',
    ];

    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(Application::class, 'kpi_app_region_mappings', 'region_id', 'application_id');
    }
}
