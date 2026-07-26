<nav class="navbar navbar-expand-lg navbar-dark navbar-gov">
    <div class="container-fluid px-3">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <div class="bg-white text-dark rounded-circle p-1 d-flex align-items-center justify-content-center" style="width:34px; height:34px;">
                <i class="bi bi-bar-chart-line-fill text-primary"></i>
            </div>
            <span class="navbar-brand-title">LAPORAN KPI MINGGUAN</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTop">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarTop">
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <!-- Global Search Button (Ctrl + K) -->
                <li class="nav-item me-2">
                    <button type="button" class="btn btn-outline-light btn-sm px-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#globalSearchModal">
                        <i class="bi bi-search"></i>
                        <span class="d-none d-md-inline">Pencarian Global...</span>
                        <kbd class="bg-secondary text-white small rounded px-1 me-1">Ctrl+K</kbd>
                    </button>
                </li>

                <li class="nav-item">
                    <span class="badge bg-warning text-dark px-3 py-2 fw-bold shadow-sm">
                        <i class="bi bi-calendar-check-fill me-1"></i> Periode {{ session('active_year', date('Y')) }}
                    </span>
                </li>

                <li class="nav-item">
                    <span class="badge bg-light text-dark px-3 py-2 border">
                        <i class="bi bi-clock-history me-1 text-primary"></i> {{ date('d M Y') }}
                    </span>
                </li>

                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-white" href="#" role="button" data-bs-toggle="dropdown">
                        <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:32px; height:32px;">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="text-start d-none d-md-block" style="font-size: 0.85rem; line-height: 1.2;">
                            <div class="fw-semibold">{{ auth()->user()->name }}</div>
                            <small class="text-light opacity-75">@ {{ auth()->user()->username }} (NIK: {{ auth()->user()->nik ?? '-' }})</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li><h6 class="dropdown-header">Pengaturan Akun (@ {{ auth()->user()->username }})</h6></li>
                        <li><a class="dropdown-item" href="{{ route('password.edit') }}"><i class="bi bi-key-fill me-2 text-warning"></i> Ubah Password Akun</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger fw-semibold">
                                    <i class="bi bi-box-arrow-right me-2"></i> Keluar (Logout)
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                <li class="nav-item">
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login Akun
                    </a>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
