@extends('layouts.admin')

@section('title', isset($kategori) ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
  <div class="page-heading">
    <div class="page-heading-copy">
      <span class="page-icon"><i class="bi bi-tag" aria-hidden="true"></i></span>
      <div>
        <p class="eyebrow mb-1">Inventory</p>
        <h1 class="h3 mb-1">{{ isset($kategori) ? 'Edit Kategori' : 'Tambah Kategori' }}</h1>
        <p class="text-muted mb-0">Buat atau ubah kategori barang.</p>
      </div>
    </div>
    <div class="heading-actions">
      <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.kategori.index') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali</a>
    </div>
  </div>

  <section class="row g-3">
    <div class="col-12 col-xl-8">
      <form class="panel needs-validation" method="POST" action="{{ isset($kategori) ? route('admin.kategori.update', $kategori->id) : route('admin.kategori.store') }}" novalidate>
        @csrf
        @if (isset($kategori))
          @method('PUT')
        @endif

        <div class="panel-header">
          <div>
            <h2 class="h5 mb-1 section-title"><i class="bi bi-tags" aria-hidden="true"></i><span>Informasi Kategori</span></h2>
            <p class="text-muted mb-0">Nama kategori harus unik di dalam sistem.</p>
          </div>
        </div>

        <div class="row g-3">
          @if (isset($kategori))
            <div class="col-12">
              <label class="form-label" for="kode">Kode Kategori</label>
              <input class="form-control" id="kode" type="text" value="{{ $kategori->kode }}" readonly>
              <div class="form-text">Kode kategori tidak dapat diubah.</div>
            </div>
          @endif

          <div class="col-12">
            <label class="form-label" for="nama_kategori">Nama Kategori</label>
            <input class="form-control @error('nama_kategori') is-invalid @enderror" id="nama_kategori" name="nama_kategori" type="text" value="{{ old('nama_kategori', $kategori->nama_kategori ?? '') }}" required>
            @error('nama_kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @if (!isset($kategori))
              <div class="form-text">Kode kategori dibuat otomatis dari nama (contoh: Kamera → KAM).</div>
            @endif
          </div>
        </div>

        <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
          <a class="btn btn-outline-secondary" href="{{ route('admin.kategori.index') }}">Batal</a>
          <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg" aria-hidden="true"></i> {{ isset($kategori) ? 'Simpan Perubahan' : 'Tambah Kategori' }}</button>
        </div>
      </form>
    </div>

    <div class="col-12 col-xl-4">
      <div class="panel h-100">
        <h2 class="h5 mb-3 section-title"><i class="bi bi-info-circle" aria-hidden="true"></i><span>Catatan</span></h2>
        <div class="activity-list">
          <div class="activity-item"><span class="activity-dot bg-primary"></span><div><p class="mb-1 fw-semibold">Unik</p><p class="text-muted small mb-0">Nama kategori tidak boleh sama dengan kategori lain.</p></div></div>
          <div class="activity-item"><span class="activity-dot bg-warning"></span><div><p class="mb-1 fw-semibold">Relasi Barang</p><p class="text-muted small mb-0">Kategori yang masih memiliki barang tidak dapat dihapus.</p></div></div>
        </div>
      </div>
    </div>
  </section>
@endsection
