@extends('layouts.app')

@section('title', 'Master Aplikasi (Superadmin)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-app-indicator me-2"></i>Master Aplikasi (Superadmin Only)</h4>
        <p class="text-muted small mb-0">Halaman pengelolaan data master aplikasi sistem laporan KPI.</p>
    </div>
    <a href="{{ route('applications.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm fw-semibold">
        <i class="bi bi-plus-circle-fill me-1"></i> Tambah Aplikasi Baru
    </a>
</div>

<div class="card card-gov shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-spreadsheet align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;">No</th>
                        <th style="width: 15%;">Kode Aplikasi</th>
                        <th style="width: 30%;">Nama Aplikasi</th>
                        <th style="width: 30%;">Deskripsi</th>
                        <th style="width: 10%; text-align: center;">Status</th>
                        <th style="width: 10%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $index => $app)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $applications->firstItem() + $index }}</td>
                            <td><span class="badge bg-light text-dark border font-monospace fs-6">{{ $app->code }}</span></td>
                            <td class="fw-bold text-primary">{{ $app->name }}</td>
                            <td class="small text-muted">{{ $app->description ?? '-' }}</td>
                            <td class="text-center">
                                @if($app->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('applications.edit', $app->id) }}" class="btn btn-outline-warning" title="Edit Aplikasi">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('applications.destroy', $app->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus aplikasi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus Aplikasi">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data Master Aplikasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($applications->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection
