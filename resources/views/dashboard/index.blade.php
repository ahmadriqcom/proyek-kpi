@extends('layouts.app')

@section('title', 'Dashboard Executive KPI Monitoring Board')

@push('styles')
<style>
    .stat-card-compact {
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        background-color: #ffffff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card-compact:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .icon-box-44 {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        font-size: 1.25rem;
    }
    /* Sticky Column Table Styling */
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
</style>
@endpush

@section('content')
<!-- Header Executive Monitoring Board -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-speedometer2 me-2"></i>Executive Monitoring Board KPI</h4>
        <p class="text-muted small mb-0">Papan pemantauan rekapitulasi kinerja operasional, grafik tren bulanan, dan alokasi prioritas.</p>
    </div>
</div>

<!-- Filter Pegawai Card Compact -->
<div class="card card-gov mb-4 shadow-sm">
    <div class="card-body p-2 bg-light-subtle">
        <form method="GET" action="{{ route('dashboard') }}" class="row g-2 align-items-center">
            <div class="col-md-7">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white fw-bold text-secondary"><i class="bi bi-funnel-fill me-1 text-primary"></i> Filter Pegawai:</span>
                    <select name="user_id" id="user_id" class="form-select fw-semibold" onchange="this.form.submit()" {{ auth()->check() && auth()->user()->isOperator() ? 'disabled' : '' }}>
                        @if(!auth()->check() || !auth()->user()->isOperator())
                            <option value="">-- Semua Pegawai (Keseluruhan Operasional Enterprise) --</option>
                        @endif
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $selectedUserId == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }} {{ auth()->check() && auth()->user()->isOperator() ? ' (Akun Anda)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-5 d-flex justify-content-end">
                @if($selectedUserId && (!auth()->check() || !auth()->user()->isOperator()))
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Compact Stat Cards Overview (Focal Point Typography & Consistent Icons) -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card-compact p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Laporan KPI</span>
                    <h2 class="fw-extrabold mb-0 text-primary">{{ number_format($summary['total_reports']) }}</h2>
                </div>
                <div class="icon-box-44 bg-primary-subtle text-primary">
                    <i class="bi bi-journal-text"></i>
                </div>
            </div>
            <div class="mt-2 text-muted small"><i class="bi bi-info-circle me-1"></i> Akumulasi data terkini</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card-compact p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Kendala Selesai</span>
                    <h2 class="fw-extrabold mb-0 text-success">{{ number_format($summary['completed_count']) }}</h2>
                </div>
                <div class="icon-box-44 bg-success-subtle text-success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
            </div>
            <div class="mt-2 text-success small fw-semibold">
                <i class="bi bi-graph-up-arrow me-1"></i> {{ $summary['completion_rate'] }}% Tingkat Penyelesaian
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card-compact p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Dalam Proses / Pending</span>
                    <h2 class="fw-extrabold mb-0 text-warning">{{ number_format($summary['pending_count']) }}</h2>
                </div>
                <div class="icon-box-44 bg-warning-subtle text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
            <div class="mt-2 text-muted small"><i class="bi bi-clock me-1"></i> Membutuhkan penanganan</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card-compact p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Rata-Rata Durasi SLA</span>
                    <h2 class="fw-extrabold mb-0 text-info">{{ $summary['avg_sla_days'] }} <small class="fs-6 text-muted">Hari</small></h2>
                </div>
                <div class="icon-box-44 bg-info-subtle text-info">
                    <i class="bi bi-stopwatch"></i>
                </div>
            </div>
            <div class="mt-2 text-muted small"><i class="bi bi-lightning-charge me-1"></i> Durasi rata-rata SLA</div>
        </div>
    </div>
</div>

<!-- Employee Matriks Score KPI Card (If Employee Selected) -->
@if($employeeScore)
<div class="card card-gov border-primary mb-4 shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
        <span class="fw-bold"><i class="bi bi-award-fill me-2"></i>SKOR MATRIKS KPI PEGAWAI: {{ strtoupper($employeeScore['user']->name) }}</span>
        <span class="badge bg-light text-primary fw-bold">{{ $employeeScore['grade_name'] }}</span>
    </div>
    <div class="card-body p-3">
        <div class="row align-items-center mb-3">
            <div class="col-md-3 text-center border-end">
                <span class="text-muted small fw-semibold text-uppercase d-block">Indeks Score KPI</span>
                <h1 class="display-5 fw-extrabold text-primary mb-0">{{ number_format($employeeScore['kpi_score'], 1) }}</h1>
                <span class="badge bg-success px-3 py-1 mt-1 fs-6">{{ $employeeScore['performance_category'] }}</span>
            </div>

            <div class="col-md-9">
                <div class="row g-2 text-center">
                    <div class="col-md-4">
                        <div class="p-2 bg-light rounded border">
                            <span class="text-muted small d-block">Target SLA:</span>
                            <strong class="text-dark"><i class="bi bi-stopwatch text-info me-1"></i> {{ $employeeScore['target_sla_days'] }} Hari</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 bg-light rounded border">
                            <span class="text-muted small d-block">Total Kendala Ditangani:</span>
                            <strong class="text-dark"><i class="bi bi-check2-all text-success me-1"></i> {{ $employeeScore['total_tasks'] }} Pekerjaan</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 bg-light rounded border">
                            <span class="text-muted small d-block">Skor Matriks (1 - 5):</span>
                            <strong class="text-primary"><i class="bi bi-star-fill text-warning me-1"></i> {{ number_format($employeeScore['total_score_5'], 2) }} / 5.00</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Executive Charts Grid (Modul 4: Line Chart Tren & Doughnut Chart Status) -->
<div class="row g-4 mb-4">
    <!-- Line Chart: Tren Penyelesaian Laporan KPI -->
    <div class="col-md-7">
        <div class="card card-gov shadow-sm h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                <span class="fw-bold text-dark"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Grafik Tren Laporan KPI (Bulanan)</span>
                <span class="badge bg-light text-secondary border">6 Bulan Terakhir</span>
            </div>
            <div class="card-body p-3">
                <div style="height: 250px;">
                    <canvas id="trendLineChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Doughnut Chart: Distribusi Status Laporan -->
    <div class="col-md-5">
        <div class="card card-gov shadow-sm h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                <span class="fw-bold text-dark"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Distribusi Status Kendala</span>
                <span class="badge bg-light text-secondary border">Akumulatif</span>
            </div>
            <div class="card-body p-3 d-flex justify-content-center align-items-center">
                <div style="height: 230px; width: 100%;">
                    <canvas id="statusDoughnutChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance & Priority Insight Row (Top Apps & Priority Alert Box) -->
<div class="row g-4 mb-4">
    <!-- Top 3 Aplikasi Terbanyak Kendala -->
    <div class="col-md-6">
        <div class="card card-gov shadow-sm h-100 border-start border-4 border-info">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                <span class="fw-bold text-dark"><i class="bi bi-app-indicator me-2 text-info"></i>Top 3 Aplikasi Dengan Laporan Kendala Terbanyak</span>
            </div>
            <div class="card-body p-3">
                @if(count($analytics['top_applications']) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($analytics['top_applications'] as $idx => $appItem)
                            <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-2 px-1">
                                <div>
                                    <span class="badge bg-info text-dark font-monospace me-2">#{{ $idx + 1 }}</span>
                                    <strong class="text-dark">{{ $appItem->application->name ?? 'Aplikasi ID ' . $appItem->application_id }}</strong>
                                    <small class="text-muted d-block font-monospace">[{{ $appItem->application->code ?? '-' }}]</small>
                                </div>
                                <span class="badge bg-light text-dark border px-3 py-2 fs-6 fw-bold">
                                    {{ $appItem->total_issues }} Laporan
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted small text-center my-3"><i class="bi bi-info-circle me-1"></i> Belum ada data kendala aplikasi terdaftar.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Priority Alert Box (Kendala Membutuhkan Perhatian) -->
    <div class="col-md-6">
        <div class="card card-gov shadow-sm h-100 border-start border-4 border-warning">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                <span class="fw-bold text-dark"><i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>Priority Alerts (Kendala Membutuhkan Perhatian)</span>
            </div>
            <div class="card-body p-3">
                @if(count($analytics['priority_alerts']) > 0)
                    <div class="list-group list-group-flush">
                        @foreach($analytics['priority_alerts'] as $alert)
                            <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center py-2 px-1">
                                <div>
                                    <span class="badge bg-light text-dark border font-monospace me-1">{{ $alert->ticket_number }}</span>
                                    <strong class="text-dark small d-block">{{ Str::limit($alert->problem, 45) }}</strong>
                                    <small class="text-muted">{{ $alert->app_region_label }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-warning text-dark mb-1 d-block"><i class="bi bi-hourglass-split me-1"></i> Running {{ $alert->running_sla_days }} Hari</span>
                                    <a href="{{ route('kpi-reports.show', $alert->id) }}" class="btn btn-link btn-sm p-0 text-decoration-none small">Detail <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-success py-3">
                        <i class="bi bi-check-circle-fill fs-3 d-block mb-1"></i>
                        <small class="fw-bold">Seluruh kendala berjalan lancar sesuai SLA!</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Data Laporan KPI Terbaru Table (With Sticky Column & Hover Effect) -->
<div class="card card-gov mb-4 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center py-3 bg-light">
        <span class="fs-6 fw-bold text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Laporan KPI Terbaru</span>
        <a href="{{ route('kpi-reports.index') }}" class="btn btn-link btn-sm text-decoration-none p-0 fw-semibold">
            Lihat Data Laporan Lengkap <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-sticky-col align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 12%;" class="sticky-cell">No. Tiket</th>
                        <th style="width: 22%;">Nama Aplikasi & Daerah</th>
                        <th style="width: 15%;">Operator Pembuat</th>
                        <th style="width: 13%;">Menu System</th>
                        <th style="width: 10%;">Tgl Mulai</th>
                        <th style="width: 28%;">Permasalahan Teknis</th>
                        <th style="width: 15%;">Status & SLA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentReports as $index => $report)
                        <tr>
                            <td class="fw-bold font-monospace sticky-cell text-primary">{{ $report->ticket_number }}</td>
                            <td><span class="fw-semibold text-dark">{{ $report->app_region_label }}</span></td>
                            <td><span class="badge bg-light text-dark border"><i class="bi bi-person me-1"></i> {{ $report->creator->name ?? 'System' }}</span></td>
                            <td><span class="badge bg-light text-dark border">{{ $report->menu }}</span></td>
                            <td>{{ $report->start_date->format('d/m/Y') }}</td>
                            <td>
                                <div class="text-truncate" style="max-width: 280px;" title="{{ $report->problem }}">
                                    {{ $report->problem }}
                                </div>
                            </td>
                            <td>
                                @if($report->status === 'completed')
                                    <span class="badge bg-success badge-sla"><i class="bi bi-check-lg me-1"></i> Selesai ({{ $report->sla_duration_days }} Hari)</span>
                                @elseif($report->status === 'cancelled')
                                    <span class="badge bg-secondary badge-sla"><i class="bi bi-x-circle me-1"></i> Dibatalkan</span>
                                @else
                                    <span class="badge bg-warning text-dark badge-sla"><i class="bi bi-hourglass-split me-1"></i> Pending ({{ $report->running_sla_days }} Hari)</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i> Belum ada pencatatan laporan KPI.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data dari Backend Controller
        const trendLabels = @json($analytics['monthly_trend']['labels']);
        const trendCompleted = @json($analytics['monthly_trend']['completed']);
        const trendTotal = @json($analytics['monthly_trend']['total']);

        const statusData = @json($analytics['status_distribution']);

        // 1. Line Chart: Tren Penyelesaian Laporan KPI
        const ctxLine = document.getElementById('trendLineChart').getContext('2d');
        const gradientBlue = ctxLine.createLinearGradient(0, 0, 0, 200);
        gradientBlue.addColorStop(0, 'rgba(13, 110, 253, 0.3)');
        gradientBlue.addColorStop(1, 'rgba(13, 110, 253, 0.0)');

        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [
                    {
                        label: 'Laporan Selesai',
                        data: trendCompleted,
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#198754'
                    },
                    {
                        label: 'Total Laporan Masuk',
                        data: trendTotal,
                        borderColor: '#0d6efd',
                        backgroundColor: gradientBlue,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#0d6efd'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { family: 'Helvetica', size: 12 } }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });

        // 2. Doughnut Chart: Distribusi Status Laporan
        const ctxDoughnut = document.getElementById('statusDoughnutChart').getContext('2d');
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Selesai', 'Pending / Dalam Proses', 'Dibatalkan'],
                datasets: [{
                    data: [statusData.completed, statusData.pending, statusData.cancelled],
                    backgroundColor: ['#198754', '#ffc107', '#6c757d'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { family: 'Helvetica', size: 11 } }
                    }
                }
            }
        });
    });
</script>
@endpush
