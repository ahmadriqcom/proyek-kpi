@extends('layouts.app')

@section('title', 'Edit Master Interpretasi Penilaian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-pencil-square me-2"></i>Edit Master Interpretasi Penilaian Dinamis</h4>
        <p class="text-muted small mb-0">Ubah narasi interpretasi, area pengembangan, dan rekomendasi otomatis.</p>
    </div>
    <a href="{{ route('score-interpretations.index') }}" class="btn btn-outline-secondary btn-sm px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-gov shadow-sm">
            <div class="card-header bg-light">
                <span class="fw-bold text-dark"><i class="bi bi-pencil-fill me-2 text-warning"></i>Form Edit Interpretasi</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('score-interpretations.update', $interpretation->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label for="kpi_grade_id" class="form-label fw-bold text-dark">1. Master Grade <span class="text-danger">*</span></label>
                            <select name="kpi_grade_id" id="kpi_grade_id" class="form-select @error('kpi_grade_id') is-invalid @enderror" required>
                                @foreach($grades as $g)
                                    <option value="{{ $g->id }}" {{ old('kpi_grade_id', $interpretation->kpi_grade_id) == $g->id ? 'selected' : '' }}>
                                        {{ $g->nama_grade }} - {{ $g->career_path ?? 'Level ' . $g->level }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kpi_grade_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-5">
                            <label for="kpi_criteria_id" class="form-label fw-bold text-dark">2. Kriteria Penilaian <span class="text-danger">*</span></label>
                            <select name="kpi_criteria_id" id="kpi_criteria_id" class="form-select @error('kpi_criteria_id') is-invalid @enderror" required>
                                @foreach($criterias as $c)
                                    <option value="{{ $c->id }}" {{ old('kpi_criteria_id', $interpretation->kpi_criteria_id) == $c->id ? 'selected' : '' }}>
                                        [{{ $c->kode_kriteria }}] {{ $c->nama_kriteria }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kpi_criteria_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-2">
                            <label for="score" class="form-label fw-bold text-dark">3. Skor <span class="text-danger">*</span></label>
                            <select name="score" id="score" class="form-select @error('score') is-invalid @enderror" required>
                                @for($s = 1; $s <= 5; $s++)
                                    <option value="{{ $s }}" {{ old('score', $interpretation->score) == $s ? 'selected' : '' }}>Skor {{ $s }}</option>
                                @endfor
                            </select>
                            @error('score') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="narasi_interpretasi" class="form-label fw-bold text-dark">Narasi Interpretasi Penilaian <span class="text-danger">*</span></label>
                        <textarea name="narasi_interpretasi" id="narasi_interpretasi" class="form-control @error('narasi_interpretasi') is-invalid @enderror" rows="3" required>{{ old('narasi_interpretasi', $interpretation->narasi_interpretasi) }}</textarea>
                        @error('narasi_interpretasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="area_pengembangan" class="form-label fw-bold text-dark">Area Pengembangan Kompetensi</label>
                        <textarea name="area_pengembangan" id="area_pengembangan" class="form-control @error('area_pengembangan') is-invalid @enderror" rows="2">{{ old('area_pengembangan', $interpretation->area_pengembangan) }}</textarea>
                        @error('area_pengembangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="rekomendasi_otomatis" class="form-label fw-bold text-dark">Rekomendasi Otomatis (Tindakan Konkret)</label>
                        <textarea name="rekomendasi_otomatis" id="rekomendasi_otomatis" class="form-control @error('rekomendasi_otomatis') is-invalid @enderror" rows="2">{{ old('rekomendasi_otomatis', $interpretation->rekomendasi_otomatis) }}</textarea>
                        @error('rekomendasi_otomatis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('score-interpretations.index') }}" class="btn btn-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-warning px-4 fw-semibold">
                            <i class="bi bi-save-fill me-1"></i> Perbarui Master Interpretasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
