<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Laporan KPI</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f2c59 0%, #1b365d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .login-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 440px;
        }

        .login-header {
            background-color: #0f2c59;
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }

        .login-header h5 {
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .btn-login {
            background-color: #0f2c59;
            border-color: #0f2c59;
            color: #ffffff;
            font-weight: 600;
            padding: 10px;
        }

        .btn-login:hover {
            background-color: #1b365d;
            border-color: #1b365d;
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <i class="bi bi-shield-lock-fill fs-1 text-warning mb-2 d-block"></i>
        <h5>SISTEM LAPORAN KPI</h5>
        <small class="text-light opacity-75">Otomatisasi Laporan Mingguan & Appraisal Konsultan IT</small>
    </div>

    <div class="card-body p-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show small" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- 1. Username Field -->
            <div class="mb-3">
                <label for="username" class="form-label fw-bold text-dark small">Username <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person-fill text-secondary"></i></span>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username akun..." value="{{ old('username') }}" required autofocus>
                </div>
            </div>

            <!-- 2. Password Field -->
            <div class="mb-3">
                <label for="password" class="form-label fw-bold text-dark small">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-key-fill text-secondary"></i></span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password..." required>
                </div>
            </div>

            <!-- 3. Tahun Anggaran / Periode Field -->
            <div class="mb-4">
                <label for="year" class="form-label fw-bold text-dark small">Tahun Anggaran / Periode Laporan <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-calendar-check-fill text-secondary"></i></span>
                    <select name="year" id="year" class="form-select fw-semibold" required>
                        <option value="2026" {{ old('year', date('Y')) == '2026' ? 'selected' : '' }}>2026 (Tahun Berjalan)</option>
                        <option value="2025" {{ old('year') == '2025' ? 'selected' : '' }}>2025</option>
                        <option value="2024" {{ old('year') == '2024' ? 'selected' : '' }}>2024</option>
                        <option value="2027" {{ old('year') == '2027' ? 'selected' : '' }}>2027</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3 shadow-sm">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Ke Sistem KPI
            </button>
        </form>
    </div>
    
    <div class="card-footer bg-light text-center py-3 border-top">
        <small class="text-muted">Government Enterprise Style &copy; {{ date('Y') }} Sistem Laporan KPI</small>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
