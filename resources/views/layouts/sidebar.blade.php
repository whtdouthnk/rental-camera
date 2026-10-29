<aside class="admin-sidebar" id="adminSidebar" aria-label="Main navigation">
    <div class="sidebar-header">
      <a class="brand-mark" href="{{ route(auth()->user()->role === 'Admin' ? 'admin.dashboard' : (auth()->user()->role === 'Petugas' ? 'petugas.dashboard' : 'user.dashboard')) }}" aria-label="adminHMD dashboard">
        <span class="brand-icon"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i></span>
        <span class="brand-copy">
          <span class="brand-title">RentCam</span>
          <span class="brand-subtitle">Pusat Rental Kamera dan tmn2nya</span>
        </span>
      </a>
    </div>

    <nav class="sidebar-nav">
      @if (auth()->user()->role === 'Admin')
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
          <span class="nav-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
          <span class="nav-text">Dashboard</span>
        </a>

        <a class="nav-link {{ request()->routeIs('admin.user.*') ? 'active' : '' }}" href="{{ route('admin.user.index') }}" @if(request()->routeIs('admin.user.*')) aria-current="page" @endif>
          <span class="nav-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
          <span class="nav-text">User</span>
        </a>

        <a class="nav-link {{ request()->routeIs('admin.barang.*') ? 'active' : '' }}" href="{{ route('admin.barang.index') }}" @if(request()->routeIs('admin.barang.*')) aria-current="page" @endif>
          <span class="nav-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
          <span class="nav-text">Barang</span>
        </a>

        <a class="nav-link {{ request()->routeIs(['admin.transaksi.*', 'admin.pengembalian.*']) ? 'active' : '' }}" data-bs-toggle="collapse" href="#transaksiSubmenu" role="button" aria-expanded="false" aria-controls="transaksiSubmenu">
          <span class="nav-icon"><i class="bi bi-arrow-left-right" aria-hidden="true"></i></span>
          <span class="nav-text">Transaksi</span>
          <i class="bi bi-chevron-down ms-auto" aria-hidden="true"></i>
        </a>

        <div class="collapse" id="transaksiSubmenu">
          <a class="nav-link ps-5 {{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }}" href="{{ route('admin.transaksi.index') }}">
            <span class="nav-icon"><i class="bi bi-box-arrow-in-right" aria-hidden="true"></i></span>
            <span class="nav-text">Peminjaman</span>
          </a>
          <a class="nav-link ps-5 {{ request()->routeIs('admin.pengembalian.*') ? 'active' : '' }}" href="{{ route('admin.pengembalian.index') }}">
            <span class="nav-icon"><i class="bi bi-arrow-return-left" aria-hidden="true"></i></span>
            <span class="nav-text">Pengembalian</span>
          </a>
        </div>

        <a class="nav-link {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}" href="{{ route('admin.kategori.index') }}" @if(request()->routeIs('admin.kategori.*')) aria-current="page" @endif>
          <span class="nav-icon"><i class="bi bi-tag" aria-hidden="true"></i></span>
          <span class="nav-text">Kategori</span>
        </a>

        <a class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}" href="{{ route('admin.laporan.index') }}" @if(request()->routeIs('admin.laporan.*')) aria-current="page" @endif>
          <span class="nav-icon"><i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i></span>
          <span class="nav-text">Laporan</span>
        </a>

        <a class="nav-link {{ request()->routeIs('admin.log-aktivitas.*') ? 'active' : '' }}" href="{{ route('admin.log-aktivitas.index') }}" @if(request()->routeIs('admin.log-aktivitas.*')) aria-current="page" @endif>
          <span class="nav-icon"><i class="bi bi-journal-text" aria-hidden="true"></i></span>
          <span class="nav-text">Log Aktivitas</span>
        </a>
      @endif

      @if (auth()->user()->role === 'Petugas')
        <a class="nav-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}" href="{{ route('petugas.dashboard') }}" @if(request()->routeIs('petugas.dashboard')) aria-current="page" @endif>
          <span class="nav-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
          <span class="nav-text">Dashboard</span>
        </a>

        <a class="nav-link {{ request()->routeIs('petugas.transaksi.*') ? 'active' : '' }}" href="{{ route('petugas.transaksi.index') }}" @if(request()->routeIs('petugas.transaksi.*')) aria-current="page" @endif>
          <span class="nav-icon"><i class="bi bi-box-arrow-in-right" aria-hidden="true"></i></span>
          <span class="nav-text">Peminjaman</span>
        </a>

        <a class="nav-link {{ request()->routeIs('petugas.pengembalian.*') ? 'active' : '' }}" href="{{ route('petugas.pengembalian.index') }}" @if(request()->routeIs('petugas.pengembalian.*')) aria-current="page" @endif>
          <span class="nav-icon"><i class="bi bi-arrow-return-left" aria-hidden="true"></i></span>
          <span class="nav-text">Pengembalian</span>
        </a>
      @endif

      @if (auth()->user()->role === 'Pengguna')
        <a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}" @if(request()->routeIs('user.dashboard')) aria-current="page" @endif>
          <span class="nav-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
          <span class="nav-text">Dashboard</span>
        </a>

        <a class="nav-link {{ request()->routeIs('user.transaksi.create') ? 'active' : '' }}" href="{{ route('user.transaksi.create') }}" @if(request()->routeIs('user.transaksi.create')) aria-current="page" @endif>
          <span class="nav-icon"><i class="bi bi-plus-circle" aria-hidden="true"></i></span>
          <span class="nav-text">Ajukan Peminjaman</span>
        </a>

        <a class="nav-link {{ request()->routeIs(['user.transaksi.index', 'user.transaksi.show']) ? 'active' : '' }}" href="{{ route('user.transaksi.index') }}" @if(request()->routeIs(['user.transaksi.index', 'user.transaksi.show'])) aria-current="page" @endif>
          <span class="nav-icon"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
          <span class="nav-text">Riwayat Peminjaman</span>
        </a>
      @endif
    </nav>
    
    <div class="sidebar-user">
      <img class="avatar-img avatar-md sidebar-user-avatar" src="{{ asset('assets/images/avatar/avatar.jpg') }}" alt="{{ auth()->user()->nama }}">
      <strong>{{ auth()->user()->nama }}</strong>
      <small>{{ auth()->user()->role }} Workspace</small>
    </div>

    
  </aside>
