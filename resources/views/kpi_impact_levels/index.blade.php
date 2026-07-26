@extends('layouts.app')

@section('title', 'Master Tingkat Dampak (Impact Level)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-diagram-2-fill me-2"></i>Master Tingkat Dampak (Impact Level)</h4>
        <p class="text-muted small mb-0">Konfigurasi jangkauan dampak kendala terhadap organisasi dan bobot pengalinya.</p>
    </div>
    <button type="button" class="btn btn-primary btn-sm px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#createImpactModal">
        <i class="bi bi-plus-circle-fill me-1"></i> Tambah Dampak Baru
    </button>
</div>

<div class="card card-gov shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-spreadsheet align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%; text-align: center;">No</th>
                        <th style="width: 25%;">Nama Tingkat Dampak</th>
                        <th style="width: 45%;">Deskripsi Jangkauan Dampak (Tooltip)</th>
                        <th style="width: 15%; text-align: center;">Bobot Dampak</th>
                        <th style="width: 10%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($impacts as $idx => $imp)
                        <tr>
                            <td class="text-center font-monospace">{{ $idx + 1 }}</td>
                            <td><strong class="text-primary fs-6">{{ $imp->name }}</strong></td>
                            <td class="small text-muted">{{ $imp->description }}</td>
                            <td class="text-center"><span class="badge bg-success px-3 py-2 fs-6">× {{ number_format($imp->impact_weight, 2) }}</span></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-warning btn-sm py-0 px-2 me-1" data-bs-toggle="modal" data-bs-target="#editImpModal{{ $imp->id }}"><i class="bi bi-pencil-square"></i> Edit</button>
                                <form action="{{ route('kpi-impact-levels.destroy', $imp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus dampak {{ $imp->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="editImpModal{{ $imp->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('kpi-impact-levels.update', $imp->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-2 text-warning"></i>Ubah Dampak [{{ $imp->name }}]</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Tingkat Dampak:</label>
                                                <input type="text" name="name" class="form-control" value="{{ old('name', $imp->name) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Deskripsi Jangkauan Dampak (Tampil di Tooltip):</label>
                                                <textarea name="description" class="form-control" rows="3" required>{{ old('description', $imp->description) }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Bobot Dampak (Pengali Composite):</label>
                                                <input type="number" step="0.05" name="impact_weight" class="form-control" value="{{ old('impact_weight', $imp->impact_weight) }}" min="0.1" max="5.0" required>
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
<div class="modal fade" id="createImpactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kpi-impact-levels.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-plus-circle-fill me-2 text-primary"></i>Tambah Tingkat Dampak Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Tingkat Dampak:</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Regional" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Jangkauan Dampak (Tampil di Tooltip):</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Tuliskan jangkauan organisasi..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bobot Dampak (Misal: 1.20):</label>
                        <input type="number" step="0.05" name="impact_weight" class="form-control" value="1.00" min="0.1" max="5.0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i> Simpan Dampak Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
