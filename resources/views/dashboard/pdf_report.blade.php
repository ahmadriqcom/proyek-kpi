<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Evaluasi KPI Pegawai - {{ $employeeScore['user']->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            color: #1e293b;
            line-height: 1.4;
            margin: 20px;
        }

        .header-kop {
            text-align: center;
            border-bottom: 3px double #0f2c59;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-kop h2 {
            margin: 0;
            font-size: 16pt;
            color: #0f2c59;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-kop p {
            margin: 3px 0 0;
            font-size: 10pt;
            color: #64748b;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 5px 8px;
            vertical-align: top;
        }

        .score-box {
            background-color: #f1f5f9;
            border: 2px solid #0f2c59;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }

        .score-box .score-num {
            font-size: 28pt;
            font-weight: bold;
            color: #0f2c59;
            margin: 0;
        }

        .score-box .score-badge {
            display: inline-block;
            background-color: #198754;
            color: #ffffff;
            font-weight: bold;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 10pt;
            margin-top: 5px;
        }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #0f2c59;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 5px;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9.5pt;
        }

        .data-table th {
            background-color: #0f2c59;
            color: #ffffff;
            text-transform: uppercase;
            font-size: 8.5pt;
            padding: 8px 6px;
            border: 1px solid #0f2c59;
            text-align: left;
        }

        .data-table td {
            padding: 7px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .footer-sign {
            margin-top: 40px;
            width: 100%;
        }

        .footer-sign td {
            text-align: center;
            vertical-align: bottom;
            height: 80px;
        }
    </style>
</head>
<body>

    <!-- Kop Surat Laporan -->
    <div class="header-kop">
        <h2>LAPORAN EVALUASI & PENILAIAN KPI PEGAWAI</h2>
        <p>Sistem Digitalisasi & Otomatisasi Laporan KPI Mingguan Operasional</p>
        <p style="font-size: 8.5pt; color: #94a3b8;">Tanggal Cetak: {{ date('d F Y - H:i') }} WIB</p>
    </div>

    <!-- Identitas Pegawai -->
    <table class="meta-table">
        <tr>
            <td style="width: 18%; font-weight: bold; color: #475569;">Nama Pegawai:</td>
            <td style="width: 32%;"><strong>{{ $employeeScore['user']->name }}</strong></td>
            <td style="width: 18%; font-weight: bold; color: #475569;">Role / Akses:</td>
            <td style="width: 32%;">{{ strtoupper($employeeScore['user']->role) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #475569;">Grade Pegawai:</td>
            <td>{{ $employeeScore['grade_name'] }}</td>
            <td style="font-weight: bold; color: #475569;">Target SLA:</td>
            <td>{{ $employeeScore['target_sla_days'] }} Hari</td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #475569;">Email Akun:</td>
            <td>{{ $employeeScore['user']->email }}</td>
            <td style="font-weight: bold; color: #475569;">Total Pekerjaan:</td>
            <td>{{ $employeeScore['total_tasks'] }} ({{ $employeeScore['completed_tasks'] }} Selesai)</td>
        </tr>
    </table>

    <!-- Ringkasan Score KPI -->
    <div class="score-box">
        <div style="font-size: 10pt; color: #64748b; font-weight: bold; text-transform: uppercase;">Akumulasi Score KPI Pegawai</div>
        <div class="score-num">{{ number_format($employeeScore['kpi_score'], 1) }} <span style="font-size: 14pt; color: #64748b;">/ 100</span></div>
        <div class="score-badge">{{ $employeeScore['performance_category'] }}</div>
    </div>

    <!-- Tabel Rincian Penilaian KPI Matriks 6 Kriteria (Skala 1 - 5) -->
    <div class="section-title">RINCIAN MATRIKS EVALUASI PENILAIAN KPI (SKALA 1 - 5)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 25%;">Kriteria Penilaian</th>
                <th style="width: 10%; text-align: center;">Bobot (%)</th>
                <th style="width: 12%; text-align: center;">Skor (1 - 5)</th>
                <th style="width: 13%; text-align: center;">Nilai (Bobot × Skor)</th>
                <th style="width: 35%;">Catatan Evaluator & Indikator Rubrik</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employeeScore['matrix'] as $idx => $row)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $idx + 1 }}</td>
                    <td style="font-weight: bold; color: #0f2c59;">{{ $row['kriteria'] }}</td>
                    <td style="text-align: center;">{{ $row['bobot_persen'] }}%</td>
                    <td style="text-align: center; font-weight: bold;">{{ $row['skor'] }}</td>
                    <td style="text-align: center; font-weight: bold; color: #0f2c59;">{{ number_format($row['nilai'], 2) }}</td>
                    <td style="font-size: 8.5pt; color: #334155;">{{ $row['catatan'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e2e8f0; font-weight: bold;">
                <td colspan="4" style="text-align: right; padding: 8px;">TOTAL NILAI EVALUASI (SKALA 1 - 5):</td>
                <td style="text-align: center; color: #0f2c59; font-size: 11pt;">{{ number_format($employeeScore['total_score_5'], 2) }}</td>
                <td style="font-size: 8.5pt; color: #0f2c59;">(Setara {{ number_format($employeeScore['kpi_score'], 1) }} pada Skala 100)</td>
            </tr>
        </tfoot>
    </table>

    <!-- Tabel Daftar Pekerjaan Terakhir -->
    <div class="section-title">DAFTAR RINCIAN PEKERJAAN DITANGANI</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">No. Ticket</th>
                <th style="width: 20%;">Aplikasi & Daerah</th>
                <th style="width: 15%;">Menu</th>
                <th style="width: 12%;">Tgl Mulai</th>
                <th style="width: 12%;">Tgl Selesai</th>
                <th style="width: 11%;">Status</th>
                <th style="width: 10%; text-align: center;">SLA</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $idx => $report)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td style="font-family: monospace;">{{ $report->ticket_number }}</td>
                    <td><strong>{{ $report->app_region_label }}</strong></td>
                    <td>{{ $report->menu }}</td>
                    <td>{{ $report->start_date->format('d/m/Y') }}</td>
                    <td>{{ $report->end_date ? $report->end_date->format('d/m/Y') : '-' }}</td>
                    <td>{{ strtoupper($report->status) }}</td>
                    <td style="text-align: center;">{{ $report->sla_duration_days ?? $report->running_sla_days }} Hari</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #94a3b8; padding: 15px;">Belum ada riwayat pekerjaan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan Evaluator -->
    <table class="footer-sign">
        <tr>
            <td style="width: 50%;">
                Mengetahui,<br>
                <strong>Pimpinan / Evaluator KPI</strong><br><br><br><br>
                ( _______________________ )
            </td>
            <td style="width: 50%;">
                Pegawai Yang Dinilai,<br><br><br><br><br>
                ( <strong>{{ $employeeScore['user']->name }}</strong> )
            </td>
        </tr>
    </table>

</body>
</html>
