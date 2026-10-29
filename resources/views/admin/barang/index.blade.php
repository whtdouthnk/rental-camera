@extends('layouts.admin')

@section('title', 'Barang')

@section('content')
  <div class="page-heading">
    <div class="page-heading-copy">
      <span class="page-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
      <div>
        <p class="eyebrow mb-1">Inventory</p>
        <h1 class="h3 mb-1">Barang</h1>
        <p class="text-muted mb-0">Kelola inventaris kamera dan perlengkapan yang tersedia untuk disewakan.</p>
      </div>
    </div>
    <div class="heading-actions">
      <a class="btn btn-primary btn-sm" href="{{ route('admin.barang.create') }}"><i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah Barang</a>
    </div>
  </div>

  <section class="panel">
    <div class="panel-header">
      <div>
        <h2 class="h5 mb-1 section-title"><i class="bi bi-box-seam" aria-hidden="true"></i><span>Data Barang</span></h2>
        <p class="text-muted mb-0">Daftar seluruh barang pada sistem penyewaan.</p>
      </div>
      <input class="form-control form-control-sm table-search" type="search" placeholder="Cari barang..." data-table-search="barangTable" aria-label="Cari barang">
    </div>
    <div class="table-responsive">
      <table class="table align-middle mb-0" id="barangTable">
        <thead>
          <tr>
            <th scope="col">Barang</th>
            <th scope="col">Kategori</th>
            <th scope="col">Kode</th>
            <th scope="col">Stok</th>
            <th scope="col">Kondisi</th>
            <th scope="col">Sewa/Hari</th>
            <th scope="col">Denda/Hari</th>
            <th scope="col">Status</th>
            <th scope="col" class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($barangs as $barang)
            <tr>
              <td>
                <div class="table-media">
                  @if ($barang->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($barang->foto))
                    <img class="product-thumb" src="{{ asset('storage/' . $barang->foto) }}" alt="{{ $barang->nama_barang }}">
                  @else
                    <img class="product-thumb" src="{{ asset('assets/images/png/dasher-ai.png') }}" alt="{{ $barang->nama_barang }}">
                  @endif
                  <span class="fw-semibold">{{ $barang->nama_barang }}</span>
                </div>
              </td>
              <td>{{ $barang->kategori->nama_kategori ?? '-' }}</td>
              <td class="fw-semibold">{{ $barang->kode_barang }}</td>
              <td>{{ $barang->stok }}</td>
              <td>
                @if ($barang->kondisi === 'Baik')
                  <span class="badge text-bg-success">{{ $barang->kondisi }}</span>
                @elseif ($barang->kondisi === 'Rusak Ringan')
                  <span class="badge text-bg-warning">{{ $barang->kondisi }}</span>
                @else
                  <span class="badge text-bg-danger">{{ $barang->kondisi }}</span>
                @endif
              </td>
              <td>{{ 'Rp ' . number_format($barang->harga_sewa_per_hari, 0, ',', '.') }}</td>
              <td>{{ 'Rp ' . number_format($barang->denda_per_hari, 0, ',', '.') }}</td>
              <td>
                @if ($barang->status === 'Aktif')
                  <span class="badge text-bg-success">{{ $barang->status }}</span>
                @else
                  <span class="badge text-bg-secondary">{{ $barang->status }}</span>
                @endif
              </td>
              <td class="text-end">
                <a class="btn btn-light btn-sm" href="{{ route('admin.barang.edit', $barang->id) }}"><i class="bi bi-pencil" aria-hidden="true"></i> Edit</a>
                <form class="d-inline" method="POST" action="{{ route('admin.barang.destroy', $barang->id) }}" onsubmit="return confirm('Yakin ingin menghapus barang {{ $barang->nama_barang }}?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-trash" aria-hidden="true"></i> Hapus</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center text-muted py-4">Belum ada data barang.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">
      {{ $barangs->links('pagination::bootstrap-5') }}
    </div>
  </section>
@endsection
