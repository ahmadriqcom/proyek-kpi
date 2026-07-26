<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\KpiAppraisal;
use App\Models\KpiCategory;
use App\Models\KpiCriteria;
use App\Models\KpiFormulaConfig;
use App\Models\KpiGrade;
use App\Models\KpiImpactLevel;
use App\Models\KpiPriority;
use App\Models\KpiReport;
use App\Models\Permission;
use App\Models\Region;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KpiAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->seed(PermissionSeeder::class);
        $this->seed(\Database\Seeders\KpiScoreInterpretationSeeder::class);
        $this->seed(\Database\Seeders\KpiMasterScoringSeeder::class);
    }

    public function test_user_can_login_with_username_password_and_year(): void
    {
        $response = $this->post('/login', [
            'username' => 'superadmin',
            'password' => 'password',
            'year' => '2026',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals(2026, session('active_year'));
    }

    public function test_global_search_api_and_user_nik_creation(): void
    {
        $superadmin = User::where('username', 'superadmin')->first();
        $this->actingAs($superadmin);

        // 1. Create User with mandatory NIK (max 10 digits)
        $createUserResponse = $this->post(route('users.store'), [
            'name' => 'Operator Baru NIK Test',
            'username' => 'operator.niktest',
            'nik' => '998877',
            'email' => 'niktest@kpi.go.id',
            'password' => 'password',
            'role' => 'operator',
            'grade_level' => 2,
        ]);

        $createUserResponse->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'operator.niktest',
            'nik' => '998877',
        ]);

        // 2. Global Search API Endpoint
        $searchResponse = $this->get('/api/global-search?q=998877');
        $searchResponse->assertStatus(200);
        $searchResponse->assertJsonFragment([
            'type' => 'Pegawai / User',
            'title' => 'Operator Baru NIK Test (NIK: 998877)',
        ]);
    }

    public function test_auto_interpretation_engine_determines_parameters_and_allows_manual_override(): void
    {
        $operator = User::where('role', 'operator')->first();
        $this->actingAs($operator);

        $app = Application::first();
        $region = Region::first();

        // Operator creates report with problem containing "crash" & "api" -> Auto-Interpretation Engine triggers
        $response = $this->post(route('kpi-reports.store'), [
            'application_id' => $app->id,
            'region_id' => $region->id,
            'menu' => 'Modul Integrasi API',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-01',
            'problem' => 'Database server crash total dan gagal integrasi API',
        ]);

        $response->assertRedirect(route('kpi-reports.index'));
        $report = KpiReport::latest()->first();

        // Verify Auto-Interpretation
        $this->assertTrue($report->is_auto_interpreted);
        $this->assertEquals('AUTO_INTERPRETED', $report->data_origin);
        $this->assertEquals('Critical', $report->priority);
        $this->assertEquals('Integrasi Sistem', $report->category);

        // Management performs Manual Override
        $superadmin = User::where('username', 'superadmin')->first();
        $this->actingAs($superadmin);

        $newCat = KpiCategory::where('name', 'Technical / Bug System')->first();
        $newPrio = KpiPriority::where('name', 'Medium')->first();
        $newImp = KpiImpactLevel::where('name', 'Satu OPD')->first();

        $overrideResponse = $this->post(route('kpi-reports.approve-classification', $report->id), [
            'kpi_category_id' => $newCat->id,
            'kpi_priority_id' => $newPrio->id,
            'kpi_impact_level_id' => $newImp->id,
            'approval_reason' => 'Disesuaikan oleh Supervisor Management setelah verifikasi tim helpdesk',
        ]);

        $overrideResponse->assertRedirect(route('kpi-reports.show', $report->id));
        $report->refresh();

        $this->assertFalse($report->is_auto_interpreted);
        $this->assertEquals('MANUAL_OVERRIDE', $report->data_origin);
        $this->assertEquals('Technical / Bug System', $report->category);
        $this->assertEquals('Medium', $report->priority);
        $this->assertEquals('Satu OPD', $report->impact_level);
        $this->assertEquals('Disesuaikan oleh Supervisor Management setelah verifikasi tim helpdesk', $report->approval_reason);
    }

    public function test_superadmin_can_manage_master_scoring_parameters_and_formula_config(): void
    {
        $superadmin = User::where('username', 'superadmin')->first();
        $this->actingAs($superadmin);

        // 1. Kategori
        $catResponse = $this->get(route('kpi-categories.index'));
        $catResponse->assertStatus(200);

        // 2. Prioritas
        $prioResponse = $this->get(route('kpi-priorities.index'));
        $prioResponse->assertStatus(200);

        // 3. Impact
        $impResponse = $this->get(route('kpi-impact-levels.index'));
        $impResponse->assertStatus(200);

        // 4. Formula Config Update
        $configResponse = $this->put(route('kpi-formula-configs.update'), [
            'use_category_weight' => '1',
            'use_priority_weight' => '1',
            'use_impact_weight' => '1',
            'use_sla_penalty' => '1',
            'use_sla_bonus' => '1',
            'sla_penalty_per_day' => '5.00',
            'sla_bonus_early' => '10.00',
            'cap_max_score' => '1',
            'max_score_cap' => '100.00',
        ]);

        $configResponse->assertRedirect(route('kpi-formula-configs.index'));
        $this->assertDatabaseHas('kpi_formula_configs', [
            'sla_bonus_early' => 10.00,
        ]);
    }

    public function test_report_with_critical_priority_triggers_pending_approval_and_management_can_approve(): void
    {
        $operator = User::where('role', 'operator')->first();
        $this->actingAs($operator);

        $app = Application::first();
        $region = Region::first();
        $cat = KpiCategory::first();
        $prioCritical = KpiPriority::where('name', 'Critical')->first();
        $imp = KpiImpactLevel::first();

        // Create Critical Report
        $reportResponse = $this->post(route('kpi-reports.store'), [
            'application_id' => $app->id,
            'region_id' => $region->id,
            'menu' => 'Modul Darurat',
            'start_date' => '2026-07-01',
            'end_date' => '2026-07-01',
            'kpi_category_id' => $cat->id,
            'kpi_priority_id' => $prioCritical->id,
            'kpi_impact_level_id' => $imp->id,
            'problem' => 'Database server crash total',
        ]);

        $report = KpiReport::latest()->first();

        // Management Approve & Override
        $management = User::where('role', 'management')->first() ?? User::where('username', 'superadmin')->first();
        $this->actingAs($management);

        $approveResponse = $this->post(route('kpi-reports.approve-classification', $report->id), [
            'kpi_category_id' => $cat->id,
            'kpi_priority_id' => $prioCritical->id,
            'kpi_impact_level_id' => $imp->id,
            'approval_reason' => 'Disetujui oleh Tim Supervisor Management',
        ]);

        $approveResponse->assertRedirect(route('kpi-reports.show', $report->id));
        $report->refresh();
        $this->assertEquals('completed', $report->status);
        $this->assertEquals('Disetujui oleh Tim Supervisor Management', $report->approval_reason);
    }

    public function test_operator_can_create_kpi_report_with_category_priority_and_optional_file(): void
    {
        Storage::fake('public');

        $operator = User::where('role', 'operator')->first();
        $this->actingAs($operator);

        $app = Application::first();
        $region = Region::first();
        $file = UploadedFile::fake()->create('dokumen_bukti.pdf', 500, 'application/pdf');

        $response = $this->post(route('kpi-reports.store'), [
            'application_id' => $app->id,
            'region_id' => $region->id,
            'menu' => 'Modul Keuangan',
            'start_date' => '2026-07-01',
            'category' => 'Technical/Bug',
            'priority' => 'Medium',
            'impact_level' => 'Medium',
            'problem' => 'Gagal simpan data transaksi APBD',
            'attachment' => $file,
        ]);

        $response->assertRedirect(route('kpi-reports.index'));
        $this->assertDatabaseHas('kpi_reports', [
            'application_id' => $app->id,
            'region_id' => $region->id,
            'problem' => 'Gagal simpan data transaksi APBD',
        ]);
    }

    public function test_api_returns_mapped_regions_for_application(): void
    {
        $superadmin = User::where('username', 'superadmin')->first();
        $this->actingAs($superadmin);

        $app = Application::first();
        $response = $this->get(route('api.mapped-regions', $app->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['id', 'code', 'name']
        ]);
    }

    public function test_superadmin_can_access_app_region_mappings_and_export_pdf(): void
    {
        $superadmin = User::where('username', 'superadmin')->first();
        $this->actingAs($superadmin);

        $indexResponse = $this->get(route('app-region-mappings.index'));
        $indexResponse->assertStatus(200);

        $pdfResponse = $this->get(route('app-region-mappings.export-pdf'));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertHeader('content-type', 'application/pdf');
    }

    public function test_superadmin_can_access_score_interpretations(): void
    {
        $superadmin = User::where('username', 'superadmin')->first();
        $this->actingAs($superadmin);

        $indexResponse = $this->get(route('score-interpretations.index'));
        $indexResponse->assertStatus(200);
    }

    public function test_appraisal_requires_minimum_100_chars_justification_and_generates_summary(): void
    {
        $superadmin = User::where('username', 'superadmin')->first();
        $this->actingAs($superadmin);

        $criterias = KpiCriteria::all();
        $scores = [];
        foreach ($criterias as $crit) {
            $scores[$crit->id] = 4;
        }

        $shortJustificationResponse = $this->post(route('appraisals.store'), [
            'user_id' => $superadmin->id,
            'scores' => $scores,
            'evaluator_justification' => 'Terlalu pendek',
        ]);
        $shortJustificationResponse->assertSessionHasErrors('evaluator_justification');

        $validJustification = trim(str_repeat('Konsultan menunjukkan kinerja dan komitmen yang sangat tinggi dalam menyelesaikan kendala. ', 3));
        $validResponse = $this->post(route('appraisals.store'), [
            'user_id' => $superadmin->id,
            'scores' => $scores,
            'evaluator_justification' => $validJustification,
        ]);

        $validResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('kpi_appraisals', [
            'user_id' => $superadmin->id,
            'evaluator_justification' => $validJustification,
        ]);
    }

    public function test_superadmin_can_create_and_update_region_with_uraian_provinsi(): void
    {
        $superadmin = User::where('username', 'superadmin')->first();
        $this->actingAs($superadmin);

        $region = Region::create([
            'code' => 'TEST-001',
            'name' => 'Daerah Uji Coba',
            'province' => 'Provinsi Test',
            'uraian_provinsi' => 'Uraian rincian wilayah provinsi uji coba',
        ]);

        $updateResponse = $this->put(route('regions.update', $region->id), [
            'code' => 'TEST-001',
            'name' => 'Daerah Uji Coba Updated',
            'province' => 'Provinsi Test Updated',
            'uraian_provinsi' => 'Uraian provinsi berhasil diperbarui',
        ]);

        $updateResponse->assertRedirect(route('regions.index'));
        $this->assertDatabaseHas('regions', [
            'id' => $region->id,
            'name' => 'Daerah Uji Coba Updated',
            'uraian_provinsi' => 'Uraian provinsi berhasil diperbarui',
        ]);
    }

    public function test_unchecking_read_permission_hides_menu_and_triggers_403_forbidden(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $otherPerm = Permission::where('name', 'kpi_reports.read')->first();

        $operator->setRelation('permissions', collect([$otherPerm]));
        $this->actingAs($operator);

        $response = $this->get(route('appraisals.index'));
        $response->assertStatus(403);
    }

    public function test_granting_read_permission_allows_access(): void
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $readPerm = Permission::where('name', 'appraisals.read')->first();

        $operator->setRelation('permissions', collect([$readPerm]));
        $this->actingAs($operator);

        $response = $this->get(route('appraisals.index'));
        $response->assertStatus(200);
    }
}
