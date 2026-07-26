@extends('layouts.app')

@section('title', 'Form Input Penilaian Pegawai KPI')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-file-earmark-plus-fill me-2"></i>Form Input Penilaian Pegawai (Appraisal)</h4>
        <p class="text-muted small mb-0">Input skor penilaian 1 s.d 5 untuk 8 kriteria standar berdasarkan Grade Pegawai & Justifikasi Evaluator.</p>
    </div>
    <a href="{{ route('appraisals.index') }}" class="btn btn-outline-secondary btn-sm px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Penilaian
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <!-- Card Panduan Real-Time Evaluator (Interactive Tooltip Guidance) -->
        <div class="card card-gov shadow-sm mb-4 border-start border-4 border-info">
            <div class="card-body p-3">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle-fill text-info me-2"></i>Panduan Skala Penilaian Evaluator (Skor 1 - 5):</h6>
                <div class="row g-2 text-center small">
                    <div class="col"><span class="badge bg-danger w-100 p-2">Skor 1: Perlu Pengembangan</span></div>
                    <div class="col"><span class="badge bg-warning text-dark w-100 p-2">Skor 2: Cukup</span></div>
                    <div class="col"><span class="badge bg-secondary w-100 p-2">Skor 3: Baik</span></div>
                    <div class="col"><span class="badge bg-primary w-100 p-2" title="Menunjukkan kemampuan memenuhi sebagian besar ekspektasi kompetensi pada Grade ini.">Skor 4: Sangat Baik</span></div>
                    <div class="col"><span class="badge bg-success w-100 p-2">Skor 5: Istimewa</span></div>
                </div>
            </div>
        </div>

        <div class="card card-gov shadow-sm mb-4">
            <div class="card-header bg-light">
                <span class="fw-bold text-dark"><i class="bi bi-person-badge me-2 text-primary"></i>Informasi Pegawai Konsultan IT & Grade</span>
            </div>
            <div class="card-body p-4">
                @if(auth()->check() && auth()->user()->isOperator())
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border">
                        <div>
                            <small class="text-muted d-block">Pegawai Yang Dinilai (Akun Login Anda):</small>
                            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-person-circle text-primary me-2"></i>{{ auth()->user()->name }}</h5>
                            <small class="text-muted">Username: @ {{ auth()->user()->username }} | Grade Level: {{ auth()->user()->grade_level }}</small>
                        </div>
                        <span class="badge bg-primary fs-6">{{ $grade->nama_grade }}</span>
                    </div>
                @else
                    <form method="GET" action="{{ route('appraisals.create') }}" class="row g-3 align-items-end mb-1">
                        <div class="col-md-8">
                            <label for="user_id" class="form-label fw-bold text-dark">Pilih Pegawai Konsultan IT <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="form-select fw-semibold" onchange="this.form.submit()">
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ $selectedUser->id == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->name }} (Grade {{ $emp->grade_level }} - {{ strtoupper($emp->role) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2 bg-light rounded border text-center">
                                <small class="text-muted d-block">Konfigurasi Grade Pegawai:</small>
                                <span class="badge bg-primary fs-6">{{ $grade->nama_grade }}</span>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <form action="{{ route('appraisals.store') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ auth()->check() && auth()->user()->isOperator() ? auth()->id() : $selectedUser->id }}">

            <div class="card card-gov shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-list-stars me-2"></i>ISIAN SKOR 8 KRITERIA PENILAIAN (SKALA 1 - 5)</span>
                    <span class="badge bg-light text-primary fw-bold">Formula Perhitungan Otomatis System</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%; text-align: center;">No</th>
                                    <th style="width: 20%;">Kriteria Penilaian</th>
                                    <th style="width: 10%; text-align: center;">Bobot (%)</th>
                                    <th style="width: 25%; text-align: center;">Pilihan Skor (1 s.d 5)</th>
                                    <th style="width: 15%; text-align: center;">Nilai Konversi (20-100)</th>
                                    <th style="width: 25%; text-align: center;">Nilai Kriteria Berbobot</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($weights as $idx => $w)
                                    @php
                                        $crit = $w->criteria;
                                    @endphp
                                    <tr>
                                        <td class="text-center fw-bold text-muted">{{ $idx + 1 }}</td>
                                        <td>
                                            <strong class="text-primary d-block">{{ $crit->nama_kriteria }}</strong>
                                            <small class="text-muted d-block" style="font-size: 8pt;">{{ $crit->deskripsi }}</small>
                                        </td>
                                        <td class="text-center fw-bold">{{ number_format($w->weight_percent, 2) }}%</td>
                                        <td class="text-center">
                                            <select name="scores[{{ $crit->id }}]" class="form-select form-select-sm fw-bold score-select" data-weight="{{ $w->weight_percent }}" data-target="val-{{ $crit->id }}" data-weighted="wscore-{{ $crit->id }}">
                                                <option value="5" title="Istimewa - Sangat Proaktif & Bebas Backlog">Skor 5 - Istimewa (100)</option>
                                                <option value="4" selected title="Menunjukkan kemampuan memenuhi sebagian besar ekspektasi kompetensi pada Grade ini.">Skor 4 - Sangat Baik (80)</option>
                                                <option value="3" title="Baik - Memenuhi standar dasar secara mandiri">Skor 3 - Baik (60)</option>
                                                <option value="2" title="Cukup - Memerlukan supervisi berkala">Skor 2 - Cukup (40)</option>
                                                <option value="1" title="Perlu Pengembangan - Memerlukan bimbingan rutin">Skor 1 - Perlu Pengembangan (20)</option>
                                            </select>
                                        </td>
                                        <td class="text-center fw-bold text-dark fs-6" id="val-{{ $crit->id }}">80</td>
                                        <td class="text-center fw-bold text-primary fs-6 bg-light-subtle" id="wscore-{{ $crit->id }}">
                                            {{ number_format(($w->weight_percent * 80) / 100, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-primary fw-extrabold">
                                <tr>
                                    <td colspan="5" class="text-end fs-6">TOTAL NILAI AKHIR APPRAISAL (0 - 100):</td>
                                    <td class="text-center fs-5 text-primary" id="grandTotalScore">80.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Mandatory Justifikasi Evaluator Card (Min 100 Karakter) -->
            <div class="card card-gov shadow-sm mb-4 border-start border-4 border-warning">
                <div class="card-header bg-light">
                    <span class="fw-bold text-dark"><i class="bi bi-journal-text me-2 text-warning"></i>Catatan Penilai / Justifikasi Evaluator <span class="text-danger">* (Wajib Minimal 100 Karakter)</span></span>
                </div>
                <div class="card-body p-4">
                    <div class="mb-2">
                        <textarea name="evaluator_justification" id="evaluator_justification" class="form-control @error('evaluator_justification') is-invalid @enderror" rows="4" placeholder="Tuliskan justifikasi objektif mengenai capaian kinerja, kendala riil di lapangan, dan pertimbangan kualitatif penilai (minimal 100 karakter)..." required>{{ old('evaluator_justification') }}</textarea>
                        @error('evaluator_justification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted" id="charCounter">Jumlah Karakter: 0 / 100 minimal</small>
                        <span id="charStatus" class="badge bg-danger">Kurang Karakter</span>
                    </div>
                </div>
            </div>

            <div class="card card-gov shadow-sm mb-4">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <span class="small text-muted"><i class="bi bi-shield-check text-success me-1"></i> Data dihitung otomatis & dikirim untuk approval Management</span>
                    <button type="submit" class="btn btn-success px-4 py-2 fw-bold" id="submitBtn">
                        <i class="bi bi-send-check-fill me-1"></i> Simpan & Kirim Penilaian KPI
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function calculateAppraisal() {
        let total = 0;
        document.querySelectorAll('.score-select').forEach(select => {
            const score = parseInt(select.value || 3);
            const weight = parseFloat(select.dataset.weight || 0);
            const converted = score * 20; // 20, 40, 60, 80, 100
            const weightedScore = (weight * converted) / 100;

            document.getElementById(select.dataset.target).innerText = converted;
            document.getElementById(select.dataset.weighted).innerText = weightedScore.toFixed(2);

            total += weightedScore;
        });

        document.getElementById('grandTotalScore').innerText = total.toFixed(2);
    }

    document.querySelectorAll('.score-select').forEach(select => {
        select.addEventListener('change', calculateAppraisal);
    });

    // Character counter untuk Justifikasi Evaluator (Min 100)
    const textarea = document.getElementById('evaluator_justification');
    const counter = document.getElementById('charCounter');
    const statusBadge = document.getElementById('charStatus');

    if (textarea) {
        textarea.addEventListener('input', function() {
            const len = this.value.length;
            counter.innerText = `Jumlah Karakter: ${len} / 100 minimal`;
            if (len >= 100) {
                statusBadge.className = 'badge bg-success';
                statusBadge.innerText = 'Persyaratan Terpenuhi';
            } else {
                statusBadge.className = 'badge bg-danger';
                statusBadge.innerText = `Memerlukan ${100 - len} karakter lagi`;
            }
        });
    }

    calculateAppraisal();
</script>
@endsection
