@extends('layouts.app')

@section('title', 'Tambah Laporan Kendala KPI Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Pencatatan Kendala Baru</h4>
        <p class="text-muted small mb-0">Lengkapi formulir di bawah ini untuk mencatat kendala teknis atau laporan KPI pengguna.</p>
    </div>
    <a href="{{ route('kpi-reports.index') }}" class="btn btn-outline-secondary btn-sm px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Data Grid
    </a>
</div>

<div class="card card-gov shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <span class="fw-bold text-secondary"><i class="bi bi-journal-plus me-2"></i>Formulir Entri Laporan KPI</span>
        <span class="badge bg-info-subtle text-info border"><i class="bi bi-cpu me-1"></i> Auto-Interpretation Engine Active</span>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('kpi-reports.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="application_id" class="form-label fw-semibold">Nama Aplikasi <span class="text-danger">*</span></label>
                    <select name="application_id" id="application_id" class="form-select @error('application_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Aplikasi --</option>
                        @foreach($applications as $app)
                            <option value="{{ $app->id }}" {{ old('application_id') == $app->id ? 'selected' : '' }}>
                                {{ $app->code }} - {{ $app->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('application_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="region_id" class="form-label fw-semibold">Nama Daerah <span class="text-danger">*</span></label>
                    <select name="region_id" id="region_id" class="form-select @error('region_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Aplikasi Terlebih Dahulu --</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                {{ $region->name }} ({{ $region->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('region_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Tampilan Eksklusif Parameter Scorer (Hanya Tampil Jika User Management / Superadmin) -->
            @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->isManagement()))
                <div class="p-3 bg-light rounded border mb-3">
                    <div class="fw-bold text-primary mb-2 small"><i class="bi bi-sliders me-1"></i> PENENTUAN PARAMETER SCORING (SUPERADMIN / MANAGEMENT)</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="kpi_category_id" class="form-label fw-semibold small">Kategori Kendala:</label>
                            <select name="kpi_category_id" id="kpi_category_id" class="form-select form-select-sm">
                                <option value="">-- Auto Determine by Engine --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('kpi_category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }} (×{{ number_format($cat->complexity_weight, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="kpi_priority_id" class="form-label fw-semibold small">Tingkat Prioritas:</label>
                            <select name="kpi_priority_id" id="kpi_priority_id" class="form-select form-select-sm">
                                <option value="">-- Auto Determine by Engine --</option>
                                @foreach($priorities as $prio)
                                    <option value="{{ $prio->id }}" {{ old('kpi_priority_id') == $prio->id ? 'selected' : '' }}>
                                        {{ $prio->name }} (SLA: {{ $prio->target_sla_days }}d, ×{{ number_format($prio->urgency_weight, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="kpi_impact_level_id" class="form-label fw-semibold small">Tingkat Dampak:</label>
                            <select name="kpi_impact_level_id" id="kpi_impact_level_id" class="form-select form-select-sm">
                                <option value="">-- Auto Determine by Engine --</option>
                                @foreach($impacts as $imp)
                                    <option value="{{ $imp->id }}" {{ old('kpi_impact_level_id') == $imp->id ? 'selected' : '' }}>
                                        {{ $imp->name }} (×{{ number_format($imp->impact_weight, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="menu" class="form-label fw-semibold">Menu / Modul Terkait <span class="text-danger">*</span></label>
                    <input type="text" name="menu" id="menu" class="form-control @error('menu') is-invalid @enderror" placeholder="Contoh: Daftar Notifikasi Transfer, RKAS / RAPBS" value="{{ old('menu') }}" required>
                    @error('menu')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="start_date" class="form-label fw-semibold">Tgl Mulai Penanganan <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', date('Y-m-d')) }}" required>
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="problem" class="form-label fw-semibold">Permasalahan yang Dihadapi <span class="text-danger">*</span></label>
                <textarea name="problem" id="problem" rows="4" class="form-control @error('problem') is-invalid @enderror" placeholder="Tuliskan detail pesan error, warning, atau permintaan bantuan dari klien/user..." required>{{ old('problem') }}</textarea>
                @error('problem')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="solution" class="form-label fw-semibold">Solusi / Langkah Penanganan (Opsional)</label>
                <textarea name="solution" id="solution" rows="3" class="form-control @error('solution') is-invalid @enderror" placeholder="Tuliskan tindakan teknis, perbaikan bug, atau instruksi penanganan jika sudah ada...">{{ old('solution') }}</textarea>
                @error('solution')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 p-3 bg-light rounded border">
                <label for="attachment" class="form-label fw-semibold text-primary"><i class="bi bi-paperclip me-1"></i> Upload Lampiran / Bukti Pendukung (Opsional)</label>
                <input type="file" name="attachment" id="attachment" class="form-control @error('attachment') is-invalid @enderror" accept=".pdf,.png,.jpg,.jpeg,.xlsx">
                <small class="text-muted d-block mt-1">Format didukung: PDF, PNG, JPG, XLSX (Max 5MB).</small>
                @error('attachment')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="end_date" class="form-label fw-semibold">Tgl Selesai (Kosongkan jika masih Dalam Proses)</label>
                    <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                    <div class="form-text">Jika diisi, laporan otomatis ditandai <strong>Selesai</strong> dan Nilai KPI langsung dihitung.</div>
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="remarks" class="form-label fw-semibold">Keterangan Catatan Tambahan</label>
                    <input type="text" name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror" placeholder="Catatan arahan lanjutan ke klien / internal..." value="{{ old('remarks') }}">
                    @error('remarks')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                <a href="{{ route('kpi-reports.index') }}" class="btn btn-secondary px-4">Batal</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan Laporan KPI</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('application_id').addEventListener('change', function() {
    const appId = this.value;
    const regionSelect = document.getElementById('region_id');
    regionSelect.innerHTML = '<option value="">-- Memuat Daerah... --</option>';

    if (!appId) {
        regionSelect.innerHTML = '<option value="">-- Pilih Aplikasi Terlebih Dahulu --</option>';
        return;
    }

    fetch('/api/applications/' + appId + '/regions')
        .then(response => response.json())
        .then(data => {
            regionSelect.innerHTML = '<option value="">-- Pilih Daerah --</option>';
            if (data.length === 0) {
                regionSelect.innerHTML = '<option value="">-- Tidak ada daerah ter-mapping --</option>';
            } else {
                data.forEach(region => {
                    const option = document.createElement('option');
                    option.value = region.id;
                    option.text = region.name + ' (' + region.code + ')';
                    regionSelect.appendChild(option);
                });
            }
        })
        .catch(err => {
            regionSelect.innerHTML = '<option value="">-- Gagal memuat daerah --</option>';
        });
});
</script>
@endsection
