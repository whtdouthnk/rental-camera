@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
  <div class="page-heading">
    <div class="page-heading-copy">
      <span class="page-icon"><i class="bi bi-tag" aria-hidden="true"></i></span>
      <div>
        <p class="eyebrow mb-1">Inventory</p>
        <h1 class="h3 mb-1">Kategori</h1>
        <p class="text-muted mb-0">Kelompokkan barang berdasarkan jenis kategorinya.</p>
      </div>
    </div>
    <div class="heading-actions">
      <a class="btn btn-primary btn-sm" href="{{ route('admin.kategori.create') }}"><i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah Kategori</a>
    </div>
  </div>

  <section class="panel">
    <div class="panel-header">
      <div>
        <h2 class="h5 mb-1 section-title"><i class="bi bi-tags" aria-hidden="true"></i><span>Data Kategori</span></h2>
        <p class="text-muted mb-0">Daftar seluruh kategori barang.</p>
      </div>
      <input class="form-control form-control-sm table-search" type="search" placeholder="Cari kategori..." data-table-search="kategoriTable" aria-label="Cari kategori">
    </div>
    <div class="table-responsive">
      <table class="table align-middle mb-0" id="kategoriTable">
        <thead>
          <tr>
            <th scope="col">Nama Kategori</th>
            <th scope="col">Jumlah Barang</th>
            <th scope="col">Dibuat</th>
            <th scope="col" class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($kategoris as $kategori)
            <tr>
              <td class="fw-semibold">{{ $kategori->nama_kategori }}</td>
              <td><span class="badge text-bg-light border">{{ $kategori->barang_count }} barang</span></td>
              <td>{{ $kategori->created_at ? $kategori->created_at->format('d M Y') : '-' }}</td>
              <td class="text-end">
                <a class="btn btn-light btn-sm" href="{{ route('admin.kategori.edit', $kategori->id) }}"><i class="bi bi-pencil" aria-hidden="true"></i> Edit</a>
                <form class="d-inline" method="POST" action="{{ route('admin.kategori.destroy', $kategori->id) }}" onsubmit="return confirm('Yakin ingin menghapus kategori {{ $kategori->nama_kategori }}?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash" aria-hidden="true"></i> Hapus</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-muted py-4">Belum ada data kategori.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">
      {{ $kategoris->links('pagination::bootstrap-5') }}
    </div>
  </section>
@endsection
