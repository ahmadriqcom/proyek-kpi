@extends('layouts.app')

@section('title', 'Manajemen User & Hak Akses (Superadmin)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-people-fill me-2"></i>Manajemen User & Pembuatan Username (Superadmin)</h4>
        <p class="text-muted small mb-0">Halaman terpusat pembuatan username baru, NIK pegawai, dan pengaturan role pengakses sistem.</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm fw-semibold">
        <i class="bi bi-person-plus-fill me-1"></i> Buat Username / User Baru
    </a>
</div>

<div class="card card-gov shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-spreadsheet align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;">No</th>
                        <th style="width: 15%;">NIK Pegawai</th>
                        <th style="width: 15%;">Username Login</th>
                        <th style="width: 25%;">Nama Lengkap</th>
                        <th style="width: 15%;">Email</th>
                        <th style="width: 10%;">Role Akses</th>
                        <th style="width: 5%; text-align: center;">Grade</th>
                        <th style="width: 10%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $usr)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $users->firstItem() + $index }}</td>
                            <td><span class="badge bg-primary-subtle text-primary border font-monospace fs-6">{{ $usr->nik ?? '-' }}</span></td>
                            <td><span class="badge bg-light text-dark border font-monospace fs-6">{{ $usr->username ?? 'N/A' }}</span></td>
                            <td class="fw-bold text-primary">{{ $usr->name }}</td>
                            <td class="small">{{ $usr->email }}</td>
                            <td>
                                @if($usr->isSuperAdmin())
                                    <span class="badge bg-danger">Superadmin</span>
                                @elseif($usr->isManagement())
                                    <span class="badge bg-primary">Management</span>
                                @else
                                    <span class="badge bg-secondary">Operator</span>
                                @endif
                            </td>
                            <td class="text-center fw-bold text-dark">G{{ $usr->grade_level }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('users.edit', $usr->id) }}" class="btn btn-outline-warning" title="Edit User">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    @if($usr->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $usr->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus User">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
