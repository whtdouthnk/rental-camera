<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="adminHMD professional admin dashboard template">
  <title>@yield('title', 'Dashboard') | adminHMD</title>

  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  @yield('head')
</head>

<body>
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>

    @include('layouts.sidebar')

    <div class="admin-main">
      @include('layouts.navbar')

      <main class="dashboard-content">
        <div class="container-fluid px-3 px-lg-4 py-4">
          @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="bi bi-check-circle me-1" aria-hidden="true"></i> {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i> {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          @yield('content')
        </div>
      </main>

      @include('layouts.footer')
    </div>
  </div>

  <script>
    window.adminHMDUser = {
      name: "{{ auth()->user()->nama }}",
      workspace: "{{ auth()->user()->role }} Workspace",
      avatar: "{{ asset('assets/images/avatar/avatar.jpg') }}"
    };
  </script>
  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>
  @yield('scripts')
</body>
</html>
