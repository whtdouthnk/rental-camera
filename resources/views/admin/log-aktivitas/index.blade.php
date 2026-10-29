@extends('layouts.admin')

@section('title', 'Log Aktivitas')

@section('content')
  <div class="page-heading">
    <div class="page-heading-copy">
      <span class="page-icon"><i class="bi bi-journal-text" aria-hidden="true"></i></span>
      <div>
        <p class="eyebrow mb-1">Audit</p>
        <h1 class="h3 mb-1">Log Aktivitas</h1>
        <p class="text-muted mb-0">Rekam jejak aktivitas seluruh pengguna dalam sistem.</p>
      </div>
    </div>
  </div>

  <section class="panel">
    <form class="panel-header" method="GET" action="{{ route('admin.log-aktivitas.index') }}">
      <div class="d-flex flex-wrap gap-2">
        <input class="form-control form-control-sm" type="search" name="cari" value="{{ request('cari') }}" placeholder="Cari deskripsi..." aria-label="Cari deskripsi">
       
        <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-funnel" aria-hidden="true"></i> Filter</button>
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.log-aktivitas.index') }}"><i class="bi bi-x-circle" aria-hidden="true"></i> Reset</a>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Waktu</th>
            <th scope="col">User</th>
            <th scope="col">Aksi</th>
            <th scope="col">Deskripsi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($logs as $log)
            @php
              $badge = match ($log->aksi) {
                'create' => 'text-bg-success',
                'update' => 'text-bg-primary',
                'delete' => 'text-bg-danger',
                'login' => 'text-bg-info',
                'logout' => 'text-bg-secondary',
                'approve', 'serah' => 'text-bg-success',
                'reject' => 'text-bg-danger',
                'pengajuan', 'pengembalian', 'cancel' => 'text-bg-warning',
                default => 'text-bg-light border',
              };
            @endphp
            <tr>
              <td class="text-nowrap">{{ $log->created_at ? $log->created_at->format('d M Y H:i:s') : '-' }}</td>
              <td>
                <div class="fw-semibold">{{ $log->user->nama ?? 'Sistem' }}</div>
                @if ($log->user)
                  <small class="text-muted">{{ $log->user->role }}</small>
                @endif
              </td>
              <td><span class="badge {{ $badge }}">{{ $log->aksi }}</span></td>
              <td>{{ $log->deskripsi }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">Belum ada data log aktivitas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">
      {{ $logs->links('pagination::bootstrap-5') }}
    </div>
  </section>
@endsection
