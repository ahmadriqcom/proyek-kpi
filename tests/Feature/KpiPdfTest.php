<?php

namespace Tests\Feature;

use App\Models\KpiReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KpiPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_download_pdf_report_for_employee(): void
    {
        $operator = User::factory()->create(['role' => 'operator', 'grade_level' => 2]);
        $this->actingAs($operator);

        KpiReport::factory()->count(2)->create(['created_by' => $operator->id]);

        $response = $this->get(route('dashboard.download-pdf', ['user_id' => $operator->id]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
