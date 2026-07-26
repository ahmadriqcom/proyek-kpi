@extends('layouts.app')

@section('title', 'Penilaian KPI Pegawai (Appraisals)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-file-earmark-check-fill me-2"></i>Penilaian Kinerja Pegawai Konsultan IT</h4>
        <p class="text-muted small mb-0">Modul resmi appraisal kinerja, evaluasi berkala, persetujuan Management, dan penetapan predikat.</p>
    </div>
    <div>
        <a href="{{ route('appraisals.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm fw-semibold">
            <i class="bi bi-plus-circle-fill me-1"></i> Buat Penilaian Pegawai Baru
        </a>
    </div>
</div>

<!-- Filter Card -->
<div class="card card-gov mb-4 shadow-sm">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('appraisals.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted mb-1">Cari Nama / No. Appraisal</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Status Persetujuan (Approval)</label>
                <select name="approval_status" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="submitted" {{ request('approval_status') === 'submitted' ? 'selected' : '' }}>Menunggu Approval (Submitted)</option>
                    <option value="approved" {{ request('approval_status') === 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
                    <option value="rejected" {{ request('approval_status') === 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end gap-1 mt-auto">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel-fill me-1"></i> Filter Data</button>
                <a href="{{ route('appraisals.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card card-gov shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-spreadsheet align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;">No</th>
                        <th style="width: 12%;">No. Appraisal</th>
                        <th style="width: 20%;">Nama Pegawai Konsultan</th>
                        <th style="width: 15%;">Grade Level</th>
                        <th style="width: 12%; text-align: center;">Nilai Akhir</th>
                        <th style="width: 15%;">Predikat</th>
                        <th style="width: 11%;">Status Approval</th>
                        <th style="width: 10%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appraisals as $index => $appraisal)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $appraisals->firstItem() + $index }}</td>
                            <td><span class="badge bg-light text-dark border font-monospace">{{ $appraisal->appraisal_number }}</span></td>
                            <td class="fw-bold text-primary">{{ $appraisal->user->name ?? 'User Unknown' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $appraisal->grade->nama_grade ?? "Grade {$appraisal->user->grade_level}" }}</span></td>
                            <td class="text-center fw-extrabold text-dark fs-6 bg-light-subtle">{{ number_format($appraisal->total_score, 2) }}</td>
                            <td>
                                @if($appraisal->predicate === 'Sangat Baik')
                                    <span class="badge bg-success px-2 py-1">{{ $appraisal->predicate }}</span>
                                @elseif($appraisal->predicate === 'Baik')
                                    <span class="badge bg-primary px-2 py-1">{{ $appraisal->predicate }}</span>
                                @elseif($appraisal->predicate === 'Cukup')
                                    <span class="badge bg-info text-dark px-2 py-1">{{ $appraisal->predicate }}</span>
                                @elseif($appraisal->predicate === 'Kurang')
                                    <span class="badge bg-warning text-dark px-2 py-1">{{ $appraisal->predicate }}</span>
                                @else
                                    <span class="badge bg-danger px-2 py-1">{{ $appraisal->predicate }}</span>
                                @endif
                            </td>
                            <td>
                                @if($appraisal->approval_status === 'approved')
                                    <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i> Approved</span>
                                @elseif($appraisal->approval_status === 'rejected')
                                    <span class="badge bg-danger"><i class="bi bi-x-circle-fill me-1"></i> Rejected</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> Submitted</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('appraisals.show', $appraisal->id) }}" class="btn btn-outline-primary" title="Lihat Detail Lembar Appraisal">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('appraisals.download-pdf', $appraisal->id) }}" class="btn btn-outline-danger" title="Download PDF Report">
                                        <i class="bi bi-file-pdf-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Belum ada data Penilaian KPI Pegawai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($appraisals->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $appraisals->links() }}
        </div>
    @endif
</div>
@endsection
