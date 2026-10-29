@extends('layouts.admin')

@section('title', 'User')

@section('content')
  <div class="page-heading">
    <div class="page-heading-copy">
      <span class="page-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
      <div>
        <p class="eyebrow mb-1">Management</p>
        <h1 class="h3 mb-1">User</h1>
        <p class="text-muted mb-0">Kelola akun pengguna beserta role dan statusnya.</p>
      </div>
    </div>
    <div class="heading-actions">
      <a class="btn btn-primary btn-sm" href="{{ route('admin.user.create') }}"><i class="bi bi-person-plus" aria-hidden="true"></i> Tambah User</a>
    </div>
  </div>

  <section class="panel">
    <div class="panel-header">
      <div>
        <h2 class="h5 mb-1 section-title"><i class="bi bi-people" aria-hidden="true"></i><span>Data User</span></h2>
        <p class="text-muted mb-0">Daftar seluruh pengguna terdaftar.</p>
      </div>
      <input class="form-control form-control-sm table-search" type="search" placeholder="Cari user..." data-table-search="userTable" aria-label="Cari user">
    </div>
    <div class="table-responsive">
      <table class="table align-middle mb-0" id="userTable">
        <thead>
          <tr>
            <th scope="col">Nama</th>
            <th scope="col">Email</th>
            <th scope="col">No. HP</th>
            <th scope="col">Role</th>
            <th scope="col">Status</th>
            <th scope="col" class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($users as $user)
            <tr>
              <td class="fw-semibold">{{ $user->nama }}</td>
              <td>{{ $user->email }}</td>
              <td>{{ $user->no_hp ?? '-' }}</td>
              <td>
                @if ($user->role === 'Admin')
                  <span class="badge text-bg-primary">{{ $user->role }}</span>
                @elseif ($user->role === 'Petugas')
                  <span class="badge text-bg-info">{{ $user->role }}</span>
                @else
                  <span class="badge text-bg-secondary">{{ $user->role }}</span>
                @endif
              </td>
              <td>
                @if ($user->status === 'Aktif')
                  <span class="badge text-bg-success">{{ $user->status }}</span>
                @else
                  <span class="badge text-bg-secondary">{{ $user->status }}</span>
                @endif
              </td>
              <td class="text-end">
                <a class="btn btn-light btn-sm" href="{{ route('admin.user.edit', $user->id) }}"><i class="bi bi-pencil" aria-hidden="true"></i> Edit</a>
                <form class="d-inline" method="POST" action="{{ route('admin.user.destroy', $user->id) }}" onsubmit="return confirm('Yakin ingin menghapus user {{ $user->nama }}?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash" aria-hidden="true"></i> Hapus</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-4">Belum ada data user.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">
      {{ $users->links('pagination::bootstrap-5') }}
    </div>
  </section>
@endsection
