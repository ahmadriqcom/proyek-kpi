@extends('layouts.app')

@section('title', 'Data Laporan KPI Mingguan & Analisis Operasional')

@push('styles')
<style>
    .stat-card-mini {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        background-color: #ffffff;
    }
    .icon-box-36 {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        font-size: 1.1rem;
    }
    /* Sticky Column Table */
    .table-sticky-col {
        position: relative;
    }
    .table-sticky-col th.sticky-cell,
    .table-sticky-col td.sticky-cell {
        position: sticky;
        left: 0;
        background-color: #ffffff !important;
        z-index: 2;
        box-shadow: 2px 0 5px rgba(0,0,0,0.05);
    }
    .table-striped tbody tr:nth-of-type(odd) td.sticky-cell {
        background-color: #f8fafc !important;
    }
    .sort-header {
        color: inherit;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .sort-header:hover {
        color: #0d6efd;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-journal-check me-2"></i>Laporan KPI Mingguan & Analisis Operasional</h4>
        <p class="text-muted small mb-0">Manajemen pencatatan kendala operasional, monitoring SLA, impor spreadsheet, dan analitis crosstab.</p>
    </div>
    <div class="d-flex gap-2">
        @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('kpi_reports', 'create')))
            <button type="button" class="btn btn-outline-success btn-sm px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-excel-fill me-1"></i> Impor Spreadsheet (Preview Engine)
            </button>
            <a href="{{ route('kpi-reports.create') }}" class="btn btn-primary btn-sm px-3 fw-semibold shadow-sm">
                <i class="bi bi-plus-circle-fill me-1"></i> Tambah Laporan Kendala
            </a>
        @endif
        @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('kpi_reports', 'print')))
            <a href="{{ route('kpi-reports.export', request()->all()) }}" class="btn btn-outline-primary btn-sm px-3 fw-semibold">
                <i class="bi bi-download me-1"></i> Ekspor Excel
            </a>
        @endif
    </div>
</div>

<!-- Tabs Navigasi Datagrid & Analisis -->
<ul class="nav nav-tabs nav-tabs-gov mb-4" id="reportTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold" id="datagrid-tab" data-bs-toggle="tab" data-bs-target="#datagrid" type="button" role="tab"><i class="bi bi-table me-1"></i> Data Grid Laporan & Filter Multi-Parameter</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold" id="analytics-tab" data-bs-toggle="tab" data-bs-target="#analytics" type="button" role="tab"><i class="bi bi-bar-chart-line-fill me-1"></i> Analisis & Operational Reporting</button>
    </li>
</ul>

