<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'score',
        'label',
        'converted_value',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'converted_value' => 'float',
        ];
    }
}
