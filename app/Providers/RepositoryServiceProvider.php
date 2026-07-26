<?php

namespace App\Providers;

use App\Repositories\Contracts\KpiReportRepositoryInterface;
use App\Repositories\Eloquent\KpiReportRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(KpiReportRepositoryInterface::class, KpiReportRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
