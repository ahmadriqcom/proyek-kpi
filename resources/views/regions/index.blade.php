@extends('layouts.app')

@section('title', 'Master Daerah (Superadmin)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-geo-alt me-2"></i>Master Daerah & Wilayah (Superadmin Only)</h4>
        <p class="text-muted small mb-0">Halaman pengelolaan data master daerah operasional.</p>
    </div>
    <a href="{{ route('regions.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm fw-semibold">
        <i class="bi bi-plus-circle-fill me-1"></i> Tambah Daerah Baru
    </a>
</div>

<div class="card card-gov shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-spreadsheet align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;">No</th>
                        <th style="width: 15%;">Kode Daerah</th>
                        <th style="width: 25%;">Nama Daerah / Kota</th>
                        <th style="width: 20%;">Provinsi</th>
                        <th style="width: 25%;">Uraian Provinsi</th>
                        <th style="width: 10%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($regions as $index => $reg)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $regions->firstItem() + $index }}</td>
                            <td><span class="badge bg-light text-dark border font-monospace fs-6">{{ $reg->code }}</span></td>
                            <td class="fw-bold text-primary">{{ $reg->name }}</td>
                            <td>{{ $reg->province ?? '-' }}</td>
                            <td class="small text-muted">{{ $reg->uraian_provinsi ?? '-' }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('regions.edit', $reg->id) }}" class="btn btn-outline-warning" title="Edit Daerah">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('regions.destroy', $reg->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus daerah ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus Daerah">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data Master Daerah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($regions->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $regions->links() }}
        </div>
    @endif
</div>
@endsection
