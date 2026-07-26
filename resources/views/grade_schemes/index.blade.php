@extends('layouts.app')

@section('title', 'Master Skema Penilaian Grade Konsultan IT (Grade 1 - 9)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-sliders me-2"></i>Master Skema Penilaian Grade Konsultan IT (Grade 1 - 9)</h4>
        <p class="text-muted small mb-0">Pengaturan standar penilaian kinerja, interpretasi dinamis, dan matriks pengembangan karier (Career Path HR Matrix).</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('grade-schemes.export-pdf') }}" class="btn btn-outline-danger btn-sm px-3 fw-semibold">
            <i class="bi bi-file-pdf-fill me-1"></i> Cetak PDF Skema (Grade 1-9)
        </a>
        @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('grade_schemes', 'create')))
            <button type="button" class="btn btn-primary btn-sm px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#createGradeModal">
                <i class="bi bi-plus-circle-fill me-1"></i> Tambah Grade Baru
            </button>
        @endif
    </div>
</div>

<!-- Tabs Konfigurasi Master -->
<ul class="nav nav-tabs nav-tabs-gov mb-4" id="schemeTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold" id="grades-tab" data-bs-toggle="tab" data-bs-target="#grades" type="button" role="tab"><i class="bi bi-diagram-3-fill me-1"></i> Master Grade (Grade 1 s.d. 9)</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold" id="career-tab" data-bs-toggle="tab" data-bs-target="#career" type="button" role="tab"><i class="bi bi-signpost-split-fill me-1"></i> Career Path & Syarat Kenaikan Grade</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold" id="criterias-tab" data-bs-toggle="tab" data-bs-target="#criterias" type="button" role="tab"><i class="bi bi-list-check me-1"></i> Master Kriteria (8 Kriteria)</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold" id="levels-tab" data-bs-toggle="tab" data-bs-target="#levels" type="button" role="tab"><i class="bi bi-calculator me-1"></i> Konversi Skor (1-5 ➔ 20-100)</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold" id="predicates-tab" data-bs-toggle="tab" data-bs-target="#predicates" type="button" role="tab"><i class="bi bi-award me-1"></i> Predikat & Rekomendasi</button>
    </li>
</ul>

