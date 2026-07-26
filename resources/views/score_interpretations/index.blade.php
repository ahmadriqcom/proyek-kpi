@extends('layouts.app')

@section('title', 'Master Data Interpretasi Penilaian Dinamis')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-journal-text me-2"></i>Master Data Interpretasi Penilaian Dinamis</h4>
        <p class="text-muted small mb-0">Matriks interpretasi otomatis $f(\text{Grade}, \text{Kriteria}, \text{Skor})$, area pengembangan, dan rekomendasi otomatis.</p>
    </div>
    <a href="{{ route('score-interpretations.create') }}" class="btn btn-primary btn-sm px-3 fw-semibold">
        <i class="bi bi-plus-circle-fill me-1"></i> Tambah Interpretasi Baru
    </a>
</div>

<!-- Filter Box -->
<div class="card card-gov shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('score-interpretations.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Filter Grade:</label>
                <select name="grade_id" class="form-select form-select-sm">
                    <option value="">-- Semua Grade (1 - 9) --</option>
                    @foreach($grades as $g)
                        <option value="{{ $g->id }}" {{ request('grade_id') == $g->id ? 'selected' : '' }}>
                            {{ $g->nama_grade }} ({{ $g->career_path ?? 'Level ' . $g->level }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Filter Kriteria:</label>
                <select name="criteria_id" class="form-select form-select-sm">
                    <option value="">-- Semua 8 Kriteria --</option>
                    @foreach($criterias as $c)
                        <option value="{{ $c->id }}" {{ request('criteria_id') == $c->id ? 'selected' : '' }}>
                            [{{ $c->kode_kriteria }}] {{ $c->nama_kriteria }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Skor (1 - 5):</label>
                <select name="score" class="form-select form-select-sm">
                    <option value="">-- Semua --</option>
                    @for($s = 1; $s <= 5; $s++)
                        <option value="{{ $s }}" {{ request('score') == $s ? 'selected' : '' }}>Skor {{ $s }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('score-interpretations.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table Data -->
<div class="card card-gov shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-spreadsheet align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">Master Grade</th>
                        <th style="width: 18%;">Kriteria Penilaian</th>
                        <th style="width: 8%; text-align: center;">Skor</th>
                        <th style="width: 27%;">Narasi Interpretasi Dinamis</th>
                        <th style="width: 17%;">Area Pengembangan</th>
                        <th style="width: 10%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($interpretations as $idx => $interp)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $interpretations->firstItem() + $idx }}</td>
                            <td>
                                <span class="badge bg-primary me-1">{{ $interp->grade->kode_grade ?? '-' }}</span>
                                <small class="fw-semibold d-block text-dark">{{ $interp->grade->career_path ?? '-' }}</small>
                            </td>
                            <td class="fw-bold text-primary small">
                                [{{ $interp->criteria->kode_kriteria ?? '-' }}] {{ $interp->criteria->nama_kriteria ?? '-' }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary px-2 py-1 fs-6">Skor {{ $interp->score }}</span>
                            </td>
                            <td class="small text-dark">{{ $interp->narasi_interpretasi }}</td>
                            <td class="small text-muted">{{ $interp->area_pengembangan ?? '-' }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('score-interpretations.edit', $interp->id) }}" class="btn btn-outline-warning">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('score-interpretations.destroy', $interp->id) }}" method="POST" onsubmit="return confirm('Hapus data interpretasi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i> Data Master Interpretasi belum tersedia atau tidak cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($interpretations->hasPages())
        <div class="card-footer bg-light p-3">
            {{ $interpretations->links() }}
        </div>
    @endif
</div>
@endsection
