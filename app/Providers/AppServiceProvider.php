<?php

namespace App\Providers;

use App\Repositories\Contracts\KpiAppraisalRepositoryInterface;
use App\Repositories\Contracts\KpiGradeSchemeRepositoryInterface;
use App\Repositories\Contracts\KpiReportRepositoryInterface;
use App\Repositories\Eloquent\KpiAppraisalRepository;
use App\Repositories\Eloquent\KpiGradeSchemeRepository;
use App\Repositories\Eloquent\KpiReportRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(KpiReportRepositoryInterface::class, KpiReportRepository::class);
        $this->app->bind(KpiGradeSchemeRepositoryInterface::class, KpiGradeSchemeRepository::class);
        $this->app->bind(KpiAppraisalRepositoryInterface::class, KpiAppraisalRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Superadmin selalu mendapatkan bypass (akses penuh), role lainnya diperiksa via hasPermissionTo()
        Gate::before(function ($user, $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
            return $user->hasPermissionTo($ability) ? true : null;
        });
    }
}
