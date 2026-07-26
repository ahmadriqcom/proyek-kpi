<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lembar Appraisal KPI - {{ $appraisal->appraisal_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #1e293b;
            line-height: 1.4;
            margin: 15px;
        }

        .header-kop {
            text-align: center;
            border-bottom: 3px double #0f2c59;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .header-kop h2 {
            margin: 0;
            font-size: 15pt;
            color: #0f2c59;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-kop p {
            margin: 2px 0 0;
            font-size: 9pt;
            color: #64748b;
        }

        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 4px 6px;
            vertical-align: top;
            font-size: 9pt;
        }

        .score-box {
            background-color: #f1f5f9;
            border: 2px solid #0f2c59;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            margin-bottom: 15px;
        }

        .score-box .score-num {
            font-size: 24pt;
            font-weight: bold;
            color: #0f2c59;
            margin: 0;
        }

        .score-box .score-badge {
            display: inline-block;
            background-color: #0f2c59;
            color: #ffffff;
            font-weight: bold;
            padding: 3px 12px;
            border-radius: 4px;
            font-size: 10pt;
            margin-top: 4px;
        }

        .section-title {
            font-size: 10.5pt;
            font-weight: bold;
            color: #0f2c59;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-top: 15px;
            margin-bottom: 8px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8.5pt;
        }

        .data-table th {
            background-color: #0f2c59;
            color: #ffffff;
            text-transform: uppercase;
            font-size: 8pt;
            padding: 6px 5px;
            border: 1px solid #0f2c59;
            text-align: left;
        }

        .data-table td {
            padding: 6px 5px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #0f2c59;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 9.5pt;
        }

        .recommendation-box {
            background-color: #e0f2fe;
            border: 1px solid #0284c7;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 9.5pt;
        }

        .footer-sign {
            margin-top: 30px;
            width: 100%;
            font-size: 9.5pt;
        }

        .footer-sign td {
            text-align: center;
            vertical-align: bottom;
            height: 70px;
        }
    </style>
