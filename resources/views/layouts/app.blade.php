<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Laporan KPI Mingguan') - Digitalisasi Laporan Operasional</title>
    
    <!-- SEO & Accessibility Meta -->
    <meta name="description" content="Sistem Digitalisasi & Otomatisasi Laporan KPI Mingguan berbasis Laravel 12 dan Bootstrap 5 Government Style.">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Tom Select 2.3 Bootstrap 5 Theme -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --gov-primary: #0f2c59;
            --gov-secondary: #1b365d;
            --gov-accent: #0056b3;
            --gov-bg: #f4f6f9;
            --gov-card-border: #e2e8f0;
            --gov-text-muted: #64748b;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--gov-bg);
            color: #1e293b;
        }

        /* Top Header Navbar */
        .navbar-gov {
            background-color: var(--gov-primary);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand-title {
            font-weight: 700;
            letter-spacing: 0.5px;
            font-size: 1.15rem;
        }

        /* Layout Structure */
        .wrapper {
            display: flex;
            min-height: calc(100vh - 60px);
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background-color: #ffffff;
            border-right: 1px solid var(--gov-card-border);
            flex-shrink: 0;
            padding-top: 1.25rem;
        }

        .sidebar .nav-link {
            color: #475569;
            font-weight: 500;
            padding: 0.65rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-left: 4px solid transparent;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: var(--gov-primary);
            background-color: #f1f5f9;
            border-left-color: var(--gov-primary);
        }

        .sidebar-heading {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--gov-text-muted);
            font-weight: 700;
            padding: 0.75rem 1.25rem 0.25rem;
        }

        /* Main Content Container */
        .main-content {
            flex-grow: 1;
            padding: 1.75rem;
            overflow-x: hidden;
        }

        /* Cards & Components */
        .card-gov {
            border: 1px solid var(--gov-card-border);
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            background: #ffffff;
        }

        .card-gov .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--gov-card-border);
            font-weight: 600;
            color: var(--gov-secondary);
            padding: 1rem 1.25rem;
        }

        /* Stat Widget Cards */
        .stat-card {
            border-left: 4px solid var(--gov-primary);
        }

        .stat-card.stat-success { border-left-color: #198754; }
        .stat-card.stat-warning { border-left-color: #ffc107; }
        .stat-card.stat-info { border-left-color: #0dcaf0; }

        /* Data Table Spreadsheet Style */
        .table-spreadsheet {
            font-size: 0.88rem;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-spreadsheet th {
            background-color: #f8fafc;
            color: #334155;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #cbd5e1;
            padding: 0.75rem 0.65rem;
        }

        .table-spreadsheet td {
            padding: 0.75rem 0.65rem;
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-spreadsheet tbody tr:hover {
            background-color: #f1f5f9;
        }

        /* Tom Select Styling Customization */
        .ts-wrapper .ts-control {
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            font-size: 0.9rem;
            border-color: #dee2e6;
        }

        .ts-wrapper.focus .ts-control {
            border-color: var(--gov-primary);
            box-shadow: 0 0 0 0.25rem rgba(15, 44, 89, 0.15);
        }

        /* Footer */
        .footer-gov {
            background-color: #ffffff;
            border-top: 1px solid var(--gov-card-border);
            padding: 1rem;
            font-size: 0.85rem;
            color: var(--gov-text-muted);
            text-align: center;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Top Navbar -->
    @include('layouts.navbar')

    <!-- Layout Wrapper -->
    <div class="wrapper">
        <!-- Sidebar Navigation -->
        @include('layouts.sidebar')

        <!-- Main Page Content -->
        <main class="main-content">
            <!-- Alert Notifications -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show card-gov mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show card-gov mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show card-gov mb-4" role="alert">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-2"></i> Terdapat kesalahan pengisian form:</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Global Search Modal Command Palette (Ctrl + K) -->
    <div class="modal fade" id="globalSearchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header border-0 pb-0">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-primary fs-4"></i></span>
                        <input type="text" id="globalSearchInput" class="form-control border-0 shadow-none fs-5" placeholder="Cari No Tiket, Problem, Aplikasi, Daerah, Pegawai / NIK..." autofocus>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" style="max-height: 450px; overflow-y: auto;">
                    <div id="globalSearchResults">
                        <p class="text-muted text-center py-4 mb-0"><i class="bi bi-search me-2"></i>Ketik minimal 2 karakter untuk memulai pencarian lintas modul...</p>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-2 d-flex justify-content-between text-muted small">
                    <div>Gunakan tombol <code>↑</code> <code>↓</code> untuk navigasi dan <code>Enter</code> untuk memilih.</div>
                    <div>Tekan <code>Esc</code> untuk menutup.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-gov">
        <div class="container-fluid">
            &copy; {{ date('Y') }} <strong>Sistem Laporan KPI Mingguan</strong> — Digitalisasi Operasional Aplikasi & Dukungan Teknis Daerah. Powered by Laravel 12 & Bootstrap 5.
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Tom Select 2.3 JS Library -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Universal Searchable Select Auto-Initialization
        document.querySelectorAll('select.form-select, select.searchable-select').forEach(function(el) {
            if (!el.classList.contains('no-tom-select') && !el.tomselect) {
                new TomSelect(el, {
                    create: false,
                    sortField: { field: "text", direction: "asc" },
                    placeholder: 'Ketik untuk mencari...',
                    noResultsText: 'Data tidak ditemukan. Silakan gunakan kata kunci lain.',
                    plugins: ['dropdown_input']
                });
            }
        });

        // Shortcut Keyboard Ctrl + K / Cmd + K
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const modalEl = document.getElementById('globalSearchModal');
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        });

        // Global Search AJAX Query
        const searchInput = document.getElementById('globalSearchInput');
        const resultsContainer = document.getElementById('globalSearchResults');
        let searchTimeout = null;

        if (searchInput) {
            document.getElementById('globalSearchModal').addEventListener('shown.bs.modal', function () {
                searchInput.focus();
            });

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length < 2) {
                    resultsContainer.innerHTML = '<p class="text-muted text-center py-4 mb-0"><i class="bi bi-search me-2"></i>Ketik minimal 2 karakter untuk memulai pencarian lintas modul...</p>';
                    return;
                }

                resultsContainer.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="text-muted mt-2 mb-0">Mencari data...</p></div>';

                searchTimeout = setTimeout(() => {
                    fetch('/api/global-search?q=' + encodeURIComponent(query))
                        .then(res => res.json())
                        .then(data => {
                            if (data.length === 0) {
                                resultsContainer.innerHTML = '<div class="text-center py-4 text-muted"><i class="bi bi-info-circle me-1"></i> Data tidak ditemukan. Silakan gunakan kata kunci lain.</div>';
                                return;
                            }

                            let html = '<div class="list-group list-group-flush">';
                            data.forEach(item => {
                                html += `
                                    <a href="${item.url}" class="list-group-item list-group-item-action p-3 d-flex align-items-center justify-content-between rounded mb-1">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="fs-4"><i class="bi ${item.icon}"></i></div>
                                            <div>
                                                <div class="fw-bold text-dark">${item.title}</div>
                                                <small class="text-muted">${item.subtitle}</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-light text-muted border">${item.type}</span>
                                    </a>
                                `;
                            });
                            html += '</div>';
                            resultsContainer.innerHTML = html;
                        })
                        .catch(err => {
                            resultsContainer.innerHTML = '<div class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Gagal memuat pencarian.</div>';
                        });
                }, 300);
            });
        }
    });
    </script>

    @stack('scripts')
</body>
</html>
