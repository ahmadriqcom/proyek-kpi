@extends('layouts.app')

@section('title', 'Detail Laporan KPI - ' . $report->ticket_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-file-earmark-text-fill me-2"></i>Detail Laporan KPI [{{ $report->ticket_number }}]</h4>
        <p class="text-muted small mb-0">Rincian informasi kendala, status penanganan, dan rekam jejak riwayat solusi (audit trail).</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('kpi-reports.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Data Laporan
        </a>
        <a href="{{ route('kpi-reports.edit', $report->id) }}" class="btn btn-warning btn-sm px-3 shadow-sm">
            <i class="bi bi-pencil-square me-1"></i> Edit / Update Solusi
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Main Info Card -->
    <div class="col-md-8">
        <div class="card card-gov shadow-sm h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <span class="fw-bold text-secondary"><i class="bi bi-journal-text me-2"></i>Rincian Atribut Laporan</span>
                <div class="d-flex gap-2 align-items-center">
                    {!! $report->data_origin_badge !!}
                    {!! $report->priority_badge !!}
                    {!! $report->sla_badge !!}
                </div>
            </div>
            <div class="card-body p-4">
                <table class="table table-borderless align-middle mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted fw-semibold" style="width: 25%;">No. Registrasi:</td>
                            <td><span class="badge bg-light text-dark border font-monospace fs-6">{{ $report->ticket_number }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Nama Aplikasi & Daerah:</td>
                            <td><strong class="text-dark fs-6">{{ $report->app_region_label }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Klasifikasi Parameter:</td>
                            <td>
                                <div class="p-2 bg-light rounded border">
                                    <div class="mb-1">
                                        <span class="text-muted small">Kategori:</span> <strong class="text-primary">{{ $report->kpiCategory->name ?? $report->category ?? 'Technical / Bug System' }}</strong>
                                    </div>
                                    <div class="mb-1">
                                        <span class="text-muted small">Prioritas & SLA:</span> <strong class="text-warning">{{ $report->kpiPriority->name ?? $report->priority ?? 'Medium' }}</strong> (Target: {{ $report->kpiPriority->target_sla_days ?? 3 }} Hari)
                                    </div>
                                    <div>
                                        <span class="text-muted small">Tingkat Dampak:</span> <strong class="text-success">{{ $report->kpiImpactLevel->name ?? $report->impact_level ?? 'Unit Kerja' }}</strong>
                                    </div>
                                    @if($report->is_auto_interpreted)
                                        <div class="mt-2 text-info small fst-italic"><i class="bi bi-cpu me-1"></i> Ditentukan Otomatis oleh Auto-Interpretation Engine berdasarkan analisis kata kunci permasalahan.</div>
                                    @else
                                        <div class="mt-2 text-warning small fst-italic"><i class="bi bi-person-gear me-1"></i> Disesuaikan secara Manual oleh Management (Manual Override).</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Menu / Modul:</td>
                            <td><span class="badge bg-light text-dark border">{{ $report->menu }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Tgl Mulai:</td>
                            <td>{{ $report->start_date->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Tgl Selesai:</td>
                            <td>{{ $report->end_date ? $report->end_date->format('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold align-top">Permasalahan:</td>
                            <td>
                                <div class="p-3 bg-light rounded border text-dark">
                                    {{ $report->problem }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold align-top">Solusi:</td>
                            <td>
                                @if($report->solution)
                                    <div class="p-3 bg-success-subtle text-success border border-success-subtle rounded">
                                        <i class="bi bi-wrench-adjustable me-2"></i> {{ $report->solution }}
                                    </div>
                                @else
                                    <span class="text-muted italic"><i class="bi bi-clock me-1"></i> Belum ada tindakan penanganan awal</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Lampiran Bukti Pendukung:</td>
                            <td>
                                @if($report->attachment_path)
                                    <a href="{{ asset('storage/' . $report->attachment_path) }}" target="_blank" class="btn btn-outline-primary btn-sm px-3">
                                        <i class="bi bi-paperclip me-1"></i> Unduh Lampiran Berkas
                                    </a>
                                @else
                                    <span class="text-muted small"><i class="bi bi-dash-circle me-1"></i> Tidak ada berkas lampiran yang diunggah.</span>
                                @endif
                            </td>
                        </tr>
                        @if($report->approval_reason)
                            <tr>
                                <td class="text-muted fw-semibold text-warning">Alasan Penyesuaian Management:</td>
                                <td><span class="badge bg-warning-subtle text-dark border p-2">{{ $report->approval_reason }}</span></td>
                            </tr>
                        @endif
                        <tr>
                            <td class="text-muted fw-semibold">Keterangan:</td>
                            <td>{{ $report->remarks ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Metadata & Score Card -->
    <div class="col-md-4">
        <!-- Card Nilai Pekerjaan (Hanya Untuk Superadmin & Management) -->
        @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isManagement()))
            <div class="card card-gov border-primary shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-star-fill me-2"></i>NILAI PEKERJAAN (KPI SCORE)</span>
                    <button type="button" class="btn btn-warning btn-sm py-0 px-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#overrideModal">
                        <i class="bi bi-person-gear me-1"></i> Manual Override
                    </button>
                </div>
                <div class="card-body p-4 text-center">
                    <span class="text-muted small fw-semibold text-uppercase d-block">Nilai Akhir Pekerjaan Ini</span>
                    <h1 class="display-4 fw-extrabold text-primary mb-1">
                        {{ $report->score !== null ? number_format($report->score, 1) : '-' }}
                    </h1>
                    <small class="text-muted d-block">Asal Data: <strong>{{ $report->data_origin }}</strong></small>
                </div>
            </div>
        @endif

        <div class="card card-gov shadow-sm">
            <div class="card-header bg-light">
                <span class="fw-bold text-secondary"><i class="bi bi-person-badge me-2"></i>Informasi Operator Pembuat</span>
            </div>
            <div class="card-body p-3">
                <div class="mb-3">
                    <small class="text-muted d-block">Diinput/Diimpor Oleh Operator:</small>
                    <strong class="text-dark fs-6"><i class="bi bi-person-circle text-primary me-1"></i> {{ $report->creator->name ?? 'System' }}</strong>
                    <div class="small text-muted mt-1"><i class="bi bi-clock me-1"></i> {{ $report->created_at->format('d M Y H:i') }}</div>
                    @if($report->creator)
                        <span class="badge bg-light text-dark border mt-1">Grade {{ $report->creator->grade_level }} - {{ strtoupper($report->creator->role) }}</span>
                    @endif
                </div>

                @if($report->updater)
                    <div class="border-top pt-2 mt-2">
                        <small class="text-muted d-block">Diperbarui Terakhir Oleh:</small>
                        <strong class="text-dark"><i class="bi bi-person-check-fill me-1"></i> {{ $report->updater->name }}</strong>
                        <div class="small text-muted">{{ $report->updated_at->format('d M Y H:i') }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Manual Override Management -->
@if(auth()->check() && (auth()->user()->isManagement() || auth()->user()->isSuperAdmin()))
    <div class="modal fade" id="overrideModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('kpi-reports.approve-classification', $report->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold"><i class="bi bi-person-gear me-2"></i>Manual Override Parameter Scorer (Management & Superadmin)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-info py-2 px-3 mb-3 small">
                            <i class="bi bi-info-circle me-1"></i> Melakukan Manual Override akan mengubah status <code>data_origin</code> menjadi <strong>MANUAL_OVERRIDE</strong> dan langsung memperbarui Nilai KPI saat disimpan.
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="appr_category" class="form-label fw-semibold">Kategori Kendala:</label>
                                <select name="kpi_category_id" id="appr_category" class="form-select">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ $report->kpi_category_id == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }} (×{{ number_format($cat->complexity_weight, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="appr_priority" class="form-label fw-semibold">Tingkat Prioritas:</label>
                                <select name="kpi_priority_id" id="appr_priority" class="form-select">
                                    @foreach($priorities as $prio)
                                        <option value="{{ $prio->id }}" {{ $report->kpi_priority_id == $prio->id ? 'selected' : '' }}>
                                            {{ $prio->name }} (SLA: {{ $prio->target_sla_days }}d, ×{{ number_format($prio->urgency_weight, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="appr_impact" class="form-label fw-semibold">Tingkat Dampak:</label>
                                <select name="kpi_impact_level_id" id="appr_impact" class="form-select">
                                    @foreach($impacts as $imp)
                                        <option value="{{ $imp->id }}" {{ $report->kpi_impact_level_id == $imp->id ? 'selected' : '' }}>
                                            {{ $imp->name }} (×{{ number_format($imp->impact_weight, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="approval_reason" class="form-label fw-semibold">Alasan Penyesuaian / Manual Override <span class="text-danger">*</span></label>
                            <textarea name="approval_reason" id="approval_reason" rows="3" class="form-control" placeholder="Tuliskan alasan spesifik penyesuaian parameter..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning btn-sm fw-bold px-3"><i class="bi bi-save me-1"></i> Simpan Manual Override & Perbarui Nilai</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- History Timeline Card (Audit Trail) -->
<div class="card card-gov shadow-sm">
    <div class="card-header bg-light">
        <span class="fw-bold text-primary"><i class="bi bi-clock-history me-2"></i>Rekam Jejak Solusi & Audit Trail History</span>
    </div>
    <div class="card-body p-4">
        @if($report->histories->count() > 0)
            <div class="timeline ps-3">
                @foreach($report->histories as $history)
                    <div class="border-start border-2 border-primary ps-4 pb-4 position-relative">
                        <div class="position-absolute start-0 top-0 translate-middle bg-primary rounded-circle" style="width: 12px; height: 12px; margin-left: -1px;"></div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark"><i class="bi bi-person me-1"></i> {{ $history->user->name ?? 'Sistem' }}</span>
                            <small class="text-muted">{{ $history->created_at->format('d M Y - H:i:s') }}</small>
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-light text-dark border">Status: {{ strtoupper($history->previous_status) }} ➔ {{ strtoupper($history->new_status) }}</span>
                        </div>
                        <p class="mb-0 text-muted small bg-light p-2 rounded border">
                            {{ $history->solution_log ?? 'Pembaruan data laporan.' }}
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted mb-0"><i class="bi bi-info-circle me-1"></i> Belum ada catatan riwayat perbaikan.</p>
        @endif
    </div>
</div>
@endsection
