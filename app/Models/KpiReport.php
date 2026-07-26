<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'application_id',
        'region_id',
        'app_region_label',
        'menu',
        'start_date',
        'end_date',
        'problem',
        'category',
        'priority',
        'impact_level',
        'kpi_category_id',
        'kpi_priority_id',
        'kpi_impact_level_id',
        'is_auto_interpreted',
        'data_origin',
        'solution',
        'remarks',
        'approval_reason',
        'attachment_path',
        'status',
        'sla_duration_days',
        'score',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'sla_duration_days' => 'integer',
            'score' => 'decimal:2',
            'is_auto_interpreted' => 'boolean',
        ];
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function kpiCategory(): BelongsTo
    {
        return $this->belongsTo(KpiCategory::class, 'kpi_category_id');
    }

    public function kpiPriority(): BelongsTo
    {
        return $this->belongsTo(KpiPriority::class, 'kpi_priority_id');
    }

    public function kpiImpactLevel(): BelongsTo
    {
        return $this->belongsTo(KpiImpactLevel::class, 'kpi_impact_level_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(KpiReportHistory::class)->latest();
    }

    public function getRunningSlaDaysAttribute(): int
    {
        if ($this->end_date) {
            return $this->sla_duration_days ?? $this->start_date->diffInDays($this->end_date);
        }
        return (int) $this->start_date->diffInDays(Carbon::now());
    }

    public function getSlaStatusAttribute(): string
    {
        $days = $this->running_sla_days;
        $targetSla = $this->kpiPriority->target_sla_days ?? 2;

        if ($this->status === 'completed') {
            return $days <= $targetSla ? 'On-Track' : 'Breached SLA';
        }

        if ($days < $targetSla) {
            return 'On-Track';
        } elseif ($days == $targetSla) {
            return 'Near SLA';
        }
        return 'Breached SLA';
    }

    public function getSlaBadgeAttribute(): string
    {
        return match ($this->sla_status) {
            'On-Track' => '<span class="badge bg-success"><i class="bi bi-shield-check me-1"></i> On-Track</span>',
            'Near SLA' => '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i> Near SLA</span>',
            default => '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Breached SLA</span>',
        };
    }

    public function getPriorityBadgeAttribute(): string
    {
        $prioName = $this->kpiPriority->name ?? $this->priority ?? 'Medium';
        return match ($prioName) {
            'Critical' => '<span class="badge bg-danger text-white"><i class="bi bi-lightning-fill me-1"></i> Critical</span>',
            'High' => '<span class="badge bg-warning text-dark"><i class="bi bi-arrow-up-circle-fill me-1"></i> High</span>',
            'Medium' => '<span class="badge bg-info text-dark"><i class="bi bi-dash-circle me-1"></i> Medium</span>',
            default => '<span class="badge bg-secondary"><i class="bi bi-arrow-down-circle me-1"></i> Low</span>',
        };
    }

    public function getDataOriginBadgeAttribute(): string
    {
        if ($this->data_origin === 'MANUAL_OVERRIDE' || !$this->is_auto_interpreted) {
            return '<span class="badge bg-warning text-dark border" title="Parameter disesuaikan manual oleh Management"><i class="bi bi-person-gear me-1"></i> MANUAL_OVERRIDE</span>';
        }
        return '<span class="badge bg-info-subtle text-info border" title="Parameter ditentukan otomatis oleh Auto-Interpretation Engine"><i class="bi bi-cpu me-1"></i> AUTO_INTERPRETED</span>';
    }
}
