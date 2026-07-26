@extends('layouts.app')

@section('title', 'Master Formula Perhitungan KPI & Kebijakan Rating')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-gear-wide-connected me-2"></i>Master Formula Perhitungan KPI & Kebijakan Rating</h4>
        <p class="text-muted small mb-0">Pengaturan saklar (toggle) parameter dinamis tanpa mengubah baris kode program.</p>
    </div>
</div>

<div class="card card-gov shadow-sm border-primary">
    <div class="card-header bg-primary text-white font-bold">
        <i class="bi bi-sliders me-2"></i>KONFIGURASI DYNAMIK MASTER FORMULA KPI (SUPERADMIN)
    </div>
    <div class="card-body p-4">
        <form action="{{ route('kpi-formula-configs.update') }}" method="POST">
            @csrf
            @method('PUT')

            <h6 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="bi bi-diagram-3 me-2"></i>1. Parameter Bobot Komposisi (Composite Weight Parameters)</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded border">
                        <div class="form-check form-switch mb-1">
                            <input class="form-check-input" type="checkbox" name="use_category_weight" id="use_category_weight" value="1" {{ $config->use_category_weight ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark" for="use_category_weight">Gunakan Bobot Kategori</label>
                        </div>
                        <small class="text-muted d-block">Memperhitungkan tingkat kompleksitas dari Kategori Kendala.</small>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 bg-light rounded border">
                        <div class="form-check form-switch mb-1">
                            <input class="form-check-input" type="checkbox" name="use_priority_weight" id="use_priority_weight" value="1" {{ $config->use_priority_weight ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark" for="use_priority_weight">Gunakan Bobot Prioritas</label>
                        </div>
                        <small class="text-muted d-block">Memperhitungkan tingkat urgensi dari Prioritas Penanganan.</small>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 bg-light rounded border">
                        <div class="form-check form-switch mb-1">
                            <input class="form-check-input" type="checkbox" name="use_impact_weight" id="use_impact_weight" value="1" {{ $config->use_impact_weight ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark" for="use_impact_weight">Gunakan Bobot Dampak</label>
                        </div>
                        <small class="text-muted d-block">Memperhitungkan luas jangkauan dampak terhadap organisasi.</small>
                    </div>
                </div>
            </div>

            <h6 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="bi bi-stopwatch me-2"></i>2. Aturan Bonus & Penalti Kepatuhan SLA</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded border">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="use_sla_bonus" id="use_sla_bonus" value="1" {{ $config->use_sla_bonus ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark" for="use_sla_bonus">Bonus Penyelesaian Sebelum / Tepat SLA</label>
                        </div>
                        <label class="form-label small fw-semibold">Jumlah Bonus Poin:</label>
                        <input type="number" step="0.5" name="sla_bonus_early" class="form-control form-control-sm" value="{{ old('sla_bonus_early', $config->sla_bonus_early) }}" min="0" max="50" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 bg-light rounded border">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="use_sla_penalty" id="use_sla_penalty" value="1" {{ $config->use_sla_penalty ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark" for="use_sla_penalty">Pengurangan Nilai (Penalty) Terlambat SLA</label>
                        </div>
                        <label class="form-label small fw-semibold">Penalti Poin per Hari Overdue × Urgensi:</label>
                        <input type="number" step="0.5" name="sla_penalty_per_day" class="form-control form-control-sm" value="{{ old('sla_penalty_per_day', $config->sla_penalty_per_day) }}" min="0" max="50" required>
                    </div>
                </div>
            </div>

            <h6 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="bi bi-shield-check me-2"></i>3. Kebijakan Batas Nilai Maksimum (Score Capping)</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded border">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="cap_max_score" id="cap_max_score" value="1" {{ $config->cap_max_score ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark" for="cap_max_score">Batasi Nilai KPI Maksimal (Default 100)</label>
                        </div>
                        <small class="text-muted d-block mb-2">Jika di-uncheck, nilai dapat melebihi 100 sebagai bonus pencapaian luar biasa.</small>
                        <label class="form-label small fw-semibold">Batas Ambang Maksimal (Cap Limit):</label>
                        <input type="number" step="1" name="max_score_cap" class="form-control form-control-sm" value="{{ old('max_score_cap', $config->max_score_cap) }}" min="50" max="200" required>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end border-top pt-3">
                <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="bi bi-save me-1"></i> Simpan Konfigurasi Formula Master</button>
            </div>
        </form>
    </div>
</div>
@endsection
