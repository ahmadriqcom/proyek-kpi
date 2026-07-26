<aside class="sidebar">
    <!-- Live Search Menu Sidebar -->
    <div class="px-2 mb-3">
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="sidebarMenuSearch" class="form-control border-start-0 bg-light" placeholder="Cari menu... (Ctrl+K)">
        </div>
    </div>

    <div class="sidebar-heading">Menu Utama</div>
    <ul class="nav flex-column mb-3 sidebar-nav-list">
        @can('dashboard.read')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard KPI
                </a>
            </li>
        @endcan

        @can('kpi_reports.read')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('kpi-reports.index') ? 'active' : '' }}" href="{{ route('kpi-reports.index') }}">
                    <i class="bi bi-table"></i> Data Laporan KPI
                </a>
            </li>
        @endcan

        @can('appraisals.read')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('appraisals.*') ? 'active' : '' }} text-primary fw-semibold" href="{{ route('appraisals.index') }}">
                    <i class="bi bi-award-fill"></i> Penilaian KPI Pegawai
                </a>
            </li>
        @endcan
    </ul>

    @if(auth()->check() && (auth()->user()->can('kpi_reports.create') || auth()->user()->can('kpi_reports.update')))
        <div class="sidebar-heading">Tindakan Cepat</div>
        <ul class="nav flex-column mb-3 sidebar-nav-list">
            @can('kpi_reports.create')
                <li class="nav-item">
                    <a class="nav-link text-primary fw-semibold" href="{{ route('kpi-reports.create') }}">
                        <i class="bi bi-plus-circle-fill"></i> Input Kendala Baru
                    </a>
                </li>
            @endcan

            @can('kpi_reports.create')
                <li class="nav-item">
                    <a class="nav-link text-success fw-semibold {{ request()->routeIs('kpi-reports.import-view') ? 'active' : '' }}" href="{{ route('kpi-reports.import-view') }}">
                        <i class="bi bi-file-earmark-excel-fill text-success"></i> Impor Spreadsheet KPI
                    </a>
                </li>
            @endcan
        </ul>
    @endif

    <!-- Master Data & Pengaturan -->
    @if(auth()->check() && (auth()->user()->can('grade_schemes.read') || auth()->user()->can('app_region_mappings.read') || auth()->user()->can('applications.read') || auth()->user()->can('regions.read') || auth()->user()->can('users.read')))
        <div class="sidebar-heading">Master Data & Pengaturan</div>
        <ul class="nav flex-column mb-3 sidebar-nav-list">
            @can('grade_schemes.read')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('grade-schemes.*') ? 'active' : '' }}" href="{{ route('grade-schemes.index') }}">
                        <i class="bi bi-sliders"></i> Skema Penilaian Grade
                    </a>
                </li>
            @endcan

            @if(auth()->check() && auth()->user()->isSuperAdmin())
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kpi-formula-configs.*') ? 'active' : '' }}" href="{{ route('kpi-formula-configs.index') }}">
                        <i class="bi bi-gear-wide-connected text-primary"></i> Master Formula KPI
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kpi-categories.*') ? 'active' : '' }}" href="{{ route('kpi-categories.index') }}">
                        <i class="bi bi-tags-fill text-primary"></i> Master Kategori Kendala
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kpi-priorities.*') ? 'active' : '' }}" href="{{ route('kpi-priorities.index') }}">
                        <i class="bi bi-exclamation-triangle-fill text-warning"></i> Master Prioritas & SLA
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kpi-impact-levels.*') ? 'active' : '' }}" href="{{ route('kpi-impact-levels.index') }}">
                        <i class="bi bi-diagram-2-fill text-success"></i> Master Tingkat Dampak
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('score-interpretations.*') ? 'active' : '' }}" href="{{ route('score-interpretations.index') }}">
                        <i class="bi bi-journal-text text-primary"></i> Master Interpretasi Penilaian
                    </a>
                </li>
            @endif

            @can('app_region_mappings.read')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('app-region-mappings.*') ? 'active' : '' }}" href="{{ route('app-region-mappings.index') }}">
                        <i class="bi bi-diagram-3-fill text-primary"></i> Master Mapping Aplikasi & Daerah
                    </a>
                </li>
            @endcan

            @can('applications.read')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('applications.*') ? 'active' : '' }}" href="{{ route('applications.index') }}">
                        <i class="bi bi-app-indicator text-primary"></i> Master Aplikasi
                    </a>
                </li>
            @endcan

            @can('regions.read')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('regions.*') ? 'active' : '' }}" href="{{ route('regions.index') }}">
                        <i class="bi bi-geo-alt text-primary"></i> Master Daerah
                    </a>
                </li>
            @endcan

            @can('users.read')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <i class="bi bi-people-fill text-primary"></i> Manajemen User & Hak Akses
                    </a>
                </li>
            @endcan
        </ul>
    @endif
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('sidebarMenuSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const navItems = document.querySelectorAll('.sidebar .nav-item');

            navItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(query)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>
