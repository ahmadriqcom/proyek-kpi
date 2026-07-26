@extends('layouts.app')

@section('title', 'Master Data Mapping Aplikasi & Daerah')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-diagram-3-fill me-2 text-primary"></i>Master Data Mapping Aplikasi & Daerah</h4>
        <p class="text-muted small mb-0">Pengelolaan relasi terdaftar antara Master Aplikasi dan Master Daerah operasional.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('app-region-mappings.export-pdf') }}" class="btn btn-outline-danger btn-sm px-3 fw-semibold">
            <i class="bi bi-file-pdf-fill me-1"></i> Cetak PDF Mapping
        </a>
        <a href="{{ route('app-region-mappings.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm fw-semibold">
            <i class="bi bi-plus-circle-fill me-1"></i> Tambah Mapping Baru
        </a>
    </div>
</div>

<!-- Filter Box -->
<div class="card card-gov mb-4 shadow-sm">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('app-region-mappings.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama aplikasi / daerah..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="application_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Filter Berdasarkan Aplikasi --</option>
                    @foreach($applications as $app)
                        <option value="{{ $app->id }}" {{ request('application_id') == $app->id ? 'selected' : '' }}>
                            {{ $app->name }} ({{ $app->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button type="submit" class="btn btn-secondary btn-sm w-50">Filter</button>
                <a href="{{ route('app-region-mappings.index') }}" class="btn btn-outline-secondary btn-sm w-50">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-gov shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-spreadsheet align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;">No</th>
                        <th style="width: 30%;">Master Aplikasi</th>
                        <th style="width: 30%;">Master Daerah / Kota</th>
                        <th style="width: 25%;">Uraian Provinsi</th>
                        <th style="width: 10%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mappings as $index => $map)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $mappings->firstItem() + $index }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $map->application->name ?? '-' }}</div>
                                <small class="text-muted font-monospace">{{ $map->application->code ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">{{ $map->region->name ?? '-' }}</div>
                                <small class="text-muted font-monospace">{{ $map->region->code ?? '-' }}</small>
                            </td>
                            <td class="small text-muted">{{ $map->region->uraian_provinsi ?? ($map->region->province ?? '-') }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('app-region-mappings.edit', $map->id) }}" class="btn btn-outline-warning" title="Edit Mapping">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('app-region-mappings.destroy', $map->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus relasi mapping ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus Mapping">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data relasi Mapping Aplikasi & Daerah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($mappings->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $mappings->links() }}
        </div>
    @endif
</div>
@endsection
