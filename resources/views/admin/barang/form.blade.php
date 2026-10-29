@extends('layouts.admin')

@section('title', isset($barang) ? 'Edit Barang' : 'Tambah Barang')

@section('content')
  <div class="page-heading">
    <div class="page-heading-copy">
      <span class="page-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
      <div>
        <p class="eyebrow mb-1">Inventory</p>
        <h1 class="h3 mb-1">{{ isset($barang) ? 'Edit Barang' : 'Tambah Barang' }}</h1>
        <p class="text-muted mb-0">Lengkapi detail barang yang akan disewakan.</p>
      </div>
    </div>
    <div class="heading-actions">
      <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.barang.index') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali</a>
    </div>
  </div>

  <section class="row g-3">
    <div class="col-12 col-xl-8">
      <form class="panel needs-validation" method="POST" action="{{ isset($barang) ? route('admin.barang.update', $barang->id) : route('admin.barang.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        @if (isset($barang))
          @method('PUT')
        @endif

        <div class="panel-header">
          <div>
            <h2 class="h5 mb-1 section-title"><i class="bi bi-box-seam" aria-hidden="true"></i><span>Detail Barang</span></h2>
            <p class="text-muted mb-0">Informasi barang, stok, kondisi, serta tarif sewa dan denda.</p>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label" for="nama_barang">Nama Barang</label>
            <input class="form-control @error('nama_barang') is-invalid @enderror" id="nama_barang" name="nama_barang" type="text" value="{{ old('nama_barang', $barang->nama_barang ?? '') }}" required>
            @error('nama_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="kode_barang">Kode Barang</label>
            @if (isset($barang))
              <input class="form-control" id="kode_barang" type="text" value="{{ $barang->kode_barang }}" readonly>
            @else
              <input class="form-control" id="kode_barang" type="text" placeholder="Otomatis, mis. KAM-001" readonly>
            @endif
            <div class="form-text">Kode dibuat otomatis dari kode kategori + nomor urut.</div>
          </div>

          <div class="col-md-6">
            <label class="form-label" for="kategori_id">Kategori</label>
            <select class="form-select @error('kategori_id') is-invalid @enderror" id="kategori_id" name="kategori_id" required>
              <option value="">Pilih kategori</option>
              @foreach ($kategoris as $kategori)
                <option value="{{ $kategori->id }}" @selected(old('kategori_id', $barang->kategori_id ?? '') == $kategori->id)>{{ $kategori->nama_kategori }}</option>
              @endforeach
            </select>
            @error('kategori_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="merk">Merk</label>
            <input class="form-control @error('merk') is-invalid @enderror" id="merk" name="merk" type="text" value="{{ old('merk', $barang->merk ?? '') }}" required>
            @error('merk')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="serial_number">Serial Number</label>
            <input class="form-control @error('serial_number') is-invalid @enderror" id="serial_number" name="serial_number" type="text" value="{{ old('serial_number', $barang->serial_number ?? '') }}" placeholder="Opsional">
            @error('serial_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="stok">Stok</label>
            <input class="form-control @error('stok') is-invalid @enderror" id="stok" name="stok" type="number" min="0" value="{{ old('stok', $barang->stok ?? '') }}" required>
            @error('stok')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="kondisi">Kondisi</label>
            <select class="form-select @error('kondisi') is-invalid @enderror" id="kondisi" name="kondisi" required>
              <option value="">Pilih kondisi</option>
              @foreach (['Baik', 'Rusak Ringan', 'Rusak'] as $kondisi)
                <option value="{{ $kondisi }}" @selected(old('kondisi', $barang->kondisi ?? '') === $kondisi)>{{ $kondisi }}</option>
              @endforeach
            </select>
            @error('kondisi')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="harga_sewa_per_hari">Harga Sewa per Hari (Rp)</label>
            <input class="form-control @error('harga_sewa_per_hari') is-invalid @enderror" id="harga_sewa_per_hari" name="harga_sewa_per_hari" type="number" min="0" step="0.01" value="{{ old('harga_sewa_per_hari', $barang->harga_sewa_per_hari ?? '') }}" required>
            @error('harga_sewa_per_hari')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="denda_per_hari">Denda per Hari (Rp)</label>
            <input class="form-control @error('denda_per_hari') is-invalid @enderror" id="denda_per_hari" name="denda_per_hari" type="number" min="0" step="0.01" value="{{ old('denda_per_hari', $barang->denda_per_hari ?? '') }}" required>
            @error('denda_per_hari')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="status">Status</label>
            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
              <option value="">Pilih status</option>
              @foreach (['Aktif', 'Nonaktif'] as $status)
                <option value="{{ $status }}" @selected(old('status', $barang->status ?? '') === $status)>{{ $status }}</option>
              @endforeach
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>

          <div class="col-md-6">
            <label class="form-label" for="foto">Foto Barang</label>
            <input class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" type="file" accept="image/*">
            <div class="form-text">Format JPG/PNG, maksimal 2MB. Kosongkan jika tidak ingin mengubah.</div>
            @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
          <a class="btn btn-outline-secondary" href="{{ route('admin.barang.index') }}">Batal</a>
          <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg" aria-hidden="true"></i> {{ isset($barang) ? 'Simpan Perubahan' : 'Tambah Barang' }}</button>
        </div>
      </form>
    </div>

    <div class="col-12 col-xl-4">
      <div class="panel h-100">
        <h2 class="h5 mb-3 section-title"><i class="bi bi-card-image" aria-hidden="true"></i><span>Foto Barang</span></h2>
        @if (isset($barang) && $barang->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($barang->foto))
          <img class="img-fluid rounded" src="{{ asset('storage/' . $barang->foto) }}" alt="{{ $barang->nama_barang }}">
        @else
          <p class="text-muted small mb-0">Belum ada foto. Tambahkan foto setelah menyimpan barang.</p>
        @endif
      </div>
    </div>
  </section>
@endsection
