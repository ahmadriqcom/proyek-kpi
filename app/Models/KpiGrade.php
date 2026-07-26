<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_grade',
        'nama_grade',
        'career_path',
        'deskripsi_kompetensi',
        'tujuan_grade',
        'ekspektasi_kompetensi',
        'career_path_requirements',
        'level',
        'urutan_grade',
        'status_aktif',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'urutan_grade' => 'integer',
            'status_aktif' => 'boolean',
        ];
    }

    public function weights(): HasMany
    {
        return $this->hasMany(KpiWeight::class);
    }

    public function schemes(): HasMany
    {
        return $this->hasMany(KpiGradeScheme::class);
    }

    public function scoreInterpretations(): HasMany
    {
        return $this->hasMany(KpiScoreInterpretation::class);
    }
}
