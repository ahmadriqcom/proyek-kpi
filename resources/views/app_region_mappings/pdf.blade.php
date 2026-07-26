<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Mapping Aplikasi & Daerah</title>
    <style>
        body { font-family: sans-serif; font-size: 11pt; color: #333; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #004085; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #004085; text-transform: uppercase; font-size: 16pt; }
        .header p { margin: 4px 0 0; font-size: 10pt; color: #666; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #cce5ff; padding: 8px; font-size: 9.5pt; text-align: left; }
        .table th { background-color: #004085; color: #ffffff; font-weight: bold; text-align: center; }
        .table-striped tbody tr:nth-of-type(odd) { background-color: #f8f9fa; }
        .text-center { text-align: center !important; }
        .footer { margin-top: 30px; text-align: right; font-size: 9pt; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h2>RINGKASAN MASTER MAPPING APLIKASI & DAERAH</h2>
        <p>Sistem Informasi Laporan KPI Pegawai & Konsultan IT | Cetak Tanggal: {{ date('d-m-Y H:i') }}</p>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 32%;">Kode & Nama Aplikasi</th>
                <th style="width: 32%;">Kode & Nama Daerah / Kota</th>
                <th style="width: 28%;">Uraian Provinsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mappings as $idx => $map)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>[{{ $map->application->code ?? '-' }}]</strong> {{ $map->application->name ?? '-' }}</td>
                    <td><strong>[{{ $map->region->code ?? '-' }}]</strong> {{ $map->region->name ?? '-' }}</td>
                    <td>{{ $map->region->uraian_provinsi ?? ($map->region->province ?? '-') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini dihasilkan secara otomatis oleh Sistem Laporan KPI Pegawai.</p>
    </div>
</body>
</html>
