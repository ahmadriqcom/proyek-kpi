<?php

namespace Tests\Feature;

use App\Exports\KpiReportExport;
use App\Models\KpiReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class KpiExcelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_export_kpi_reports_to_excel(): void
    {
        Excel::fake();

        $operator = User::factory()->create(['role' => 'operator']);
        $this->actingAs($operator);

        KpiReport::factory()->count(3)->create(['created_by' => $operator->id]);

        $response = $this->get(route('kpi-reports.export'));
        $response->assertStatus(200);

        Excel::assertDownloaded('Laporan_KPI_Mingguan_' . date('Ymd') . '.xlsx', function (KpiReportExport $export) {
            return count($export->sheets()) === 2;
        });
    }

    public function test_operator_management_and_superadmin_can_access_import_view(): void
    {
        $roles = ['operator', 'management', 'super_admin'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user);

            $response = $this->get(route('kpi-reports.import-view'));
            $response->assertStatus(200);
            $response->assertSee('Modul Impor Spreadsheet');
        }
    }
}
