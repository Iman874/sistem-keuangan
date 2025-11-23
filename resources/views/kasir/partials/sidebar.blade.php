<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('kasir.dashboard') }}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 50px; max-height: 50px;">
        </div>
        <div class="mx-3 sidebar-brand-text">kasir</div>
    </a>

    <!-- Divider -->
    <hr class="my-0 sidebar-divider">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('kasir.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Heading -->
<div class="sidebar-heading">
    Transaksi
</div>

    <!-- Nav Item - Income -->
    <li class="nav-item {{ request()->routeIs('kasir.income.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('kasir.income.index') }}">
            <i class="fas fa-fw fa-money-bill-wave"></i>
            <span>Pemasukkan</span></a>
    </li>
    <!-- Nav Item - Session Report -->
    <li class="nav-item {{ request()->routeIs('kasir.session-report.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('kasir.session-report.index') }}">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>Laporan Sesi</span></a>
    </li>

    <!-- Nav Item - Expend -->
    <li class="nav-item {{ request()->routeIs('kasir.expend.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('kasir.expend.index') }}">
            <i class="fas fa-fw fa-money-bill"></i>
            <span>Pengeluaran</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Nav Item - Return to Home -->
    <li class="nav-item">
        <a class="nav-link" href="{{ url('/') }}">
            <i class="fas fa-fw fa-home"></i>
            <span>Kembali ke Beranda</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">
    
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="border-0 rounded-circle" id="sidebarToggle"></button>
    </div>
</ul>