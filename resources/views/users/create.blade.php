@extends('layouts.app')

@section('title', 'Buat Username / User Baru (Superadmin)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-primary-dark"><i class="bi bi-person-plus-fill me-2"></i>Buat Username & Matriks Hak Akses User</h4>
        <p class="text-muted small mb-0">Atur NIK pegawai, kredensial akun, dan centang matriks hak akses granular per menu.</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar User
    </a>
</div>

<form action="{{ route('users.store') }}" method="POST">
    @csrf

    <div class="row g-4 mb-4">
        <!-- User Identitas Card -->
        <div class="col-md-5">
            <div class="card card-gov shadow-sm h-100">
                <div class="card-header bg-light">
                    <span class="fw-bold text-dark"><i class="bi bi-person-badge me-2 text-primary"></i>Kredensial & Profile User</span>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label for="nik" class="form-label fw-bold text-dark">NIK Pegawai (Nomor Induk Karyawan) <span class="text-danger">*</span></label>
                        <input type="text" name="nik" id="nik" class="form-control @error('nik') is-invalid @enderror" placeholder="Contoh: 202607 (Maks 10 digit)" value="{{ old('nik') }}" required maxlength="10">
                        <small class="text-muted d-block mt-1">NIK digunakan dalam rumus Nomor Registrasi KPI (<code>KPI-{NIK}-{APP}-{REG}-{NO}</code>). Maksimal 10 digit angka.</small>
                        @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label fw-bold text-dark">Username Login <span class="text-danger">*</span></label>
                        <input type="text" name="username" id="username" class="form-control @error('username') is-invalid @enderror" placeholder="Contoh: operator.c" value="{{ old('username') }}" required>
                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama lengkap..." value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold text-dark">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email..." value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold text-dark">Password Awal <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password min 6 karakter..." required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label for="role" class="form-label fw-bold text-dark">Role Akses <span class="text-danger">*</span></label>
                            <select name="role" id="role" class="form-select fw-semibold" required>
                                <option value="operator" {{ old('role') === 'operator' ? 'selected' : '' }}>Operator</option>
                                <option value="management" {{ old('role') === 'management' ? 'selected' : '' }}>Management</option>
                                <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Superadmin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="grade_level" class="form-label fw-bold text-dark">Grade Level <span class="text-danger">*</span></label>
                            <select name="grade_level" id="grade_level" class="form-select fw-semibold" required>
                                @for($g = 1; $g <= 9; $g++)
                                    <option value="{{ $g }}" {{ old('grade_level') == $g ? 'selected' : '' }}>Grade {{ $g }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matriks Hak Akses Checkbox Per Menu -->
        <div class="col-md-7">
            <div class="card card-gov shadow-sm h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark"><i class="bi bi-shield-lock-fill me-2 text-success"></i>Matriks Hak Akses Granular Per Menu</span>
                    <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 fw-semibold" id="checkAllBtn">Centang Semua</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width: 35%;">Nama Menu Sistem</th>
                                    <th style="width: 13%; text-align: center;">Lihat</th>
                                    <th style="width: 13%; text-align: center;">Tambah</th>
                                    <th style="width: 13%; text-align: center;">Ubah</th>
                                    <th style="width: 13%; text-align: center;">Hapus</th>
                                    <th style="width: 13%; text-align: center;">Cetak</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $menuNames = [
                                        'dashboard' => 'Dashboard Executive KPI',
                                        'kpi_reports' => 'Data Laporan KPI Mingguan',
                                        'appraisals' => 'Penilaian KPI Pegawai',
                                        'grade_schemes' => 'Master Skema Grade',
                                        'applications' => 'Master Aplikasi',
                                        'regions' => 'Master Daerah',
                                        'app_region_mappings' => 'Master Data Mapping Aplikasi & Daerah',
                                        'users' => 'Manajemen User & Hak Akses',
                                    ];
                                @endphp

                                @foreach($menuNames as $mKey => $mTitle)
                                    @php
                                        $group = $permissions->get($mKey, collect());
                                        $pRead = $group->where('action_key', 'read')->first();
                                        $pCreate = $group->where('action_key', 'create')->first();
                                        $pUpdate = $group->where('action_key', 'update')->first();
                                        $pDelete = $group->where('action_key', 'delete')->first();
                                        $pPrint = $group->where('action_key', 'print')->first();
                                    @endphp
                                    <tr>
                                        <td class="fw-semibold text-primary small">{{ $mTitle }}</td>
                                        <td class="text-center">
                                            @if($pRead)
                                                <input type="checkbox" name="permissions[]" value="{{ $pRead->id }}" data-menu="{{ $mKey }}" data-action="read" class="form-check-input perm-checkbox" checked>
                                            @else - @endif
                                        </td>
                                        <td class="text-center">
                                            @if($pCreate)
                                                <input type="checkbox" name="permissions[]" value="{{ $pCreate->id }}" data-menu="{{ $mKey }}" data-action="create" class="form-check-input perm-checkbox">
                                            @else - @endif
                                        </td>
                                        <td class="text-center">
                                            @if($pUpdate)
                                                <input type="checkbox" name="permissions[]" value="{{ $pUpdate->id }}" data-menu="{{ $mKey }}" data-action="update" class="form-check-input perm-checkbox">
                                            @else - @endif
                                        </td>
                                        <td class="text-center">
                                            @if($pDelete)
                                                <input type="checkbox" name="permissions[]" value="{{ $pDelete->id }}" data-menu="{{ $mKey }}" data-action="delete" class="form-check-input perm-checkbox">
                                            @else - @endif
                                        </td>
                                        <td class="text-center">
                                            @if($pPrint)
                                                <input type="checkbox" name="permissions[]" value="{{ $pPrint->id }}" data-menu="{{ $mKey }}" data-action="print" class="form-check-input perm-checkbox">
                                            @else - @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-gov shadow-sm">
        <div class="card-body p-3 d-flex justify-content-between align-items-center">
            <span class="text-muted small"><i class="bi bi-shield-check text-success me-1"></i> Hak akses akan langsung ter-enforce di seluruh tombol, menu, dan API controller.</span>
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                <i class="bi bi-check-circle-fill me-1"></i> Simpan User & Matriks Hak Akses
            </button>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const allCheckboxes = document.querySelectorAll('.perm-checkbox');
        const checkAllBtn = document.getElementById('checkAllBtn');

        function applyRolePermissions(role) {
            if (role === 'super_admin' || role === 'superadmin') {
                allCheckboxes.forEach(cb => {
                    cb.checked = true;
                });
            } else if (role === 'management') {
                allCheckboxes.forEach(cb => {
                    const menu = cb.dataset.menu;
                    const action = cb.dataset.action;
                    if (action === 'read' || action === 'print' || (menu === 'appraisals' && (action === 'create' || action === 'update')) || (menu === 'kpi_reports' && (action === 'create' || action === 'update'))) {
                        cb.checked = true;
                    } else {
                        cb.checked = false;
                    }
                });
            } else if (role === 'operator') {
                allCheckboxes.forEach(cb => {
                    const menu = cb.dataset.menu;
                    const action = cb.dataset.action;
                    if ((menu === 'dashboard' || menu === 'kpi_reports' || menu === 'appraisals') && (action === 'read' || action === 'create' || action === 'update' || action === 'print')) {
                        cb.checked = true;
                    } else {
                        cb.checked = false;
                    }
                });
            }
        }

        if (roleSelect) {
            roleSelect.addEventListener('change', function() {
                applyRolePermissions(this.value);
            });
            applyRolePermissions(roleSelect.value);
        }

        if (checkAllBtn) {
            checkAllBtn.addEventListener('click', function() {
                const allChecked = Array.from(allCheckboxes).every(c => c.checked);
                allCheckboxes.forEach(c => c.checked = !allChecked);
                this.innerText = allChecked ? 'Centang Semua' : 'Hapus Semua Centang';
            });
        }
    });
</script>
@endsection
