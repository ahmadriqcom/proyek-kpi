<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\KpiReport;
use App\Models\KpiScoringRule;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KpiReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $operator;
    protected Application $applicationModel;
    protected Region $region;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed Scoring Rules
        KpiScoringRule::create([
            'grade_level' => 2,
            'grade_name' => 'Grade 2 - Support Specialist',
            'target_sla_days' => 2,
            'base_score' => 100.00,
            'sla_penalty_per_day' => 8.00,
        ]);

        $this->operator = User::factory()->create([
            'role' => 'operator',
            'grade_level' => 2,
        ]);
        $this->applicationModel = Application::factory()->create(['code' => 'BOP', 'name' => 'Bantuan Operasional']);
        $this->region = Region::factory()->create(['code' => 'BTM', 'name' => 'Kota Batam']);
    }

    public function test_operator_can_create_kpi_report_and_auto_bind_created_by(): void
    {
        $this->actingAs($this->operator);

        $response = $this->post(route('kpi-reports.store'), [
            'application_id' => $this->applicationModel->id,
            'region_id' => $this->region->id,
            'menu' => 'Daftar Notifikasi Transfer',
            'start_date' => '2026-07-01',
            'problem' => 'Gagal kirim data notifikasi transfer',
        ]);

        $response->assertRedirect(route('kpi-reports.index'));
        $this->assertDatabaseHas('kpi_reports', [
            'app_region_label' => 'BOP - Kota Batam',
            'menu' => 'Daftar Notifikasi Transfer',
            'status' => 'pending',
            'created_by' => $this->operator->id,
        ]);
    }

    public function test_updating_kpi_report_calculates_score_based_on_grade(): void
    {
        $this->actingAs($this->operator);

        $report = KpiReport::factory()->create([
            'application_id' => $this->applicationModel->id,
            'region_id' => $this->region->id,
            'start_date' => '2026-07-01',
            'end_date' => null,
            'status' => 'pending',
            'created_by' => $this->operator->id,
        ]);

        // Target SLA is 2 days. 2026-07-01 to 2026-07-05 is 4 days SLA. Overdue by 2 days.
        // Penalty = 2 days * 8.00 = 16.00. Score = 100 - 16 = 84.00
        $response = $this->put(route('kpi-reports.update', $report->id), [
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-05',
            'status' => 'completed',
            'solution' => 'Perbaikan skema database dan restart API Gateway',
            'solution_log' => 'Solusi telah diuji dan teratasi',
        ]);

        $response->assertRedirect(route('kpi-reports.show', $report->id));

        $this->assertDatabaseHas('kpi_reports', [
            'id' => $report->id,
            'status' => 'completed',
            'sla_duration_days' => 4,
            'score' => 80.00,
            'updated_by' => $this->operator->id,
        ]);
    }
}
