@extends('layouts.app')

@section('title', 'Edit Mapping Aplikasi & Daerah')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<style>
    .choices__inner {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
        min-height: 42px;
    }
    .choices__focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-pencil-square me-2"></i>Edit Relasi Mapping Aplikasi & Daerah</h4>
        <p class="text-muted small mb-0">Ubah relasi terhubung antara Master Aplikasi dan Master Daerah.</p>
    </div>
    <a href="{{ route('app-region-mappings.index') }}" class="btn btn-outline-secondary btn-sm px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card card-gov shadow-sm">
            <div class="card-header bg-light">
                <span class="fw-bold text-dark"><i class="bi bi-pencil-fill me-2 text-warning"></i>Form Edit Mapping</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('app-region-mappings.update', $mapping->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="application_id" class="form-label fw-bold text-dark">1. Master Aplikasi <span class="text-danger">*</span></label>
                        <select name="application_id" id="application_id" class="form-select @error('application_id') is-invalid @enderror" required>
                            @foreach($applications as $app)
                                <option value="{{ $app->id }}" {{ old('application_id', $mapping->application_id) == $app->id ? 'selected' : '' }}>
                                    {{ $app->name }} ({{ $app->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('application_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="region_id" class="form-label fw-bold text-dark">2. Master Daerah <span class="text-danger">*</span></label>
                        <select name="region_id" id="region_id" class="form-select @error('region_id') is-invalid @enderror" required>
                            @foreach($regions as $reg)
                                <option value="{{ $reg->id }}" {{ old('region_id', $mapping->region_id) == $reg->id ? 'selected' : '' }}>
                                    {{ $reg->name }} ({{ $reg->code }}) {{ $reg->province ? '- ' . $reg->province : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('region_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('app-region-mappings.index') }}" class="btn btn-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-warning px-4 fw-semibold">
                            <i class="bi bi-save me-1"></i> Perbarui Mapping
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Choices('#application_id', {
            searchEnabled: true,
            searchPlaceholderValue: 'Ketik nama/kode aplikasi...',
            itemSelectText: 'Pilih',
            noResultsText: 'Aplikasi tidak ditemukan',
        });

        new Choices('#region_id', {
            searchEnabled: true,
            searchPlaceholderValue: 'Ketik nama/kode daerah...',
            itemSelectText: 'Pilih',
            noResultsText: 'Daerah tidak ditemukan',
        });
    });
</script>
@endpush
