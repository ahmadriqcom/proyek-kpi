<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KpiAppraisalController;
use App\Http\Controllers\KpiGradeSchemeController;
use App\Http\Controllers\KpiReportController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckPermissionMiddleware;
use App\Http\Middleware\PreventOperatorMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use Illuminate\Support\Facades\Route;

// Autentikasi Khusus (Username, Password, Tahun)
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard KPI
    Route::get('/', [DashboardController::class, 'index'])
        ->middleware(CheckPermissionMiddleware::class . ':dashboard.read')
        ->name('dashboard');
    Route::get('dashboard/download-pdf', [DashboardController::class, 'downloadPdf'])
        ->middleware(CheckPermissionMiddleware::class . ':dashboard.print')
        ->name('dashboard.download-pdf');

    // API JSON Dynamic Dependent Dropdown & Global Search
    Route::get('api/applications/{id}/regions', [KpiReportController::class, 'getMappedRegions'])->name('api.mapped-regions');
    Route::get('api/global-search', [KpiReportController::class, 'globalSearch'])->name('api.global-search');

    // Fitur Ubah Password Akun (Semua Role)
    Route::get('change-password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('change-password', [PasswordController::class, 'update'])->name('password.update');

    // Laporan KPI Mingguan
    Route::get('kpi-reports/export', [KpiReportController::class, 'export'])
        ->middleware(CheckPermissionMiddleware::class . ':kpi_reports.print')
        ->name('kpi-reports.export');
    Route::get('kpi-reports/import-form', [KpiReportController::class, 'importView'])
        ->middleware(CheckPermissionMiddleware::class . ':kpi_reports.create')
        ->name('kpi-reports.import-view');
    Route::post('kpi-reports/import-preview', [KpiReportController::class, 'importPreview'])
        ->middleware(CheckPermissionMiddleware::class . ':kpi_reports.create')
        ->name('kpi-reports.import-preview');
    Route::post('kpi-reports/import', [KpiReportController::class, 'import'])
        ->middleware(CheckPermissionMiddleware::class . ':kpi_reports.create')
        ->name('kpi-reports.import');
    Route::post('kpi-reports/{id}/override-score', [KpiReportController::class, 'overrideScore'])
        ->middleware(SuperAdminMiddleware::class)
        ->name('kpi-reports.override-score');

    Route::get('kpi-reports', [KpiReportController::class, 'index'])
        ->middleware(CheckPermissionMiddleware::class . ':kpi_reports.read')
        ->name('kpi-reports.index');
    Route::get('kpi-reports/create', [KpiReportController::class, 'create'])
        ->middleware(CheckPermissionMiddleware::class . ':kpi_reports.create')
        ->name('kpi-reports.create');
    Route::post('kpi-reports', [KpiReportController::class, 'store'])
        ->middleware(CheckPermissionMiddleware::class . ':kpi_reports.create')
        ->name('kpi-reports.store');
    Route::get('kpi-reports/{kpi_report}', [KpiReportController::class, 'show'])
        ->middleware(CheckPermissionMiddleware::class . ':kpi_reports.read')
        ->name('kpi-reports.show');
    Route::get('kpi-reports/{kpi_report}/edit', [KpiReportController::class, 'edit'])
        ->middleware(CheckPermissionMiddleware::class . ':kpi_reports.update')
        ->name('kpi-reports.edit');
    Route::put('kpi-reports/{kpi_report}', [KpiReportController::class, 'update'])
        ->middleware(CheckPermissionMiddleware::class . ':kpi_reports.update')
        ->name('kpi-reports.update');
    Route::delete('kpi-reports/{kpi_report}', [KpiReportController::class, 'destroy'])
        ->middleware(CheckPermissionMiddleware::class . ':kpi_reports.delete')
        ->name('kpi-reports.destroy');

    // Modul Skema Penilaian Grade Konsultan IT
    Route::middleware([PreventOperatorMiddleware::class, CheckPermissionMiddleware::class . ':grade_schemes.read'])->group(function () {
        Route::get('grade-schemes/export-pdf', [KpiGradeSchemeController::class, 'exportPdf'])
            ->middleware(CheckPermissionMiddleware::class . ':grade_schemes.print')
            ->name('grade-schemes.export-pdf');
        Route::get('grade-schemes', [KpiGradeSchemeController::class, 'index'])->name('grade-schemes.index');
        Route::post('grade-schemes', [KpiGradeSchemeController::class, 'store'])
            ->middleware(SuperAdminMiddleware::class)
            ->name('grade-schemes.store');
        Route::get('grade-schemes/{id}', [KpiGradeSchemeController::class, 'show'])->name('grade-schemes.show');
        Route::put('grade-schemes/{id}', [KpiGradeSchemeController::class, 'update'])
            ->middleware(SuperAdminMiddleware::class)
            ->name('grade-schemes.update');
        Route::delete('grade-schemes/{id}', [KpiGradeSchemeController::class, 'destroy'])
            ->middleware(SuperAdminMiddleware::class)
            ->name('grade-schemes.destroy');
        Route::post('grade-schemes/{id}/weights', [KpiGradeSchemeController::class, 'updateWeights'])
            ->middleware(SuperAdminMiddleware::class)
            ->name('grade-schemes.update-weights');
        Route::post('grade-schemes/{id}/indicators', [KpiGradeSchemeController::class, 'storeIndicator'])
            ->middleware(SuperAdminMiddleware::class)
            ->name('grade-schemes.store-indicator');
        Route::delete('grade-schemes/indicators/{indicatorId}', [KpiGradeSchemeController::class, 'destroyIndicator'])
            ->middleware(SuperAdminMiddleware::class)
            ->name('grade-schemes.destroy-indicator');
    });

    // Modul Penilaian Pegawai (Appraisals)
    Route::get('appraisals/download-pdf/{id}', [KpiAppraisalController::class, 'downloadPdf'])
        ->middleware(CheckPermissionMiddleware::class . ':appraisals.print')
        ->name('appraisals.download-pdf');
    Route::post('appraisals/{id}/approve', [KpiAppraisalController::class, 'approve'])
        ->middleware(CheckPermissionMiddleware::class . ':appraisals.update')
        ->name('appraisals.approve');
    Route::post('appraisals/{id}/reject', [KpiAppraisalController::class, 'reject'])
        ->middleware(CheckPermissionMiddleware::class . ':appraisals.update')
        ->name('appraisals.reject');

    Route::get('appraisals', [KpiAppraisalController::class, 'index'])
        ->middleware(CheckPermissionMiddleware::class . ':appraisals.read')
        ->name('appraisals.index');
    Route::get('appraisals/create', [KpiAppraisalController::class, 'create'])
        ->middleware(CheckPermissionMiddleware::class . ':appraisals.create')
        ->name('appraisals.create');
    Route::post('appraisals', [KpiAppraisalController::class, 'store'])
        ->middleware(CheckPermissionMiddleware::class . ':appraisals.create')
        ->name('appraisals.store');
    Route::get('appraisals/{appraisal}', [KpiAppraisalController::class, 'show'])
        ->middleware(CheckPermissionMiddleware::class . ':appraisals.read')
        ->name('appraisals.show');

    // Management Approval Classification & Adjustment Workflow
    Route::post('kpi-reports/{id}/approve-classification', [KpiReportController::class, 'approveClassification'])
        ->middleware(CheckPermissionMiddleware::class . ':kpi_reports.update')
        ->name('kpi-reports.approve-classification');

    // Master Data & Manajemen User (EKSKLUSIF DIAKSES Superadmin)
    Route::middleware(SuperAdminMiddleware::class)->group(function () {
        Route::resource('kpi-categories', \App\Http\Controllers\KpiCategoryController::class);
        Route::resource('kpi-priorities', \App\Http\Controllers\KpiPriorityController::class);
        Route::resource('kpi-impact-levels', \App\Http\Controllers\KpiImpactLevelController::class);
        Route::get('kpi-formula-configs', [\App\Http\Controllers\KpiFormulaConfigController::class, 'index'])->name('kpi-formula-configs.index');
        Route::put('kpi-formula-configs', [\App\Http\Controllers\KpiFormulaConfigController::class, 'update'])->name('kpi-formula-configs.update');

        Route::resource('score-interpretations', \App\Http\Controllers\KpiScoreInterpretationController::class);
        Route::get('app-region-mappings/export-pdf', [\App\Http\Controllers\KpiAppRegionMappingController::class, 'exportPdf'])->name('app-region-mappings.export-pdf');
        Route::resource('app-region-mappings', \App\Http\Controllers\KpiAppRegionMappingController::class);
        Route::resource('applications', ApplicationController::class);
        Route::resource('regions', RegionController::class);
        Route::resource('users', UserController::class);
    });
});
