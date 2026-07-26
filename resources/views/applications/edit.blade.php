@extends('layouts.app')

@section('title', 'Edit Master Aplikasi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-pencil-square me-2"></i>Edit Master Aplikasi [{{ $application->code }}]</h4>
        <p class="text-muted small mb-0">Perbarui informasi data master aplikasi.</p>
    </div>
    <a href="{{ route('applications.index') }}" class="btn btn-outline-secondary btn-sm px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-gov shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('applications.update', $application->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="code" class="form-label fw-bold text-dark">Kode Aplikasi <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $application->code) }}" required>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold text-dark">Nama Lengkap Aplikasi <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $application->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold text-dark">Deskripsi</label>
                        <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $application->description) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="is_active" class="form-label fw-bold text-dark">Status Operasional <span class="text-danger">*</span></label>
                        <select name="is_active" id="is_active" class="form-select fw-semibold" required>
                            <option value="1" {{ old('is_active', $application->is_active) ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !old('is_active', $application->is_active) ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-semibold">
                        <i class="bi bi-save-fill me-1"></i> Perbarui Master Aplikasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