<div class="tab-content" id="schemeTabContent">
    <!-- Tab 1: Master Grade (1-9) -->
    <div class="tab-pane fade show active" id="grades" role="tabpanel">
        <div class="row g-4">
            @foreach($grades as $grade)
                <div class="col-md-6 col-lg-4">
                    <div class="card card-gov h-100 shadow-sm border-start border-4 border-primary">
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary fs-6">{{ $grade->kode_grade }}</span>
                                    <span class="badge bg-info text-dark border fw-bold"><i class="bi bi-briefcase-fill me-1"></i> {{ $grade->career_path ?? 'Level ' . $grade->level }}</span>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">{{ $grade->nama_grade }}</h5>
                                <p class="text-primary small fw-semibold mb-2"><i class="bi bi-award me-1"></i> {{ $grade->career_path ?? '-' }}</p>
                                
                                <div class="mb-3 p-3 bg-light rounded border">
                                    <small class="text-muted d-block fw-bold mb-1"><i class="bi bi-info-circle me-1"></i> Deskripsi Kompetensi:</small>
                                    <p class="small text-dark mb-2 text-truncate-2" title="{{ $grade->deskripsi_kompetensi }}">{{ Str::limit($grade->deskripsi_kompetensi ?? 'Menunjukkan kemampuan operasional sesuai jenjang.', 110) }}</p>

                                    @if($grade->career_path_requirements)
                                        <small class="text-success d-block fw-bold mb-1"><i class="bi bi-arrow-up-circle me-1"></i> Syarat Promosi:</small>
                                        <p class="small text-dark mb-0 text-truncate-2" title="{{ $grade->career_path_requirements }}">{{ Str::limit($grade->career_path_requirements, 90) }}</p>
                                    @endif
                                </div>

                                <div class="p-2 bg-success-subtle border border-success rounded mb-3 text-center">
                                    <small class="text-muted d-block">Total Bobot Configured:</small>
                                    <h6 class="fw-bold text-success mb-0"><i class="bi bi-check-circle-fill me-1"></i> {{ number_format($grade->weights->sum('weight_percent'), 2) }}% (Valid 100%)</h6>
                                </div>
                            </div>

                            <div class="d-flex flex-column gap-2">
                                <a href="{{ route('grade-schemes.show', $grade->id) }}" class="btn btn-outline-primary btn-sm w-100 fw-semibold">
                                    <i class="bi bi-gear-fill me-1"></i> Kelola Skema & Rubrik Skor
                                </a>
                                @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('grade_schemes', 'update')))
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-warning btn-sm w-50 fw-semibold" data-bs-toggle="modal" data-bs-target="#editGradeModal{{ $grade->id }}">
                                            <i class="bi bi-pencil-square me-1"></i> Ubah
                                        </button>
                                        @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('grade_schemes', 'delete')))
                                            <form action="{{ route('grade-schemes.destroy', $grade->id) }}" method="POST" class="w-50" onsubmit="return confirm('Yakin ingin menghapus Grade {{ $grade->nama_grade }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 fw-semibold">
                                                    <i class="bi bi-trash-fill me-1"></i> Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Ubah Grade -->
                @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('grade_schemes', 'update')))
                    <div class="modal fade" id="editGradeModal{{ $grade->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form action="{{ route('grade-schemes.update', $grade->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-2 text-warning"></i>Ubah Parameter [{{ $grade->kode_grade }} - {{ $grade->nama_grade }}]</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Kode Grade:</label>
                                                <input type="text" name="kode_grade" class="form-control" value="{{ old('kode_grade', $grade->kode_grade) }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Nama Grade:</label>
                                                <input type="text" name="nama_grade" class="form-control" value="{{ old('nama_grade', $grade->nama_grade) }}" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Career Path / Peran Jabatan:</label>
                                            <input type="text" name="career_path" class="form-control" value="{{ old('career_path', $grade->career_path) }}" placeholder="Contoh: IT Help Desk / Solution Architect">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Deskripsi Kompetensi:</label>
                                            <textarea name="deskripsi_kompetensi" class="form-control" rows="3">{{ old('deskripsi_kompetensi', $grade->deskripsi_kompetensi) }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Syarat Promosi Kenaikan Grade (Career Path Requirements):</label>
                                            <textarea name="career_path_requirements" class="form-control" rows="2">{{ old('career_path_requirements', $grade->career_path_requirements) }}</textarea>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Level:</label>
                                                <input type="number" name="level" class="form-control" value="{{ old('level', $grade->level) }}" min="1" max="10" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label fw-semibold">Urutan Tampilan:</label>
                                                <input type="number" name="urutan_grade" class="form-control" value="{{ old('urutan_grade', $grade->urutan_grade) }}" min="1" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Tab 2: Career Path & HR Development Matrix (Modul 4) -->
    <div class="tab-pane fade" id="career" role="tabpanel">
        <div class="card card-gov shadow-sm border-primary">
            <div class="card-header bg-primary text-white font-bold">
                <i class="bi bi-diagram-3-fill me-2"></i>VISUALISASI JALUR KARIER (CAREER PATH ROADMAP G1 ➔ G9)
            </div>
            <div class="card-body p-4">
                <div class="timeline-stepper">
                    @foreach($grades as $index => $g)
                        <div class="p-3 mb-3 bg-light rounded border border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-primary fs-6">{{ $g->kode_grade }}</span>
                                <strong class="text-primary">{{ $g->career_path ?? '-' }}</strong>
                            </div>
                            <h6 class="fw-bold text-dark mb-2">{{ $g->nama_grade }} - {{ $g->career_path ?? '' }}</h6>
                            <p class="small text-muted mb-2"><i class="bi bi-info-circle me-1"></i> {{ $g->deskripsi_kompetensi }}</p>
                            <div class="p-2 bg-success-subtle rounded text-success-emphasis small border border-success">
                                <strong><i class="bi bi-check2-circle me-1"></i> Syarat Promosi Ke Jenjang Berikutnya:</strong><br>
                                {{ $g->career_path_requirements ?? 'Memenuhi kualifikasi penilaian kinerja organisasi.' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 3: Master Kriteria -->
    <div class="tab-pane fade" id="criterias" role="tabpanel">
        <div class="card card-gov shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-spreadsheet align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 15%;">Kode</th>
                                <th style="width: 25%;">Nama Kriteria</th>
                                <th style="width: 40%;">Deskripsi Indikator Utama</th>
                                <th style="width: 15%; text-align: center;">Bobot Default</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($criterias as $idx => $crit)
                                <tr>
                                    <td class="text-center text-muted fw-bold">{{ $idx + 1 }}</td>
                                    <td><span class="badge bg-light text-dark border font-monospace">{{ $crit->kode_kriteria }}</span></td>
                                    <td class="fw-bold text-primary">{{ $crit->nama_kriteria }}</td>
                                    <td class="small text-muted">{{ $crit->deskripsi }}</td>
                                    <td class="text-center fw-bold text-dark">{{ number_format($crit->bobot_default, 2) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 4: Konversi Skor -->
    <div class="tab-pane fade" id="levels" role="tabpanel">
        <div class="card card-gov shadow-sm">
            <div class="card-body p-0">
                <table class="table table-spreadsheet align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 15%; text-align: center;">Skor Input (1 - 5)</th>
                            <th style="width: 35%;">Label Kualifikasi</th>
                            <th style="width: 25%; text-align: center;">Nilai Konversi Standar</th>
                            <th style="width: 25%;">Keterangan System</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($levels as $lvl)
                            <tr>
                                <td class="text-center fs-5 fw-bold"><span class="badge bg-secondary px-3 py-2">{{ $lvl->score }}</span></td>
                                <td class="fw-bold text-dark fs-6">{{ $lvl->label }}</td>
                                <td class="text-center fw-extrabold text-primary fs-5">{{ number_format($lvl->converted_value, 0) }}</td>
                                <td class="small text-muted">Formula: (Bobot % × {{ number_format($lvl->converted_value, 0) }}) / 100</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 5: Predikat & Rekomendasi -->
    <div class="tab-pane fade" id="predicates" role="tabpanel">
        <div class="card card-gov shadow-sm">
            <div class="card-body p-0">
                <table class="table table-spreadsheet align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 20%;">Rentang Nilai Akhir</th>
                            <th style="width: 25%;">Predikat Kinerja</th>
                            <th style="width: 55%;">Rekomendasi Tindak Lanjut Otomatis (Management)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($predicates as $pred)
                            <tr>
                                <td class="fw-bold text-dark fs-6">{{ number_format($pred->min_score, 0) }} s.d {{ number_format($pred->max_score, 0) }}</td>
                                <td>
                                    @if($pred->predicate === 'Sangat Baik')
                                        <span class="badge bg-success px-3 py-2 fs-6">{{ $pred->predicate }}</span>
                                    @elseif($pred->predicate === 'Baik')
                                        <span class="badge bg-primary px-3 py-2 fs-6">{{ $pred->predicate }}</span>
                                    @elseif($pred->predicate === 'Cukup')
                                        <span class="badge bg-info text-dark px-3 py-2 fs-6">{{ $pred->predicate }}</span>
                                    @elseif($pred->predicate === 'Kurang')
                                        <span class="badge bg-warning text-dark px-3 py-2 fs-6">{{ $pred->predicate }}</span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2 fs-6">{{ $pred->predicate }}</span>
                                    @endif
                                </td>
                                <td class="fw-medium text-dark">{{ $pred->recommendation }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Grade Baru -->
@if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('grade_schemes', 'create')))
    <div class="modal fade" id="createGradeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('grade-schemes.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-dark"><i class="bi bi-plus-circle-fill me-2 text-primary"></i>Tambah Master Grade Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kode Grade (Misal: GRADE-10):</label>
                                <input type="text" name="kode_grade" class="form-control" placeholder="Contoh: GRADE-10" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Grade (Misal: Grade 10):</label>
                                <input type="text" name="nama_grade" class="form-control" placeholder="Contoh: Grade 10" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Career Path / Peran Jabatan:</label>
                            <input type="text" name="career_path" class="form-control" placeholder="Contoh: Chief Technology Architect">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi Kompetensi:</label>
                            <textarea name="deskripsi_kompetensi" class="form-control" rows="3" placeholder="Deskripsi kemampuan teknis dan manajerial..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Syarat Promosi Kenaikan Grade (Career Path Requirements):</label>
                            <textarea name="career_path_requirements" class="form-control" rows="2" placeholder="Contoh: Skor Kriteria Implementasi ≥ 4, Problem Analysis ≥ 4..."></textarea>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Level:</label>
                                <input type="number" name="level" class="form-control" value="{{ count($grades) + 1 }}" min="1" max="15" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Urutan Tampilan:</label>
                                <input type="number" name="urutan_grade" class="form-control" value="{{ count($grades) + 1 }}" min="1" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i> Simpan Grade Baru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
