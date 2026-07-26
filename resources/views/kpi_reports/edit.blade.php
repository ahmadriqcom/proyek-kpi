@extends('layouts.app')

@section('title', 'Edit Solusi Laporan KPI - ' . $report->ticket_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-pencil-square me-2"></i>Perbarui Penanganan Kendala [{{ $report->ticket_number }}]</h4>
        <p class="text-muted small mb-0">Lakukan pembaruan solusi, penutupan laporan (Tgl Selesai), atau revisi catatan penanganan teknis.</p>
    </div>
    <a href="{{ route('kpi-reports.show', $report->id) }}" class="btn btn-outline-secondary btn-sm px-3">
        <i class="bi bi-eye me-1"></i> Detail Laporan
    </a>
</div>

<div class="card card-gov shadow-sm mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <span class="fw-bold text-secondary"><i class="bi bi-info-circle me-2"></i>Informasi Kendala</span>
        <span class="badge bg-primary font-monospace">{{ $report->ticket_number }}</span>
    </div>
    <div class="card-body bg-light-subtle">
        <div class="row">
            <div class="col-md-4 mb-2">
                <small class="text-muted d-block">Nama Aplikasi & Daerah:</small>
                <strong class="text-dark">{{ $report->app_region_label }}</strong>
            </div>
            <div class="col-md-4 mb-2">
                <small class="text-muted d-block">Menu / Modul Terkait:</small>
                <strong>{{ $report->menu }}</strong>
            </div>
            <div class="col-md-4 mb-2">
                <small class="text-muted d-block">Tanggal Mulai:</small>
                <strong>{{ $report->start_date->format('d F Y') }}</strong>
            </div>
            <div class="col-12 mt-2">
                <small class="text-muted d-block">Permasalahan yang Dihadapi:</small>
                <div class="p-3 bg-white border rounded text-dark">{{ $report->problem }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card card-gov shadow-sm">
    <div class="card-header bg-light">
        <span class="fw-bold text-primary"><i class="bi bi-wrench-adjustable me-2"></i>Form Pembaruan Solusi & Status</span>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('kpi-reports.update', $report->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label for="status" class="form-label fw-semibold">Status Penanganan <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="pending" {{ old('status', $report->status) === 'pending' ? 'selected' : '' }}>Dalam Proses (Pending)</option>
                        <option value="pending_approval" {{ old('status', $report->status) === 'pending_approval' ? 'selected' : '' }}>Pending Approval Management</option>
                        <option value="completed" {{ old('status', $report->status) === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                        <option value="cancelled" {{ old('status', $report->status) === 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="kpi_category_id" class="form-label fw-semibold">
                        Kategori Kendala 
                        <i class="bi bi-info-circle text-primary ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Pilih kategori kendala dari Master Data."></i>
                    </label>
                    <select name="kpi_category_id" id="kpi_category_id" class="form-select">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('kpi_category_id', $report->kpi_category_id) == $cat->id ? 'selected' : '' }} title="{{ $cat->description }}">
                                {{ $cat->name }} (×{{ number_format($cat->complexity_weight, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="kpi_priority_id" class="form-label fw-semibold">
                        Tingkat Prioritas 
                        <i class="bi bi-info-circle text-warning ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="High / Critical otomatis memerlukan persetujuan Management."></i>
                    </label>
                    <select name="kpi_priority_id" id="kpi_priority_id" class="form-select">
                        @foreach($priorities as $prio)
                            <option value="{{ $prio->id }}" {{ old('kpi_priority_id', $report->kpi_priority_id) == $prio->id ? 'selected' : '' }} title="{{ $prio->description }}">
                                {{ $prio->name }} (SLA: {{ $prio->target_sla_days }}d, ×{{ number_format($prio->urgency_weight, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="kpi_impact_level_id" class="form-label fw-semibold">
                        Tingkat Dampak 
                        <i class="bi bi-info-circle text-success ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Luas jangkauan dampak pada instansi."></i>
                    </label>
                    <select name="kpi_impact_level_id" id="kpi_impact_level_id" class="form-select">
                        @foreach($impacts as $imp)
                            <option value="{{ $imp->id }}" {{ old('kpi_impact_level_id', $report->kpi_impact_level_id) == $imp->id ? 'selected' : '' }} title="{{ $imp->description }}">
                                {{ $imp->name }} (×{{ number_format($imp->impact_weight, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="start_date" class="form-label fw-semibold">Tgl Mulai Penanganan</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $report->start_date->format('Y-m-d')) }}" required>
                </div>

                <div class="col-md-6">
                    <label for="end_date" class="form-label fw-semibold">Tgl Selesai Penanganan</label>
                    <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $report->end_date ? $report->end_date->format('Y-m-d') : '') }}">
                    <div class="form-text">Isi tanggal ini untuk menyelesaikan kendala & menghitung durasi SLA.</div>
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="solution" class="form-label fw-semibold">Langkah Solusi Penanganan Teknis</label>
                <textarea name="solution" id="solution" rows="4" class="form-control @error('solution') is-invalid @enderror" placeholder="Tuliskan tindakan perbaikan teknis, rincian instruksi, atau patch yang diterapkan...">{{ old('solution', $report->solution) }}</textarea>
                @error('solution')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 p-3 bg-light rounded border">
                <label for="attachment" class="form-label fw-semibold text-primary"><i class="bi bi-paperclip me-1"></i> Upload / Perbarui Lampiran (Opsional)</label>
                @if($report->attachment_path)
                    <div class="mb-2">
                        <small class="text-success fw-bold me-2"><i class="bi bi-file-earmark-check me-1"></i> Lampiran Terunggah Saat Ini:</small>
                        <a href="{{ asset('storage/' . $report->attachment_path) }}" target="_blank" class="btn btn-outline-primary btn-sm px-2 py-0"><i class="bi bi-download me-1"></i> Unduh Lampiran</a>
                    </div>
                @endif
                <input type="file" name="attachment" id="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".pdf,.png,.jpg,.jpeg,.xlsx">
                <small class="text-muted d-block mt-1">Format: PDF, PNG, JPG, XLSX (Max 5MB).</small>
            </div>

            <div class="mb-3">
                <label for="solution_log" class="form-label fw-semibold text-primary">Catatan Riwayat / Log Revisi (Audit Trail)</label>
                <input type="text" name="solution_log" id="solution_log" class="form-control" placeholder="Contoh: Perbaikan patch v2.1 telah diuji di staging oleh tim Helpdesk..." value="{{ old('solution_log') }}">
                <div class="form-text">Catatan ini akan disimpan secara permanen di riwayat rekam jejak solusi.</div>
            </div>

            <div class="mb-4">
                <label for="remarks" class="form-label fw-semibold">Keterangan Catatan Tambahan</label>
                <input type="text" name="remarks" id="remarks" class="form-control" value="{{ old('remarks', $report->remarks) }}" placeholder="Catatan atau arahan lanjutan ke klien/internal...">
            </div>

            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                <a href="{{ route('kpi-reports.show', $report->id) }}" class="btn btn-secondary px-4">Batal</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle me-1"></i> Simpan Perubahan Solusi</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
