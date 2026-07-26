@extends('layouts.app')

@section('title', 'Detail Lembar Appraisal KPI - ' . $appraisal->appraisal_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-file-earmark-check-fill me-2"></i>Lembar Appraisal KPI [{{ $appraisal->appraisal_number }}]</h4>
        <p class="text-muted small mb-0">Rincian evaluasi kinerja konsultan, interpretasi penilaian dinamis, dan ringkasan eksekutif.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('appraisals.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <a href="{{ route('appraisals.download-pdf', $appraisal->id) }}" class="btn btn-danger btn-sm px-3 shadow-sm fw-semibold">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download PDF Document
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Main Info & Approval Status -->
    <div class="col-md-8">
        <div class="card card-gov shadow-sm h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Informasi Pegawai & Evaluator</span>
                @if($appraisal->approval_status === 'approved')
                    <span class="badge bg-success fs-6"><i class="bi bi-check-circle-fill me-1"></i> Approved</span>
                @elseif($appraisal->approval_status === 'rejected')
                    <span class="badge bg-danger fs-6"><i class="bi bi-x-circle-fill me-1"></i> Rejected</span>
                @else
                    <span class="badge bg-warning text-dark fs-6"><i class="bi bi-hourglass-split me-1"></i> Submitted (Menunggu Approval)</span>
                @endif
            </div>
            <div class="card-body p-4">
                <table class="table table-borderless align-middle mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted fw-semibold" style="width: 25%;">No. Appraisal:</td>
                            <td><span class="badge bg-light text-dark border font-monospace fs-6">{{ $appraisal->appraisal_number }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Nama Pegawai:</td>
                            <td><strong class="text-dark fs-6">{{ $appraisal->user->name ?? 'User Unknown' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Grade Position:</td>
                            <td><span class="badge bg-primary fs-6">{{ $appraisal->grade->nama_grade ?? "Grade {$appraisal->user->grade_level}" }} {{ $appraisal->grade->career_path ? '(' . $appraisal->grade->career_path . ')' : '' }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Penilai (Evaluator):</td>
                            <td><i class="bi bi-person-circle text-primary me-1"></i> {{ $appraisal->evaluator->name ?? 'System Evaluator' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Tanggal Pembuatan:</td>
                            <td>{{ $appraisal->created_at->format('d F Y - H:i') }} WIB</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Final Score & Predicate Box -->
    <div class="col-md-4">
        <div class="card card-gov border-primary shadow-sm h-100 text-center">
            <div class="card-header bg-primary text-white">
                <span class="fw-bold"><i class="bi bi-award-fill me-2"></i>HASIL PENILAIAN KPI</span>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center">
                <span class="text-muted small fw-semibold text-uppercase d-block">Nilai Akhir Berbobot</span>
                <h1 class="display-3 fw-extrabold text-primary mb-1">{{ number_format($appraisal->total_score, 2) }}</h1>
                
                @if($appraisal->predicate === 'Sangat Baik')
                    <span class="badge bg-success px-4 py-2 fs-6 mt-2">{{ $appraisal->predicate }}</span>
                @elseif($appraisal->predicate === 'Baik')
                    <span class="badge bg-primary px-4 py-2 fs-6 mt-2">{{ $appraisal->predicate }}</span>
                @elseif($appraisal->predicate === 'Cukup')
                    <span class="badge bg-info text-dark px-4 py-2 fs-6 mt-2">{{ $appraisal->predicate }}</span>
                @elseif($appraisal->predicate === 'Kurang')
                    <span class="badge bg-warning text-dark px-4 py-2 fs-6 mt-2">{{ $appraisal->predicate }}</span>
                @else
                    <span class="badge bg-danger px-4 py-2 fs-6 mt-2">{{ $appraisal->predicate }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Card Ringkasan Eksekutif & Interpretasi Otomatis (Modul 2) -->
<div class="card card-gov border-primary shadow-sm mb-4">
    <div class="card-header bg-primary text-white font-bold">
        <i class="bi bi-journal-check me-2"></i>RINGKASAN EKSEKUTIF & EVALUASI KOMPETENSI
    </div>
    <div class="card-body p-4">
        <div class="mb-3 p-3 bg-light-subtle rounded border border-primary-subtle">
            <strong class="text-primary d-block mb-1"><i class="bi bi-chat-quote-fill me-1"></i> Paragraf Ringkasan Eksekutif Otomatis:</strong>
            <p class="fs-6 text-dark mb-0 fst-italic">"{{ $appraisal->executive_summary }}"</p>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="p-3 bg-success-subtle rounded border border-success">
                    <strong class="text-success d-block mb-1"><i class="bi bi-star-fill me-1"></i> Kompetensi Terkuat (Skor ≥ 4):</strong>
                    <span class="fw-semibold text-dark">{{ $appraisal->strongest_competency ?? '-' }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 bg-warning-subtle rounded border border-warning">
                    <strong class="text-warning-emphasis d-block mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Area Pengembangan (Skor ≤ 3):</strong>
                    <span class="fw-semibold text-dark">{{ $appraisal->weakest_competency ?? '-' }}</span>
                </div>
            </div>
        </div>

        @if($appraisal->evaluator_justification)
            <div class="p-3 bg-light rounded border">
                <strong class="text-dark d-block mb-1"><i class="bi bi-journal-text me-1 text-primary"></i> Catatan Penilai / Justifikasi Evaluator:</strong>
                <p class="small text-dark mb-0">{{ $appraisal->evaluator_justification }}</p>
            </div>
        @endif
    </div>
</div>

<!-- Recommendation Card -->
<div class="card card-gov border-info shadow-sm mb-4">
    <div class="card-header bg-info-subtle text-info-emphasis fw-bold">
        <i class="bi bi-lightbulb-fill me-2"></i>Rekomendasi Tindak Lanjut Otomatis (Management)
    </div>
    <div class="card-body p-3">
        <p class="mb-0 fw-semibold text-dark fs-6">{{ $appraisal->recommendation ?? 'Tidak ada rekomendasi khusus.' }}</p>
    </div>
</div>

<!-- Rincian 8 Kriteria Table Card (Renamed to Interpretasi Penilaian) -->
<div class="card card-gov shadow-sm mb-4">
    <div class="card-header bg-light">
        <span class="fw-bold text-dark"><i class="bi bi-table me-2 text-primary"></i>Rincian Skor & Interpretasi Penilaian 8 Kriteria</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-spreadsheet align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;">No</th>
                        <th style="width: 20%;">Kriteria Penilaian</th>
                        <th style="width: 10%; text-align: center;">Bobot (%)</th>
                        <th style="width: 10%; text-align: center;">Skor (1-5)</th>
                        <th style="width: 12%; text-align: center;">Nilai Konversi</th>
                        <th style="width: 13%; text-align: center;">Nilai Berbobot</th>
                        <th style="width: 30%;">Interpretasi Penilaian Dinamis</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appraisal->details as $idx => $dt)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                            <td class="fw-bold text-primary">{{ $dt->criteria->nama_kriteria ?? 'Kriteria' }}</td>
                            <td class="text-center fw-semibold">{{ number_format($dt->weight_percent, 2) }}%</td>
                            <td class="text-center"><span class="badge bg-secondary fs-6 px-3">{{ $dt->score_input }}</span></td>
                            <td class="text-center fw-bold text-dark">{{ number_format($dt->converted_value, 0) }}</td>
                            <td class="text-center fw-extrabold text-primary fs-6 bg-light-subtle">{{ number_format($dt->weighted_score, 2) }}</td>
                            <td class="small text-dark">{{ $dt->indicator_snapshot }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-secondary fw-extrabold">
                        <td colspan="5" class="text-end">TOTAL NILAI AKHIR BERBOBOT:</td>
                        <td class="text-center text-primary fs-5">{{ number_format($appraisal->total_score, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Approval Action Box for Management -->
@if(auth()->check() && (auth()->user()->isManagement() || auth()->user()->isSuperAdmin()))
    <div class="card card-gov border-success shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <span class="fw-bold"><i class="bi bi-check2-square me-2"></i>Form Persetujuan (Approval) Management</span>
        </div>
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <form action="{{ route('appraisals.approve', $appraisal->id) }}" method="POST" class="mb-3">
                        @csrf
                        <label for="approval_notes" class="form-label fw-bold text-dark">Catatan Persetujuan Evaluator / Pimpinan</label>
                        <textarea name="approval_notes" id="approval_notes" class="form-control mb-3" rows="2" placeholder="Tuliskan arahan atau catatan evaluasi untuk pegawai...">{{ old('approval_notes', $appraisal->approval_notes) }}</textarea>
                        
                        <button type="submit" class="btn btn-success fw-bold px-4">
                            <i class="bi bi-check-circle-fill me-1"></i> Setujui Penilaian (Approve)
                        </button>
                    </form>

                    <form action="{{ route('appraisals.reject', $appraisal->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger fw-bold px-4" onclick="return confirm('Apakah Anda yakin ingin menolak penilaian ini?');">
                            <i class="bi bi-x-circle-fill me-1"></i> Tolak (Reject)
                        </button>
                    </form>
                </div>
                <div class="col-md-5 border-start">
                    <small class="text-muted d-block mb-1">Catatan Persetujuan Terakhir:</small>
                    <div class="p-3 bg-light rounded border text-dark">
                        {{ $appraisal->approval_notes ?? 'Belum ada catatan persetujuan.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
