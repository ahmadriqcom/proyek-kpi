@extends('layouts.app')

@section('title', 'Edit Master Daerah')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-pencil-square me-2"></i>Edit Master Daerah [{{ $region->code }}]</h4>
        <p class="text-muted small mb-0">Perbarui informasi master data daerah.</p>
    </div>
    <a href="{{ route('regions.index') }}" class="btn btn-outline-secondary btn-sm px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-gov shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('regions.update', $region->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="code" class="form-label fw-bold text-dark">Kode Daerah <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $region->code) }}" required>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold text-dark">Nama Daerah / Kota <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $region->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="province" class="form-label fw-bold text-dark">Provinsi</label>
                        <input type="text" name="province" id="province" class="form-control @error('province') is-invalid @enderror" value="{{ old('province', $region->province) }}">
                        @error('province') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="uraian_provinsi" class="form-label fw-bold text-dark">Uraian Provinsi</label>
                        <textarea name="uraian_provinsi" id="uraian_provinsi" rows="3" class="form-control @error('uraian_provinsi') is-invalid @enderror" placeholder="Catatan / uraian detail mengenai provinsi...">{{ old('uraian_provinsi', $region->uraian_provinsi) }}</textarea>
                        @error('uraian_provinsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-semibold">
                        <i class="bi bi-save-fill me-1"></i> Perbarui Master Daerah
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
