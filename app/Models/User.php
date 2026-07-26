<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'nik',
        'email',
        'password',
        'role',
        'grade_level',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'grade_level' => 'integer',
        ];
    }

    public function scoringRule(): BelongsTo
    {
        return $this->belongsTo(KpiScoringRule::class, 'grade_level', 'grade_level');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id');
    }

    public function hasPermissionTo(string $permissionName): bool
    {
        // Superadmin melepaskan seluruh pengecekan izin (Akses Penuh)
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Jika matriks izin khusus belum pernah diset untuk pengguna baru / factory, gunakan fallback standar role
        if ($this->permissions->isEmpty()) {
            if ($this->isOperator()) {
                $defaultOperatorPerms = [
                    'dashboard.read', 'dashboard.print',
                    'kpi_reports.read', 'kpi_reports.create', 'kpi_reports.update', 'kpi_reports.print',
                    'appraisals.read', 'appraisals.create', 'appraisals.print',
                ];
                return in_array($permissionName, $defaultOperatorPerms);
            }

            if ($this->isManagement()) {
                $defaultMgmtPerms = [
                    'dashboard.read', 'dashboard.print',
                    'kpi_reports.read', 'kpi_reports.create', 'kpi_reports.update', 'kpi_reports.print',
                    'appraisals.read', 'appraisals.create', 'appraisals.update', 'appraisals.print',
                    'grade_schemes.read',
                ];
                return in_array($permissionName, $defaultMgmtPerms);
            }
        }

        return $this->permissions->contains('name', $permissionName);
    }

    public function hasPermission(string $menuKey, string $actionKey = 'read'): bool
    {
        return $this->hasPermissionTo("{$menuKey}.{$actionKey}");
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function isManagement(): bool
    {
        return $this->role === 'management';
    }
}
