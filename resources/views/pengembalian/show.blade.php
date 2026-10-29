@extends('layouts.admin')

@section('title', 'Detail Pengembalian')

@section('content')
  @php $role = auth()->user()->role; @endphp

  <div class="page-heading">
    <div class="page-heading-copy">
      <span class="page-icon"><i class="bi bi-arrow-return-left" aria-hidden="true"></i></span>
      <div>
        <p class="eyebrow mb-1">Pengembalian</p>
        <h1 class="h3 mb-1">{{ $pengembalian->transaksi->kode_transaksi ?? '-' }}</h1>
        <p class="text-muted mb-0">Detail proses pengembalian barang.</p>
      </div>
    </div>
    <div class="heading-actions">
      <a class="btn btn-outline-secondary btn-sm" href="{{ route($role === 'Petugas' ? 'petugas.pengembalian.index' : 'admin.pengembalian.index') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali</a>
    </div>
  </div>

  <section class="row g-3">
    <div class="col-12 col-xl-7">
      <div class="panel">
        <div class="panel-header">
          <div>
            <h2 class="h5 mb-1 section-title"><i class="bi bi-info-circle" aria-hidden="true"></i><span>Informasi Pengembalian</span></h2>
            <span class="badge text-bg-success">Selesai</span>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-md-6"><p class="text-muted small mb-1">Peminjam</p><p class="fw-semibold mb-0">{{ $pengembalian->transaksi->user->nama ?? '-' }}</p><p class="text-muted small mb-0">{{ $pengembalian->transaksi->user->email ?? '-' }}</p></div>
          <div class="col-md-6"><p class="text-muted small mb-1">Tanggal Kembali</p><p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($pengembalian->tgl_kembali)->format('d M Y') }}</p></div>
          <div class="col-md-4"><p class="text-muted small mb-1">Keterlambatan</p><p class="fw-semibold mb-0">{{ $pengembalian->terlambat_hari }} hari</p></div>
          <div class="col-md-4"><p class="text-muted small mb-1">Total Denda</p><p class="fw-semibold mb-0 text-danger">{{ 'Rp ' . number_format($pengembalian->total_denda, 0, ',', '.') }}</p></div>
          <div class="col-md-4"><p class="text-muted small mb-1">Biaya Sewa</p><p class="fw-semibold mb-0">{{ 'Rp ' . number_format($pengembalian->transaksi->totalBiayaSewa(), 0, ',', '.') }}</p></div>
          <div class="col-md-4"><p class="text-muted small mb-1">Total Tagihan</p><p class="fw-semibold mb-0 text-primary">{{ 'Rp ' . number_format($pengembalian->transaksi->totalBiayaSewa() + $pengembalian->total_denda, 0, ',', '.') }}</p></div>
          <div class="col-md-4"><p class="text-muted small mb-1">Kondisi Barang</p>
            <p class="fw-semibold mb-0">
              @if ($pengembalian->kondisi_barang === 'Baik')
                <span class="badge text-bg-success">{{ $pengembalian->kondisi_barang }}</span>
              @elseif ($pengembalian->kondisi_barang === 'Rusak Ringan')
                <span class="badge text-bg-warning">{{ $pengembalian->kondisi_barang }}</span>
              @else
                <span class="badge text-bg-danger">{{ $pengembalian->kondisi_barang }}</span>
              @endif
            </p>
          </div>
          <div class="col-md-6"><p class="text-muted small mb-1">Diterima Oleh</p><p class="fw-semibold mb-0">{{ $pengembalian->petugas->nama ?? '-' }}</p></div>
          @if ($pengembalian->catatan)
            <div class="col-12"><p class="text-muted small mb-1">Catatan</p><p class="mb-0">{{ $pengembalian->catatan }}</p></div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-5">
      <div class="panel h-100">
        <div class="panel-header">
          <div>
            <h2 class="h5 mb-1 section-title"><i class="bi bi-box-seam" aria-hidden="true"></i><span>Barang Dikembalikan</span></h2>
            <p class="text-muted mb-0">{{ $pengembalian->transaksi->detailTransaksi->sum('jumlah') }} item.</p>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead><tr><th>Barang</th><th>Kode</th><th class="text-end">Jumlah</th></tr></thead>
            <tbody>
              @foreach ($pengembalian->transaksi->detailTransaksi as $detail)
                <tr>
                  <td class="fw-semibold">{{ $detail->barang->nama_barang }}</td>
                  <td class="text-muted">{{ $detail->barang->kode_barang }}</td>
                  <td class="text-end">{{ $detail->jumlah }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
@endsection