</head>
<body>

    <!-- Kop Surat -->
    <div class="header-kop">
        <h2>LEMBAR PENILAIAN KINERJA (APPRAISAL) KONSULTAN IT</h2>
        <p>Sistem Evaluasi Skema Penilaian Grade & Interpretasi Kompetensi Pegawai</p>
        <p style="font-size: 8pt; color: #94a3b8;">No. Dokumen: {{ $appraisal->appraisal_number }} | Tanggal Cetak: {{ date('d F Y - H:i') }} WIB</p>
    </div>

    <!-- Identitas -->
    <table class="meta-table">
        <tr>
            <td style="width: 18%; font-weight: bold; color: #475569;">Nama Pegawai:</td>
            <td style="width: 32%;"><strong>{{ $appraisal->user->name ?? 'User Unknown' }}</strong></td>
            <td style="width: 18%; font-weight: bold; color: #475569;">Grade Position:</td>
            <td style="width: 32%;">{{ $appraisal->grade->nama_grade ?? "Grade {$appraisal->user->grade_level}" }} {{ $appraisal->grade->career_path ? '(' . $appraisal->grade->career_path . ')' : '' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #475569;">Role Access:</td>
            <td>{{ strtoupper($appraisal->user->role ?? 'OPERATOR') }}</td>
            <td style="font-weight: bold; color: #475569;">Penilai (Evaluator):</td>
            <td>{{ $appraisal->evaluator->name ?? 'System' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #475569;">Status Approval:</td>
            <td><strong>{{ strtoupper($appraisal->approval_status) }}</strong></td>
            <td style="font-weight: bold; color: #475569;">Tanggal Penilaian:</td>
            <td>{{ $appraisal->created_at->format('d F Y') }}</td>
        </tr>
    </table>

    <!-- Score Box -->
    <div class="score-box">
        <div style="font-size: 9pt; color: #64748b; font-weight: bold; text-transform: uppercase;">Total Nilai Akhir Appraisal KPI</div>
        <div class="score-num">{{ number_format($appraisal->total_score, 2) }} <span style="font-size: 12pt; color: #64748b;">/ 100</span></div>
        <div class="score-badge">PREDIKAT: {{ strtoupper($appraisal->predicate) }}</div>
    </div>

    <!-- Ringkasan Eksekutif & Interpretasi Otomatis (Modul 2) -->
    <div class="summary-box">
        <strong style="color: #0f2c59;">RINGKASAN EKSEKUTIF & EVALUASI KOMPETENSI:</strong><br>
        <p style="margin: 4px 0; font-style: italic; color: #0f2c59;">"{{ $appraisal->executive_summary }}"</p>
        <div style="margin-top: 6px; font-size: 8.5pt;">
            <strong>• Kompetensi Terkuat (Skor ≥ 4):</strong> {{ $appraisal->strongest_competency ?? '-' }}<br>
            <strong>• Area Pengembangan (Skor ≤ 3):</strong> {{ $appraisal->weakest_competency ?? '-' }}
        </div>
        @if($appraisal->evaluator_justification)
            <div style="margin-top: 6px; padding-top: 6px; border-top: 1px dashed #cbd5e1; font-size: 8.5pt;">
                <strong>• Catatan Penilai / Justifikasi Evaluator:</strong> {{ $appraisal->evaluator_justification }}
            </div>
        @endif
    </div>

    <!-- Rekomendasi Box -->
    <div class="recommendation-box">
        <strong style="color: #0369a1;">Rekomendasi Tindak Lanjut Otomatis (Management):</strong><br>
        {{ $appraisal->recommendation }}
    </div>

    <!-- Tabel Rincian Kriteria (Renamed to Interpretasi Penilaian Dinamis) -->
    <div class="section-title">RINCIAN SKOR & INTERPRETASI PENILAIAN 8 KRITERIA</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 22%;">Kriteria Penilaian</th>
                <th style="width: 10%; text-align: center;">Bobot (%)</th>
                <th style="width: 10%; text-align: center;">Skor (1-5)</th>
                <th style="width: 11%; text-align: center;">Nilai Konversi</th>
                <th style="width: 12%; text-align: center;">Nilai Berbobot</th>
                <th style="width: 30%;">Interpretasi Penilaian Dinamis</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appraisal->details as $idx => $dt)
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $idx + 1 }}</td>
                    <td style="font-weight: bold; color: #0f2c59;">{{ $dt->criteria->nama_kriteria ?? 'Kriteria' }}</td>
                    <td style="text-align: center;">{{ number_format($dt->weight_percent, 2) }}%</td>
                    <td style="text-align: center; font-weight: bold;">{{ $dt->score_input }}</td>
                    <td style="text-align: center;">{{ number_format($dt->converted_value, 0) }}</td>
                    <td style="text-align: center; font-weight: bold; color: #0f2c59;">{{ number_format($dt->weighted_score, 2) }}</td>
                    <td style="font-size: 8pt; color: #334155;">{{ $dt->indicator_snapshot }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e2e8f0; font-weight: bold;">
                <td colspan="5" style="text-align: right; padding: 6px;">TOTAL NILAI AKHIR BERBOBOT:</td>
                <td style="text-align: center; color: #0f2c59; font-size: 10pt;">{{ number_format($appraisal->total_score, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- Tanda Tangan -->
    <table class="footer-sign">
        <tr>
            <td style="width: 50%;">
                Menyetujui,<br>
                <strong>Management / Evaluator</strong><br><br><br><br>
                ( <strong>{{ $appraisal->evaluator->name ?? 'Evaluator' }}</strong> )
            </td>
            <td style="width: 50%;">
                Pegawai Konsultan IT,<br><br><br><br><br>
                ( <strong>{{ $appraisal->user->name ?? 'Pegawai' }}</strong> )
            </td>
        </tr>
    </table>

</body>
</html>
