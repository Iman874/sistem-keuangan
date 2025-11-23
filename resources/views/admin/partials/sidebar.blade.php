<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 50px; max-height: 50px;">
        </div>
        <div class="mx-3 sidebar-brand-text">admin</div>
    </a>
    <hr class="my-0 sidebar-divider">
    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

     @php
        $canAnalysis = auth()->user() && method_exists(auth()->user(),'hasPermission') &&
            (auth()->user()->hasPermission('income.read') || auth()->user()->hasPermission('expense.read'));
    @endphp
    @if($canAnalysis)
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Analisis</div>
    <li class="nav-item {{ request()->routeIs('owner.analysis.finance') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('owner.analysis.finance') }}">
            <i class="fas fa-fw fa-chart-pie"></i>
            <span>Analisis Keuangan</span></a>
    </li>
    @endif
    
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Manajemen</div>
    @if(auth()->user() && method_exists(auth()->user(),'hasPermission') && auth()->user()->hasPermission('users.read'))
    <li class="nav-item {{ request()->routeIs('owner.users.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('owner.users.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>User Management</span></a>
    </li>
    @endif

    @if(auth()->user() && method_exists(auth()->user(),'hasPermission') && auth()->user()->hasPermission('pemasukkan.read'))
    <li class="nav-item {{ request()->routeIs('owner.modal.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('owner.modal.index') }}">
            <i class="fas fa-fw fa-window-restore"></i>
            <span>Modal Management</span></a>
    </li>
    <li class="nav-item {{ request()->routeIs('owner.pemasukkan.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('owner.pemasukkan.index') }}">
            <i class="fas fa-fw fa-money-bill-wave"></i>
            <span>Pemasukkan</span></a>
    </li>
    @endif

    @if(auth()->user() && method_exists(auth()->user(),'hasPermission') && auth()->user()->hasPermission('saldo.read'))
    <li class="nav-item {{ request()->routeIs('owner.saldo.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('owner.saldo.index') }}">
            <i class="fas fa-fw fa-exchange-alt"></i>
            <span>Saldo Management</span></a>
    </li>
    @endif

    @if(auth()->user() && method_exists(auth()->user(),'hasPermission') && auth()->user()->hasPermission('salary.read'))
    <li class="nav-item {{ request()->routeIs('owner.employee-salary.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('owner.employee-salary.index') }}">
            <i class="fas fa-fw fa-briefcase"></i>
            <span>Gaji Karyawan</span></a>
    </li>
    @endif

    @php
        $canPantauKasir = auth()->user() && method_exists(auth()->user(),'hasPermission') &&
            (auth()->user()->hasPermission('income.read') || auth()->user()->hasPermission('expense.read'));
    @endphp
    @if($canPantauKasir)
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Pantau Kasir</div>
        @if(auth()->user()->hasPermission('expense.read'))
        <li class="nav-item {{ request()->routeIs('owner.kasir-expend*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('owner.kasir-expend.index') }}">
                <i class="fas fa-fw fa-money-bill-wave"></i>
                <span>Pengeluaran Kasir</span></a>
        </li>
        @endif
        @if(auth()->user()->hasPermission('income.read'))
        <li class="nav-item {{ request()->routeIs('owner.kasir-income*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('owner.kasir-income.index') }}">
                <i class="fas fa-fw fa-money-bill-wave"></i>
                <span>Pemasukkan Kasir</span></a>
        </li>
        @endif
    @endif

   

    <hr class="sidebar-divider">
    <li class="nav-item">
        <a class="nav-link" href="{{ url('/') }}">
            <i class="fas fa-fw fa-home"></i>
            <span>Kembali ke Beranda</span></a>
    </li>
    <hr class="sidebar-divider">
    <div class="text-center d-none d-md-inline">
        <button class="border-0 rounded-circle" id="sidebarToggle"></button>
    </div>
</ul>