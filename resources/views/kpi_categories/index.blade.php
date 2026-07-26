@extends('layouts.app')

@section('title', 'Master Kategori Kendala & Bobot Kompleksitas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-tags-fill me-2"></i>Master Kategori Kendala & Bobot Kompleksitas</h4>
        <p class="text-muted small mb-0">Konfigurasi parameter kategori, bobot kompleksitas pekerjaan, dan pemicu approval management.</p>
    </div>
    <button type="button" class="btn btn-primary btn-sm px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
        <i class="bi bi-plus-circle-fill me-1"></i> Tambah Kategori Baru
    </button>
</div>

<div class="card card-gov shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-spreadsheet align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%; text-align: center;">No</th>
                        <th style="width: 25%;">Nama Kategori Kendala</th>
                        <th style="width: 35%;">Deskripsi Standar Klasifikasi</th>
                        <th style="width: 15%; text-align: center;">Bobot Kompleksitas</th>
                        <th style="width: 10%; text-align: center;">Need Approval</th>
                        <th style="width: 10%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $idx => $cat)
                        <tr>
                            <td class="text-center font-monospace">{{ $idx + 1 }}</td>
                            <td><strong class="text-primary fs-6">{{ $cat->name }}</strong></td>
                            <td class="small text-muted">{{ $cat->description }}</td>
                            <td class="text-center"><span class="badge bg-info text-dark px-3 py-2 fs-6">× {{ number_format($cat->complexity_weight, 2) }}</span></td>
                            <td class="text-center">
                                @if($cat->requires_approval)
                                    <span class="badge bg-warning text-dark"><i class="bi bi-shield-lock-fill me-1"></i> Ya</span>
                                @else
                                    <span class="badge bg-light text-muted border">Tidak</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-warning btn-sm py-0 px-2 me-1" data-bs-toggle="modal" data-bs-target="#editCatModal{{ $cat->id }}"><i class="bi bi-pencil-square"></i> Edit</button>
                                <form action="{{ route('kpi-categories.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kategori {{ $cat->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="editCatModal{{ $cat->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('kpi-categories.update', $cat->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-2 text-warning"></i>Ubah Kategori [{{ $cat->name }}]</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Kategori:</label>
                                                <input type="text" name="name" class="form-control" value="{{ old('name', $cat->name) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Deskripsi Definisional (Tampil di Tooltip):</label>
                                                <textarea name="description" class="form-control" rows="3" required>{{ old('description', $cat->description) }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Bobot Kompleksitas (Pengali Composite):</label>
                                                <input type="number" step="0.05" name="complexity_weight" class="form-control" value="{{ old('complexity_weight', $cat->complexity_weight) }}" min="0.1" max="5.0" required>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" name="requires_approval" id="requires_approval_{{ $cat->id }}" class="form-check-input" value="1" {{ $cat->requires_approval ? 'checked' : '' }}>
                                                <label for="requires_approval_{{ $cat->id }}" class="form-check-label fw-semibold">Pemicu Mandatory Approval Management</label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" name="is_active" id="is_active_{{ $cat->id }}" class="form-check-input" value="1" {{ $cat->is_active ? 'checked' : '' }}>
                                                <label for="is_active_{{ $cat->id }}" class="form-check-label fw-semibold">Status Aktif</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kpi-categories.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-plus-circle-fill me-2 text-primary"></i>Tambah Kategori Kendala Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori:</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Modul Keuangan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Definisional (Tampil di Tooltip):</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Tuliskan definisi standar klasifikasi kategori ini..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bobot Kompleksitas (Misal: 1.20):</label>
                        <input type="number" step="0.05" name="complexity_weight" class="form-control" value="1.00" min="0.1" max="5.0" required>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="requires_approval" id="requires_approval_new" class="form-check-input" value="1">
                        <label for="requires_approval_new" class="form-check-label fw-semibold">Pemicu Mandatory Approval Management</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active_new" class="form-check-input" value="1" checked>
                        <label for="is_active_new" class="form-check-label fw-semibold">Status Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i> Simpan Kategori Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
