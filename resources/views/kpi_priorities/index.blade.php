@extends('layouts.app')

@section('title', 'Master Tingkat Prioritas & Target SLA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-exclamation-triangle-fill me-2"></i>Master Tingkat Prioritas & Target SLA</h4>
        <p class="text-muted small mb-0">Konfigurasi bobot urgensi, target batas SLA penyelesaian, dan indikator prioritas.</p>
    </div>
    <button type="button" class="btn btn-primary btn-sm px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#createPriorityModal">
        <i class="bi bi-plus-circle-fill me-1"></i> Tambah Prioritas Baru
    </button>
</div>

<div class="card card-gov shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-spreadsheet align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%; text-align: center;">No</th>
                        <th style="width: 20%;">Nama Prioritas</th>
                        <th style="width: 40%;">Deskripsi Definisi (Tooltip)</th>
                        <th style="width: 15%; text-align: center;">Bobot Urgensi</th>
                        <th style="width: 10%; text-align: center;">Target SLA</th>
                        <th style="width: 10%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($priorities as $idx => $prio)
                        <tr>
                            <td class="text-center font-monospace">{{ $idx + 1 }}</td>
                            <td><strong class="text-primary fs-6">{{ $prio->name }}</strong></td>
                            <td class="small text-muted">{{ $prio->description }}</td>
                            <td class="text-center"><span class="badge bg-warning text-dark px-3 py-2 fs-6">× {{ number_format($prio->urgency_weight, 2) }}</span></td>
                            <td class="text-center fw-bold text-dark"><i class="bi bi-stopwatch text-info me-1"></i> {{ $prio->target_sla_days }} Hari</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-outline-warning btn-sm py-0 px-2 me-1" data-bs-toggle="modal" data-bs-target="#editPrioModal{{ $prio->id }}"><i class="bi bi-pencil-square"></i> Edit</button>
                                <form action="{{ route('kpi-priorities.destroy', $prio->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus prioritas {{ $prio->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="editPrioModal{{ $prio->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('kpi-priorities.update', $prio->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-2 text-warning"></i>Ubah Prioritas [{{ $prio->name }}]</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Nama Prioritas:</label>
                                                <input type="text" name="name" class="form-control" value="{{ old('name', $prio->name) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Deskripsi Definisi (Tampil di Tooltip):</label>
                                                <textarea name="description" class="form-control" rows="3" required>{{ old('description', $prio->description) }}</textarea>
                                            </div>
                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Bobot Urgensi:</label>
                                                    <input type="number" step="0.05" name="urgency_weight" class="form-control" value="{{ old('urgency_weight', $prio->urgency_weight) }}" min="0.1" max="5.0" required>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-semibold">Target SLA (Hari):</label>
                                                    <input type="number" name="target_sla_days" class="form-control" value="{{ old('target_sla_days', $prio->target_sla_days) }}" min="1" max="30" required>
                                                </div>
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
<div class="modal fade" id="createPriorityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kpi-priorities.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-plus-circle-fill me-2 text-primary"></i>Tambah Prioritas Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Prioritas:</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Urgent" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi Definisi (Tampil di Tooltip):</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Tuliskan definisi tingkat urgensi..." required></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Bobot Urgensi:</label>
                            <input type="number" step="0.05" name="urgency_weight" class="form-control" value="1.00" min="0.1" max="5.0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Target SLA (Hari):</label>
                            <input type="number" name="target_sla_days" class="form-control" value="2" min="1" max="30" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i> Simpan Prioritas Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
