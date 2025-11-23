<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('owner.dashboard') }}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 50px; max-height: 50px;">
        </div>
        <div class="mx-3 sidebar-brand-text">{{ (auth()->user()->role ?? 'owner') === 'admin' ? 'ADMIN' : 'OWNER' }}</div>
    </a>

    <!-- Divider -->
    <hr class="my-0 sidebar-divider">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('owner.dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Analisis
    </div>

    <!-- Nav Item - Analisis Keuangan (moved directly below Dashboard) -->
    <li class="nav-item {{ request()->routeIs('owner.analysis.finance') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('owner.analysis.finance') }}">
            <i class="fas fa-fw fa-chart-pie"></i>
            <span>Analisis Keuangan</span>
        </a>
    </li>

    <!-- Heading -->
    <div class="sidebar-heading">
        Manajemen
    </div>

    <!-- Nav Item - User Management -->
    <li class="nav-item {{ request()->routeIs('owner.users.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('owner.users.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>User Management</span></a>
    </li>

    <!-- Nav Item - modal Management -->
    <li class="nav-item {{ request()->routeIs('owner.modal.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('owner.modal.index') }}">
            <i class="fas fa-fw fa-window-restore"></i>
            <span>Modal Management</span></a>
    </li>

    <!-- Nav Item - Pemasukkan Management -->
    <li class="nav-item {{ request()->routeIs('owner.pemasukkan.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('owner.pemasukkan.index') }}">
            <i class="fas fa-fw fa-money-bill-wave"></i>
            <span>Pemasukkan</span></a>
    </li>

    <!-- Nav Item - Saldo Management -->
    <li class="nav-item {{ request()->routeIs('owner.saldo.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('owner.saldo.index') }}">
            <i class="fas fa-fw fa-exchange-alt"></i>
            <span>Saldo Management</span></a>
    </li>

    @php $u = auth()->user(); $canSalary = $u && ($u->role==='owner' || (method_exists($u,'hasPermission') && $u->hasPermission('salary.read'))); @endphp
    @if($canSalary)
    <li class="nav-item {{ request()->routeIs('owner.employee-salary.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('owner.employee-salary.index') }}">
            <i class="fas fa-fw fa-briefcase"></i>
            <span>Gaji Karyawan</span></a>
    </li>
    @endif

     <!-- Divider -->
    <hr class="sidebar-divider">
        
    <!-- Heading -->
     <div class="sidebar-heading">
        Pantau Kasir
    </div>
        
    <!-- Nav Item - Pengeluaran kasir -->
    <li class="nav-item {{ request()->routeIs('owner.kasir-expend*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('owner.kasir-expend.index') }}">
            <i class="fas fa-fw fa-money-bill-wave"></i>
            <span>Pengeluaran Kasir</span>
        </a>
    </li>
    
    <!-- Nav Item - Pemasukkan Kasir -->
    <li class="nav-item {{ request()->routeIs('owner.kasir-income*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('owner.kasir-income.index') }}">
            <i class="fas fa-fw fa-money-bill-wave"></i>
            <span>Pemasukkan Kasir</span>
        </a>
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