<div class="tab-content" id="reportTabContent">
    <!-- TAB 1: DATA GRID LAPORAN & FILTER MULTI-PARAMETER -->
    <div class="tab-pane fade show active" id="datagrid" role="tabpanel">
        
        <!-- Summary Cards Real-Time (Di Atas Tabel) -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card-mini p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Laporan KPI</span>
                            <h3 class="fw-extrabold mb-0 text-primary">{{ number_format($summary['total_reports']) }}</h3>
                        </div>
                        <div class="icon-box-36 bg-primary-subtle text-primary">
                            <i class="bi bi-journal-text"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card-mini p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Laporan Selesai</span>
                            <h3 class="fw-extrabold mb-0 text-success">{{ number_format($summary['completed_count']) }}</h3>
                        </div>
                        <div class="icon-box-36 bg-success-subtle text-success">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card-mini p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Dalam Proses / Pending</span>
                            <h3 class="fw-extrabold mb-0 text-warning">{{ number_format($summary['pending_count']) }}</h3>
                        </div>
                        <div class="icon-box-36 bg-warning-subtle text-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card-mini p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Mean Time to Resolve (MTTR)</span>
                            <h3 class="fw-extrabold mb-0 text-info">{{ $summary['avg_sla_days'] }} <small class="fs-6 text-muted">Hari</small></h3>
                        </div>
                        <div class="icon-box-36 bg-info-subtle text-info">
                            <i class="bi bi-stopwatch"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Session Import Failure Log Detail Table Alert -->
        @if(session('import_detailed_errors'))
            <div class="alert alert-danger shadow-sm mb-4">
                <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-octagon-fill me-2"></i>Detail Tabel Baris Excel Gagal Diimpor:</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered bg-white text-dark mb-0">
                        <thead class="table-danger">
                            <tr>
                                <th style="width: 15%;">Baris Excel</th>
                                <th style="width: 35%;">Nama Data</th>
                                <th style="width: 50%;">Alasan Spesifik Kegagalan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('import_detailed_errors') as $err)
                                <tr>
                                    <td class="fw-bold text-center">Baris {{ $err['row'] }}</td>
                                    <td>{{ $err['data_name'] }}</td>
                                    <td class="text-danger small">{{ $err['reason'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Panel Filter Multi-Parametric -->
        <div class="card card-gov mb-4 shadow-sm">
            <div class="card-header bg-light py-2">
                <span class="fw-bold text-secondary small"><i class="bi bi-funnel-fill me-1 text-primary"></i> Panel Filter Multi-Parametric</span>
            </div>
            <div class="card-body p-3">
                <form method="GET" action="{{ route('kpi-reports.index') }}" class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Pencarian Kata Kunci:</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="No. tiket, menu, permasalahan..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">Status Penanganan:</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending / Proses</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">Master Aplikasi:</label>
                        <select name="application_id" class="form-select form-select-sm">
                            <option value="">-- Semua Aplikasi --</option>
                            @foreach($applications as $app)
                                <option value="{{ $app->id }}" {{ request('application_id') == $app->id ? 'selected' : '' }}>{{ $app->code }} - {{ $app->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">Master Daerah:</label>
                        <select name="region_id" class="form-select form-select-sm">
                            <option value="">-- Semua Daerah --</option>
                            @foreach($regions as $reg)
                                <option value="{{ $reg->id }}" {{ request('region_id') == $reg->id ? 'selected' : '' }}>{{ $reg->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Tingkat Prioritas:</label>
                        <select name="priority" class="form-select form-select-sm">
                            <option value="">-- Semua Prioritas --</option>
                            <option value="Critical" {{ request('priority') === 'Critical' ? 'selected' : '' }}>Critical (Mendesak)</option>
                            <option value="High" {{ request('priority') === 'High' ? 'selected' : '' }}>High (Tinggi)</option>
                            <option value="Medium" {{ request('priority') === 'Medium' ? 'selected' : '' }}>Medium (Sedang)</option>
                            <option value="Low" {{ request('priority') === 'Low' ? 'selected' : '' }}>Low (Rendah)</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Range Tgl Mulai (Dari - Sampai):</label>
                        <div class="input-group input-group-sm">
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Range Nilai KPI (Min - Max):</label>
                        <div class="input-group input-group-sm">
                            <input type="number" name="min_score" class="form-control" placeholder="0" value="{{ request('min_score') }}">
                            <input type="number" name="max_score" class="form-control" placeholder="100" value="{{ request('max_score') }}">
                        </div>
                    </div>

                    <div class="col-md-6 d-flex align-items-end justify-content-end gap-2 mt-3">
                        <button type="submit" class="btn btn-primary btn-sm px-3"><i class="bi bi-search me-1"></i> Terapkan Filter</button>
                        <a href="{{ route('kpi-reports.index') }}" class="btn btn-outline-secondary btn-sm px-3"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Grid Table With Sorting Columns & Sticky Column -->
        <div class="card card-gov mb-4 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-sticky-col align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 12%;" class="sticky-cell">
                                    <a href="{{ route('kpi-reports.index', array_merge(request()->all(), ['sort_by' => 'ticket_number', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="sort-header">
                                        <span>No. Tiket</span> <i class="bi bi-arrow-down-up small"></i>
                                    </a>
                                </th>
                                <th style="width: 18%;">Nama Aplikasi & Daerah</th>
                                <th style="width: 10%;">Prioritas</th>
                                <th style="width: 12%;">Kategori</th>
                                <th style="width: 10%;">
                                    <a href="{{ route('kpi-reports.index', array_merge(request()->all(), ['sort_by' => 'start_date', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="sort-header">
                                        <span>Tgl Mulai</span> <i class="bi bi-arrow-down-up small"></i>
                                    </a>
                                </th>
                                <th style="width: 20%;">Permasalahan</th>
                                <th style="width: 10%;">
                                    <a href="{{ route('kpi-reports.index', array_merge(request()->all(), ['sort_by' => 'status', 'sort_dir' => request('sort_dir') === 'asc' ? 'desc' : 'asc'])) }}" class="sort-header">
                                        <span>Status</span> <i class="bi bi-arrow-down-up small"></i>
                                    </a>
                                </th>
                                <th style="width: 12%;">Monitoring SLA</th>
                                <th style="width: 6%; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                                <tr>
                                    <td class="fw-bold font-monospace sticky-cell text-primary">
                                        {{ $report->ticket_number }}
                                    </td>
                                    <td>
                                        <strong class="text-dark d-block">{{ $report->app_region_label }}</strong>
                                        <small class="text-muted font-monospace">{{ $report->menu }}</small>
                                    </td>
                                    <td>{!! $report->priority_badge !!}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border d-block mb-1">{{ $report->kpiCategory->name ?? $report->category ?? 'Technical / Bug' }}</span>
                                        {!! $report->data_origin_badge !!}
                                    </td>
                                    <td>{{ $report->start_date->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 250px;" title="{{ $report->problem }}">
                                            {{ $report->problem }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($report->status === 'completed')
                                            <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i> Selesai</span>
                                        @elseif($report->status === 'cancelled')
                                            <span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i> Dibatalkan</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            {!! $report->sla_badge !!}
                                            <small class="text-muted fw-semibold"><i class="bi bi-clock me-1"></i> Usia: {{ $report->running_sla_days }} Hari</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle py-0" type="button" data-bs-toggle="dropdown">
                                                Opsi
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                <li><a class="dropdown-menu-item dropdown-item small" href="{{ route('kpi-reports.show', $report->id) }}"><i class="bi bi-eye text-primary me-2"></i> Detail Laporan</a></li>
                                                @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('kpi_reports', 'update')))
                                                    <li><a class="dropdown-menu-item dropdown-item small" href="{{ route('kpi-reports.edit', $report->id) }}"><i class="bi bi-pencil-square text-warning me-2"></i> Edit / Solusi</a></li>
                                                @endif
                                                @if($report->attachment_path)
                                                    <li><a class="dropdown-menu-item dropdown-item small text-success" href="{{ asset('storage/' . $report->attachment_path) }}" target="_blank"><i class="bi bi-paperclip me-2"></i> Unduh Lampiran</a></li>
                                                @endif
                                                @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('kpi_reports', 'delete')))
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('kpi-reports.destroy', $report->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus laporan {{ $report->ticket_number }}?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger small"><i class="bi bi-trash-fill me-2"></i> Hapus Laporan</button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i> Tidak ada data laporan KPI yang sesuai filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light d-flex justify-content-between align-items-center py-2">
                <span class="small text-muted">Menampilkan {{ $reports->firstItem() ?? 0 }} s.d {{ $reports->lastItem() ?? 0 }} dari {{ $reports->total() }} Laporan</span>
                <div>{{ $reports->links() }}</div>
            </div>
        </div>
    </div>

    <!-- TAB 2: ANALISIS & OPERATIONAL REPORTING -->
    <div class="tab-pane fade" id="analytics" role="tabpanel">
        <div class="row g-4 mb-4">
            <div class="col-md-7">
                <div class="card card-gov shadow-sm h-100">
                    <div class="card-header bg-light">
                        <span class="fw-bold text-dark"><i class="bi bi-graph-up me-2 text-primary"></i>Grafik Tren Volume Kendala vs Penanganan Selesai</span>
                    </div>
                    <div class="card-body p-3">
                        <div style="height: 260px;">
                            <canvas id="reportingLineChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card card-gov shadow-sm h-100">
                    <div class="card-header bg-light">
                        <span class="fw-bold text-dark"><i class="bi bi-pie-chart me-2 text-success"></i>Distribusi Status Laporan</span>
                    </div>
                    <div class="card-body p-3 d-flex justify-content-center align-items-center">
                        <div style="height: 240px; width: 100%;">
                            <canvas id="reportingDonutChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Rekapitulasi Crosstab Matrix (Aplikasi x Daerah) -->
        <div class="card card-gov shadow-sm">
            <div class="card-header bg-light">
                <span class="fw-bold text-dark"><i class="bi bi-grid-3x3-gap-fill me-2 text-info"></i>Tabel Rekapitulasi Crosstab (Aplikasi × Daerah)</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-spreadsheet align-middle mb-0">
                        <thead class="table-primary text-dark">
                            <tr>
                                <th style="width: 5%; text-align: center;">No</th>
                                <th style="width: 25%;">Kode & Nama Aplikasi</th>
                                <th style="width: 25%;">Frekuensi Kendala Terbanyak</th>
                                <th style="width: 20%; text-align: center;">Jumlah Laporan</th>
                                <th style="width: 25%;">Status Dominan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analytics['top_applications'] as $idx => $topApp)
                                <tr>
                                    <td class="text-center font-monospace">{{ $idx + 1 }}</td>
                                    <td class="fw-bold text-primary">{{ $topApp->application->name ?? 'Aplikasi ID ' . $topApp->application_id }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $topApp->application->code ?? '-' }}</span></td>
                                    <td class="text-center fs-6 fw-extrabold text-dark">{{ $topApp->total_issues }} Kendala</td>
                                    <td><span class="badge bg-success-subtle text-success border">Operasional Terpantau</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Spreadsheet Import With Advanced Preview Engine Engine -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('kpi-reports.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-file-earmark-excel-fill me-2 text-success"></i>Advanced Spreadsheet Import Engine</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3 p-3 bg-light rounded border">
                        <label for="import_file" class="form-label fw-semibold">Pilih Berkas Spreadsheet (.xlsx / .csv) <span class="text-danger">*</span></label>
                        <input type="file" name="file" id="import_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted d-block mt-1">Ukuran maksimal file: 5MB.</small>
                    </div>

                    <!-- Button Trigger Preview Grid Modal -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <button type="button" class="btn btn-outline-info btn-sm fw-semibold" id="btnPreviewExcel">
                            <i class="bi bi-eye-fill me-1"></i> Pratinjau Grid (10 Baris Pertama)
                        </button>
                        <small class="text-muted italic">Uji validasi kolom sebelum komitmen impor ke database.</small>
                    </div>

                    <!-- Area Grid Preview Modal -->
                    <div id="previewGridContainer" class="d-none border rounded p-2 bg-white">
                        <h6 class="fw-bold text-primary small mb-2"><i class="bi bi-table me-1"></i>Pratinjau Data Excel (10 Baris Pertama):</h6>
                        <div class="table-responsive" style="max-height: 200px;">
                            <table class="table table-sm table-bordered small mb-0" id="previewGridTable">
                                <thead class="table-light" id="previewGridHeader"></thead>
                                <tbody id="previewGridBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-upload me-1"></i> Eksekusi Impor Spreadsheet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Render Chart pada Tab Analisis
    const trendLabels = @json($analytics['monthly_trend']['labels']);
    const trendCompleted = @json($analytics['monthly_trend']['completed']);
    const trendTotal = @json($analytics['monthly_trend']['total']);

    const statusData = @json($analytics['status_distribution']);

    const ctxLine = document.getElementById('reportingLineChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [
                {
                    label: 'Selesai Ditangani',
                    data: trendCompleted,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Total Kendala',
                    data: trendTotal,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    const ctxDonut = document.getElementById('reportingDonutChart').getContext('2d');
    new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: ['Selesai', 'Pending / Proses', 'Dibatalkan'],
            datasets: [{
                data: [statusData.completed, statusData.pending, statusData.cancelled],
                backgroundColor: ['#198754', '#ffc107', '#6c757d']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 2. Ajax Pratinjau Grid 10 Baris Pertama Spreadsheet
    document.getElementById('btnPreviewExcel').addEventListener('click', function() {
        const fileInput = document.getElementById('import_file');
        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Silakan pilih berkas spreadsheet terlebih dahulu!');
            return;
        }

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("kpi-reports.import-preview") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('previewGridContainer');
            const headerRow = document.getElementById('previewGridHeader');
            const bodyRows = document.getElementById('previewGridBody');

            headerRow.innerHTML = '';
            bodyRows.innerHTML = '';

            if (data.header && data.header.length > 0) {
                let trH = '<tr>';
                data.header.forEach(h => { trH += `<th>${h}</th>`; });
                trH += '</tr>';
                headerRow.innerHTML = trH;
            }

            if (data.preview_rows && data.preview_rows.length > 0) {
                data.preview_rows.forEach(row => {
                    let trB = '<tr>';
                    row.forEach(cell => { trB += `<td>${cell !== null ? cell : ''}</td>`; });
                    trB += '</tr>';
                    bodyRows.innerHTML += trB;
                });
            }

            container.classList.remove('d-none');
        })
        .catch(err => {
            alert('Gagal membaca pratinjau berkas. Pastikan format spreadsheet valid.');
        });
    });

    // LocalStorage Filter Persistence
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        const filterKeys = ['status', 'application_id', 'region_id', 'priority', 'category', 'start_date_from', 'start_date_to'];

        filterForm.addEventListener('submit', function() {
            filterKeys.forEach(key => {
                const input = filterForm.querySelector(`[name="${key}"]`);
                if (input) {
                    localStorage.setItem('kpi_filter_' + key, input.value);
                }
            });
        });

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.toString() === '') {
            let restored = false;
            filterKeys.forEach(key => {
                const savedVal = localStorage.getItem('kpi_filter_' + key);
                const input = filterForm.querySelector(`[name="${key}"]`);
                if (savedVal && input && !input.value) {
                    input.value = savedVal;
                    restored = true;
                }
            });
            if (restored) {
                filterForm.submit();
            }
        }
    }
});
</script>
@endpush
