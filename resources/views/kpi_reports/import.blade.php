@extends('layouts.app')

@section('title', 'Impor Spreadsheet Laporan KPI')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-file-earmark-excel-fill text-success me-2"></i>Modul Impor Spreadsheet Laporan KPI</h4>
        <p class="text-muted small mb-0">Unggah berkas laporan KPI mingguan berbasis spreadsheet (.xlsx / .csv) ke dalam sistem.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('kpi-reports.export') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-download me-1"></i> Unduh Format Contoh (.xlsx)
        </a>
        <a href="{{ route('kpi-reports.index') }}" class="btn btn-outline-primary btn-sm px-3">
            <i class="bi bi-table me-1"></i> Lihat Data Laporan
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-gov shadow-sm">
            <div class="card-header bg-light">
                <span class="fw-bold text-success"><i class="bi bi-upload me-2"></i>Form Unggah Berkas Spreadsheet</span>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info border-info mb-4">
                    <h6 class="fw-bold mb-2"><i class="bi bi-info-circle-fill me-2"></i>Ketentuan Format Spreadsheet:</h6>
                    <ul class="mb-0 small ps-3">
                        <li><strong>Akses Multi-Role:</strong> Dapat diakses dan diunggah oleh <strong>Operator</strong>, <strong>Management</strong>, dan <strong>Superadmin</strong>.</li>
                        <li><strong>Pengikatan Otomatis:</strong> Seluruh data yang diimpor akan otomatis diikat atas nama akun yang sedang login saat unggah.</li>
                        <li><strong>Struktur Kolom Wajib:</strong>
                            <ol class="mt-1 mb-0 ps-3">
                                <li><code>Nama Aplikasi & Daerah</code> (Contoh: <em>BOP - Batam</em>)</li>
                                <li><code>Menu</code> (Contoh: <em>Daftar Notifikasi Transfer</em>)</li>
                                <li><code>Tgl Mulai</code> (Format: <em>DD/MM/YYYY</em> atau <em>YYYY-MM-DD</em>)</li>
                                <li><code>Permasalahan yang Dihadapi</code></li>
                                <li><code>Solusi</code> (Opsional)</li>
                                <li><code>Tgl Selesai</code> (Opsional)</li>
                                <li><code>Keterangan</code> (Opsional)</li>
                            </ol>
                        </li>
                    </ul>
                </div>

                <form action="{{ route('kpi-reports.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="file" class="form-label fw-bold text-dark">Pilih Berkas Spreadsheet (.xlsx, .xls, .csv) <span class="text-danger">*</span></label>
                        <input class="form-control form-control-lg @error('file') is-invalid @enderror" type="file" id="file" name="file" accept=".xlsx, .xls, .csv" required>
                        <div class="form-text">Maksimal ukuran berkas 10 MB.</div>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-top pt-3">
                        <span class="small text-muted"><i class="bi bi-shield-check text-success me-1"></i> Data dienkripsi & diproses aman</span>
                        <button type="submit" class="btn btn-success px-4 py-2 fw-semibold">
                            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Unggah & Proses Impor Spreadsheet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
