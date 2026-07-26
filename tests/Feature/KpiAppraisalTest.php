<?php

namespace Tests\Feature;

use App\Models\KpiCriteria;
use App\Models\KpiGrade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KpiAppraisalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->seed(\Database\Seeders\KpiScoreInterpretationSeeder::class);
    }

    public function test_can_calculate_appraisal_automatically_and_assign_predicate(): void
    {
        $operator = User::where('role', 'operator')->first();
        $this->actingAs($operator);

        $criterias = KpiCriteria::all();
        $scores = [];
        foreach ($criterias as $crit) {
            $scores[$crit->id] = 4; // Skor 4 (Nilai Konversi 80)
        }

        $justification = str_repeat('Catatan evaluasi kinerja objektif oleh penilai untuk pengujian sistem otomatis. ', 3);

        $response = $this->post(route('appraisals.store'), [
            'user_id' => $operator->id,
            'scores' => $scores,
            'evaluator_justification' => $justification,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('kpi_appraisals', [
            'user_id' => $operator->id,
            'total_score' => 80.00,
            'predicate' => 'Baik',
            'approval_status' => 'submitted',
        ]);
    }

    public function test_management_can_approve_appraisal(): void
    {
        $management = User::where('role', 'management')->first();
        $operator = User::where('role', 'operator')->first();
        $this->actingAs($operator);

        $criterias = KpiCriteria::all();
        $scores = [];
        foreach ($criterias as $crit) {
            $scores[$crit->id] = 5; // Skor 5 (Nilai Konversi 100)
        }

        $justification = str_repeat('Catatan evaluasi kinerja objektif oleh penilai untuk pengujian sistem otomatis. ', 3);

        $this->post(route('appraisals.store'), [
            'user_id' => $operator->id,
            'scores' => $scores,
            'evaluator_justification' => $justification,
        ]);

        $appraisal = \App\Models\KpiAppraisal::latest()->first();

        // Switch to Management
        $this->actingAs($management);
        $approveResponse = $this->post(route('appraisals.approve', $appraisal->id), [
            'approval_notes' => 'Disetujui oleh Management.',
        ]);

        $approveResponse->assertRedirect(route('appraisals.show', $appraisal->id));
        $this->assertDatabaseHas('kpi_appraisals', [
            'id' => $appraisal->id,
            'total_score' => 100.00,
            'predicate' => 'Sangat Baik',
            'approval_status' => 'approved',
            'evaluator_id' => $management->id,
        ]);
    }

    public function test_can_download_appraisal_pdf(): void
    {
        $management = User::where('role', 'management')->first();
        $operator = User::where('role', 'operator')->first();
        $this->actingAs($operator);

        $criterias = KpiCriteria::all();
        $scores = [];
        foreach ($criterias as $crit) {
            $scores[$crit->id] = 4;
        }

        $justification = str_repeat('Catatan evaluasi kinerja objektif oleh penilai untuk pengujian sistem otomatis. ', 3);

        $this->post(route('appraisals.store'), [
            'user_id' => $operator->id,
            'scores' => $scores,
            'evaluator_justification' => $justification,
        ]);

        $appraisal = \App\Models\KpiAppraisal::latest()->first();

        $this->actingAs($management);
        $pdfResponse = $this->get(route('appraisals.download-pdf', $appraisal->id));

        $pdfResponse->assertStatus(200);
        $pdfResponse->assertHeader('content-type', 'application/pdf');
    }
}
