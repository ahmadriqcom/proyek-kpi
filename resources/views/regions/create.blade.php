@extends('layouts.app')

@section('title', 'Tambah Master Daerah')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Master Daerah Baru</h4>
        <p class="text-muted small mb-0">Lengkapi formulir untuk menambahkan master data daerah operasional.</p>
    </div>
    <a href="{{ route('regions.index') }}" class="btn btn-outline-secondary btn-sm px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-gov shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('regions.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="code" class="form-label fw-bold text-dark">Kode Daerah <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="Contoh: REG-001 / BTM" required>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold text-dark">Nama Daerah / Kota <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Kota Batam / BANK B J B" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="province" class="form-label fw-bold text-dark">Provinsi (Opsional)</label>
                        <input type="text" name="province" id="province" class="form-control @error('province') is-invalid @enderror" value="{{ old('province') }}" placeholder="Contoh: Kepulauan Riau">
                        @error('province') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="uraian_provinsi" class="form-label fw-bold text-dark">Uraian Provinsi (Opsional)</label>
                        <textarea name="uraian_provinsi" id="uraian_provinsi" rows="3" class="form-control @error('uraian_provinsi') is-invalid @enderror" placeholder="Catatan / uraian detail mengenai provinsi atau wilayah operasional daerah...">{{ old('uraian_provinsi') }}</textarea>
                        @error('uraian_provinsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        <i class="bi bi-save-fill me-1"></i> Simpan Master Daerah
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
