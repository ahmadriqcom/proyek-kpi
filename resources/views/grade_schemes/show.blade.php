@extends('layouts.app')

@section('title', 'Kelola Skema ' . $grade->nama_grade)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-sliders me-2"></i>Konfigurasi {{ $grade->nama_grade }} {{ $grade->career_path ? ' (' . $grade->career_path . ')' : '' }}</h4>
        <p class="text-muted small mb-0">Pengaturan bobot persentase 8 kriteria dan indikator rubrik skor 1 s.d 5.</p>
    </div>
    <a href="{{ route('grade-schemes.index') }}" class="btn btn-outline-secondary btn-sm px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Master Skema
    </a>
</div>

<!-- Header Card Profil Kompetensi Grade -->
<div class="card card-gov shadow-sm mb-4 border-start border-4 border-primary">
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary fs-6">{{ $grade->kode_grade }}</span>
                    <span class="badge bg-info text-dark border fw-bold fs-6"><i class="bi bi-briefcase-fill me-1"></i> {{ $grade->career_path ?? 'Level ' . $grade->level }}</span>
                </div>
                <h5 class="fw-bold text-dark mb-2">{{ $grade->nama_grade }} - {{ $grade->career_path ?? '' }}</h5>
                <p class="text-muted small mb-2"><strong>Deskripsi Kompetensi:</strong> {{ $grade->deskripsi_kompetensi ?? '-' }}</p>
            </div>
            <div class="col-md-6 border-start ps-md-4">
                <div class="mb-2">
                    <strong class="small text-primary d-block"><i class="bi bi-target me-1"></i> Tujuan Grade:</strong>
                    <p class="small text-dark mb-2">{{ $grade->tujuan_grade ?? '-' }}</p>
                </div>
                <div>
                    <strong class="small text-success d-block"><i class="bi bi-star-fill me-1"></i> Ekspektasi Kompetensi:</strong>
                    <p class="small text-dark mb-0">{{ $grade->ekspektasi_kompetensi ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Form Kelola Bobot (Khusus Superadmin) -->
    <div class="col-md-5">
        <div class="card card-gov shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Alokasi Bobot Kriteria (Wajib 100%)</span>
                @if(auth()->check() && auth()->user()->isSuperAdmin())
                    <span class="badge bg-danger">Akses Superadmin</span>
                @else
                    <span class="badge bg-secondary">Read-Only</span>
                @endif
            </div>
            <div class="card-body p-4">
                <form action="{{ route('grade-schemes.update-weights', $grade->id) }}" method="POST">
                    @csrf
                    
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kriteria Penilaian</th>
                                    <th style="width: 35%; text-align: center;">Bobot (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criterias as $crit)
                                    @php
                                        $currentWeight = $weights->has($crit->id) ? $weights[$crit->id]->weight_percent : $crit->bobot_default;
                                    @endphp
                                    <tr>
                                        <td class="fw-bold text-primary small">{{ $crit->nama_kriteria }}</td>
                                        <td>
                                            @if(auth()->check() && auth()->user()->isSuperAdmin())
                                                <div class="input-group input-group-sm">
                                                    <input type="number" step="0.01" min="0" max="100" name="weights[{{ $crit->id }}]" class="form-control text-center fw-bold weight-input" value="{{ $currentWeight }}" required>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            @else
                                                <span class="fw-bold text-dark d-block text-center">{{ number_format($currentWeight, 2) }}%</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-primary fw-bold">
                                <tr>
                                    <td class="text-end">TOTAL BOBOT:</td>
                                    <td class="text-center fs-6 text-primary" id="totalWeightDisplay">
                                        {{ number_format($weights->sum('weight_percent'), 2) }}%
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if(auth()->check() && auth()->user()->isSuperAdmin())
                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            <i class="bi bi-save-fill me-1"></i> Simpan Konfigurasi Bobot
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Rincian Indikator Rubrik Skor 1-5 per Kriteria (CRUD Indikator Superadmin) -->
    <div class="col-md-7">
        <div class="card card-gov shadow-sm h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-journal-check me-2 text-success"></i>Indikator Rubrik Penilaian (Skor 1 - 5)</span>
            </div>
            <div class="card-body p-4" style="max-height: 650px; overflow-y: auto;">
                <div class="accordion" id="rubricAccordion">
                    @foreach($criterias as $cIndex => $crit)
                        <div class="accordion-item mb-2 border">
                            <h2 class="accordion-header" id="heading{{ $crit->id }}">
                                <button class="accordion-button {{ $cIndex !== 0 ? 'collapsed' : '' }} fw-bold text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $crit->id }}">
                                    <i class="bi bi-check2-square me-2"></i> {{ $crit->nama_kriteria }} (Bobot: {{ number_format($weights->has($crit->id) ? $weights[$crit->id]->weight_percent : 0, 2) }}%)
                                </button>
                            </h2>
                            <div id="collapse{{ $crit->id }}" class="accordion-collapse collapse {{ $cIndex === 0 ? 'show' : '' }}" data-bs-parent="#rubricAccordion">
                                <div class="accordion-body p-3 bg-light-subtle">
                                    <ul class="list-group list-group-flush">
                                        @for($s = 1; $s <= 5; $s++)
                                            @php
                                                $sch = $schemes->where('kpi_criteria_id', $crit->id)->where('score', $s)->first();
                                            @endphp
                                            <li class="list-group-item bg-transparent py-2">
                                                <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                                    <span class="badge bg-secondary px-2 py-1">Skor {{ $s }}</span>
                                                    @if(auth()->check() && auth()->user()->isSuperAdmin())
                                                        <button class="btn btn-link btn-sm p-0 text-primary fw-semibold" data-bs-toggle="modal" data-bs-target="#editModal{{ $crit->id }}_{{ $s }}">
                                                            <i class="bi bi-pencil-square me-1"></i> Edit Rubrik
                                                        </button>
                                                    @endif
                                                </div>
                                                <p class="small text-dark mb-0">{{ $sch->indicator_description ?? "Belum ada deskripsi indikator skor {$s}." }}</p>
                                            </li>

                                            <!-- Modal Edit Indikator (Superadmin) -->
                                            @if(auth()->check() && auth()->user()->isSuperAdmin())
                                                <div class="modal fade" id="editModal{{ $crit->id }}_{{ $s }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('grade-schemes.store-indicator', $grade->id) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="kpi_criteria_id" value="{{ $crit->id }}">
                                                                <input type="hidden" name="score" value="{{ $s }}">
                                                                <div class="modal-header bg-light">
                                                                    <h6 class="modal-title fw-bold">Edit Indikator Rubrik [{{ $crit->nama_kriteria }} - Skor {{ $s }}]</h6>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <label class="form-label fw-semibold">Teks Indikator Rubrik Penilaian:</label>
                                                                    <textarea name="indicator_description" class="form-control" rows="4" required>{{ $sch->indicator_description ?? '' }}</textarea>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i> Simpan Indikator</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endfor
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.weight-input').forEach(input => {
        input.addEventListener('input', function() {
            let total = 0;
            document.querySelectorAll('.weight-input').forEach(i => {
                total += parseFloat(i.value || 0);
            });
            const display = document.getElementById('totalWeightDisplay');
            display.innerText = total.toFixed(2) + '%';
            if (Math.abs(total - 100.00) < 0.01) {
                display.className = 'text-center fs-6 text-success fw-bold';
            } else {
                display.className = 'text-center fs-6 text-danger fw-bold';
            }
        });
    });
</script>
@endsection
