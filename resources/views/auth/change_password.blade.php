@extends('layouts.app')

@section('title', 'Ubah Password Akun')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-key-fill me-2"></i>Ubah Password Akun Pengguna</h4>
        <p class="text-muted small mb-0">Fitur keamanan akun untuk memperbarui password Anda secara berkala.</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-gov shadow-sm">
            <div class="card-header bg-light">
                <span class="fw-bold text-dark"><i class="bi bi-shield-lock me-2 text-warning"></i>Form Pembaruan Password</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-bold text-dark">Password Lama <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold text-dark">Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-bold text-dark">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-top pt-3">
                        <span class="small text-muted"><i class="bi bi-info-circle me-1"></i> Ganti password demi keamanan akun</span>
                        <button type="submit" class="btn btn-warning px-4 fw-semibold">
                            <i class="bi bi-save-fill me-1"></i> Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
