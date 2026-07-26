<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Skema Penilaian Grade KPI Pegawai (Grade 1 - 9)</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; margin: 15px; }
        .header { text-align: center; border-bottom: 2px solid #004085; padding-bottom: 8px; margin-bottom: 15px; }
        .header h2 { margin: 0; color: #004085; text-transform: uppercase; font-size: 14pt; }
        .header p { margin: 3px 0 0; font-size: 9pt; color: #666; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table th, .table td { border: 1px solid #cce5ff; padding: 6px; font-size: 8.5pt; text-align: left; }
        .table th { background-color: #004085; color: #ffffff; font-weight: bold; text-align: center; }
        .table-striped tbody tr:nth-of-type(odd) { background-color: #f8f9fa; }
        .text-center { text-align: center !important; }
        .badge { display: inline-block; padding: 2px 5px; font-size: 8pt; font-weight: bold; color: #fff; background-color: #004085; border-radius: 3px; }
        .footer { margin-top: 20px; text-align: right; font-size: 8.5pt; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h2>RINGKASAN SKEMA PENILAIAN GRADE KONSULTAN IT (GRADE 1 - 9)</h2>
        <p>Sistem Informasi Laporan KPI Pegawai & Konsultan IT | Cetak Tanggal: {{ date('d-m-Y H:i') }}</p>
    </div>

    <h4 style="color: #004085; margin-bottom: 6px;">1. Master Daftar Grade (Grade 1 s.d Grade 9) & Career Path</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th style="width: 8%;">Kode</th>
                <th style="width: 15%;">Nama Grade</th>
                <th style="width: 22%;">Career Path</th>
                <th style="width: 40%;">Deskripsi Kompetensi</th>
                <th style="width: 15%;">Total Bobot</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grades as $grade)
                <tr>
                    <td class="text-center font-monospace"><strong>{{ $grade->kode_grade }}</strong></td>
                    <td><strong>{{ $grade->nama_grade }}</strong></td>
                    <td><strong>{{ $grade->career_path ?? '-' }}</strong></td>
                    <td>{{ $grade->deskripsi_kompetensi ?? '-' }}</td>
                    <td class="text-center"><strong>{{ number_format($grade->weights->sum('weight_percent'), 2) }}%</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4 style="color: #004085; margin-bottom: 6px;">2. Master 8 Kriteria Penilaian</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 14%;">Kode</th>
                <th style="width: 25%;">Nama Kriteria</th>
                <th style="width: 40%;">Deskripsi Indikator Utama</th>
                <th style="width: 15%;">Bobot Default</th>
            </tr>
        </thead>
        <tbody>
            @foreach($criterias as $idx => $crit)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center"><strong>{{ $crit->kode_kriteria }}</strong></td>
                    <td><strong>{{ $crit->nama_kriteria }}</strong></td>
                    <td>{{ $crit->deskripsi }}</td>
                    <td class="text-center"><strong>{{ number_format($crit->bobot_default, 2) }}%</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4 style="color: #004085; margin-bottom: 6px;">3. Skala Konversi Skor & Predikat Kinerja</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th style="width: 25%;">Rentang Nilai Akhir</th>
                <th style="width: 25%;">Predikat</th>
                <th style="width: 50%;">Rekomendasi Tindak Lanjut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($predicates as $pred)
                <tr>
                    <td class="text-center"><strong>{{ number_format($pred->min_score, 0) }} s.d {{ number_format($pred->max_score, 0) }}</strong></td>
                    <td><strong>{{ $pred->predicate }}</strong></td>
                    <td>{{ $pred->recommendation }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini dihasilkan secara otomatis oleh Sistem Laporan KPI Pegawai.</p>
    </div>
</body>
</html>
