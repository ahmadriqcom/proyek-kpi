<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiPredicate extends Model
{
    use HasFactory;

    protected $fillable = [
        'min_score',
        'max_score',
        'predicate',
        'recommendation',
    ];

    protected function casts(): array
    {
        return [
            'min_score' => 'float',
            'max_score' => 'float',
        ];
    }
}
