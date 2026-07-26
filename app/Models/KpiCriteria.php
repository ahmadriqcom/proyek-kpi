<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiCriteria extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_kriteria',
        'nama_kriteria',
        'deskripsi',
        'bobot_default',
        'status_aktif',
    ];

    protected function casts(): array
    {
        return [
            'bobot_default' => 'float',
            'status_aktif' => 'boolean',
        ];
    }

    public function weights(): HasMany
    {
        return $this->hasMany(KpiWeight::class);
    }
}
