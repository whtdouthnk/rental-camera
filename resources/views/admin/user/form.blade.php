@extends('layouts.admin')

@section('title', isset($user) ? 'Edit User' : 'Tambah User')

@section('content')
  <div class="page-heading">
    <div class="page-heading-copy">
      <span class="page-icon"><i class="bi bi-person-badge" aria-hidden="true"></i></span>
      <div>
        <p class="eyebrow mb-1">Management</p>
        <h1 class="h3 mb-1">{{ isset($user) ? 'Edit User' : 'Tambah User' }}</h1>
        <p class="text-muted mb-0">Lengkapi data akun pengguna di bawah ini.</p>
      </div>
    </div>
    <div class="heading-actions">
      <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.user.index') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali</a>
    </div>
  </div>

  <section class="row g-3">
    <div class="col-12 col-xl-8">
      <form class="panel needs-validation" method="POST" action="{{ isset($user) ? route('admin.user.update', $user->id) : route('admin.user.store') }}" novalidate>
        @csrf
        @if (isset($user))
          @method('PUT')
        @endif

        <div class="panel-header">
          <div>
            <h2 class="h5 mb-1 section-title"><i class="bi bi-person-vcard" aria-hidden="true"></i><span>Informasi User</span></h2>
            <p class="text-muted mb-0">Data pribadi dan akses akun pengguna.</p>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label" for="nama">Nama</label>
            <input class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" type="text" value="{{ old('nama', $user->nama ?? '') }}" required>
            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="email">Email</label>
            <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="no_hp">No. HP</label>
            <input class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" type="text" value="{{ old('no_hp', $user->no_hp ?? '') }}" placeholder="Opsional">
            @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="role">Role</label>
            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
              <option value="">Pilih role</option>
              @foreach (['Admin', 'Petugas', 'Pengguna'] as $role)
                <option value="{{ $role }}" @selected(old('role', $user->role ?? '') === $role)>{{ $role }}</option>
              @endforeach
            </select>
            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="status">Status</label>
            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
              <option value="">Pilih status</option>
              @foreach (['Aktif', 'Nonaktif'] as $status)
                <option value="{{ $status }}" @selected(old('status', $user->status ?? '') === $status)>{{ $status }}</option>
              @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="password">Password</label>
            <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" @if(!isset($user)) required @endif autocomplete="new-password">
            @if (isset($user))
              <div class="form-text">Kosongkan jika tidak ingin mengubah password.</div>
            @endif
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
            <input class="form-control @error('password') is-invalid @enderror" id="password_confirmation" name="password_confirmation" type="password" @if(!isset($user)) required @endif autocomplete="new-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
          <a class="btn btn-outline-secondary" href="{{ route('admin.user.index') }}">Batal</a>
          <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg" aria-hidden="true"></i> {{ isset($user) ? 'Simpan Perubahan' : 'Tambah User' }}</button>
        </div>
      </form>
    </div>

    <div class="col-12 col-xl-4">
      <div class="panel h-100">
        <h2 class="h5 mb-3 section-title"><i class="bi bi-list-check" aria-hidden="true"></i><span>Catatan</span></h2>
        <div class="activity-list">
          <div class="activity-item"><span class="activity-dot bg-primary"></span><div><p class="mb-1 fw-semibold">Role</p><p class="text-muted small mb-0">Admin dan Petugas mengelola sistem, Pengguna hanya meminjam.</p></div></div>
          <div class="activity-item"><span class="activity-dot bg-success"></span><div><p class="mb-1 fw-semibold">Password</p><p class="text-muted small mb-0">Minimal 8 karakter dan disimpan terenkripsi.</p></div></div>
          <div class="activity-item"><span class="activity-dot bg-warning"></span><div><p class="mb-1 fw-semibold">Status</p><p class="text-muted small mb-0">User Nonaktif tidak dapat masuk ke sistem.</p></div></div>
        </div>
      </div>
    </div>
  </section>
@endsection
