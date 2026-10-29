@extends('layouts.admin')

@section('title', 'Detail Transaksi')

@section('content')
  @php $role = auth()->user()->role; @endphp

  <div class="page-heading">
    <div class="page-heading-copy">
      <span class="page-icon"><i class="bi bi-receipt" aria-hidden="true"></i></span>
      <div>
        <p class="eyebrow mb-1">Transaksi</p>
        <h1 class="h3 mb-1">{{ $transaksi->kode_transaksi }}</h1>
        <p class="text-muted mb-0">Detail transaksi penyewaan kamera.</p>
      </div>
    </div>
    <div class="heading-actions">
      @if ($role === 'Petugas' && in_array($transaksi->status, ['Dipinjam', 'Selesai']) && $transaksi->pembayaran)
        <a class="btn btn-outline-primary btn-sm" target="_blank" href="{{ route('petugas.transaksi.bukti', $transaksi->id) }}"><i class="bi bi-printer" aria-hidden="true"></i> Cetak Bukti Pembayaran</a>
      @endif
      <a class="btn btn-outline-secondary btn-sm" href="{{ route($role === 'Pengguna' ? 'user.transaksi.index' : ($role === 'Petugas' ? 'petugas.transaksi.index' : 'admin.transaksi.index')) }}"><i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali</a>
    </div>
  </div>

  @php
    $badge = match ($transaksi->status) {
      'Menunggu' => 'text-bg-warning',
      'Disetujui' => 'text-bg-primary',
      'Ditolak' => 'text-bg-danger',
      'Dipinjam' => 'text-bg-info',
      'Selesai' => 'text-bg-success',
      'Dibatalkan' => 'text-bg-secondary',
    };
  @endphp

  <section class="row g-3">
    <div class="col-12 col-xl-7">
      <div class="panel">
        <div class="panel-header">
          <div>
            <h2 class="h5 mb-1 section-title"><i class="bi bi-info-circle" aria-hidden="true"></i><span>Informasi Peminjaman</span></h2>
            <span class="badge {{ $badge }}">{{ $transaksi->status }}</span>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-md-6"><p class="text-muted small mb-1">Peminjam</p><p class="fw-semibold mb-0">{{ $transaksi->user->nama }}</p><p class="text-muted small mb-0">{{ $transaksi->user->email }}</p></div>
          <div class="col-md-6"><p class="text-muted small mb-1">No. HP</p><p class="fw-semibold mb-0">{{ $transaksi->user->no_hp ?? '-' }}</p></div>
          <div class="col-md-6"><p class="text-muted small mb-1">Tanggal Pinjam</p><p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($transaksi->tgl_pinjam)->format('d M Y') }}</p></div>
          <div class="col-md-6"><p class="text-muted small mb-1">Jatuh Tempo</p><p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($transaksi->tgl_jatuh_tempo)->format('d M Y') }}</p></div>
          <div class="col-md-6"><p class="text-muted small mb-1">Lama Sewa</p><p class="fw-semibold mb-0">{{ $transaksi->durasiHari() }} hari</p></div>
          <div class="col-md-6"><p class="text-muted small mb-1">Diajukan</p><p class="fw-semibold mb-0">{{ $transaksi->created_at ? $transaksi->created_at->format('d M Y H:i') : '-' }}</p></div>
          <div class="col-md-6"><p class="text-muted small mb-1">Diproses Oleh</p><p class="fw-semibold mb-0">{{ $transaksi->petugas->nama ?? '-' }}</p></div>
          @if ($transaksi->pembayaran)
            <div class="col-12 mt-3 pt-3 border-top">
              <div class="row g-3">
                <div class="col-md-4"><p class="text-muted small mb-1">Pembayaran</p><p class="fw-semibold mb-0">{{ $transaksi->pembayaran->metode }} — {{ 'Rp ' . number_format($transaksi->pembayaran->jumlah, 0, ',', '.') }}</p></div>
                <div class="col-md-4"><p class="text-muted small mb-1">Dibayar Pada</p><p class="fw-semibold mb-0">{{ $transaksi->pembayaran->dibayar_pada ? $transaksi->pembayaran->dibayar_pada->format('d M Y H:i') : '-' }}</p></div>
                <div class="col-md-4"><p class="text-muted small mb-1">Diterima Oleh</p><p class="fw-semibold mb-0">{{ $transaksi->pembayaran->petugas->nama ?? '-' }}</p></div>
              </div>
            </div>
          @endif
          @if ($transaksi->catatan)
            <div class="col-12"><p class="text-muted small mb-1">Catatan</p><p class="mb-0">{{ $transaksi->catatan }}</p></div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-5">
      <div class="panel h-100">
        <div class="panel-header">
          <div>
            <h2 class="h5 mb-1 section-title"><i class="bi bi-box-seam" aria-hidden="true"></i><span>Barang Dipinjam</span></h2>
            <p class="text-muted mb-0">{{ $transaksi->detailTransaksi->sum('jumlah') }} item.</p>
          </div>
        </div>
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead><tr><th>Barang</th><th>Kode</th><th class="text-end">Jumlah</th><th class="text-end">Sewa/Hari</th><th class="text-end">Biaya</th></tr></thead>
            <tbody>
              @foreach ($transaksi->detailTransaksi as $detail)
                <tr>
                  <td class="fw-semibold">{{ $detail->barang->nama_barang }}</td>
                  <td class="text-muted">{{ $detail->barang->kode_barang }}</td>
                  <td class="text-end">{{ $detail->jumlah }}</td>
                  <td class="text-end">{{ 'Rp ' . number_format($detail->barang->harga_sewa_per_hari, 0, ',', '.') }}</td>
                  <td class="text-end">{{ 'Rp ' . number_format($detail->jumlah * $detail->barang->harga_sewa_per_hari * $transaksi->durasiHari(), 0, ',', '.') }}</td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr class="table-light">
                <td colspan="4" class="text-end fw-semibold">Total Biaya Sewa ({{ $transaksi->durasiHari() }} hari)</td>
                <td class="text-end fw-semibold">{{ 'Rp ' . number_format($transaksi->totalBiayaSewa(), 0, ',', '.') }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    @if ($transaksi->pengembalian)
      <div class="col-12">
        <div class="panel">
          <div class="panel-header">
            <div>
              <h2 class="h5 mb-1 section-title"><i class="bi bi-arrow-return-left" aria-hidden="true"></i><span>Pengembalian</span></h2>
              <p class="text-muted mb-0">Informasi pengembalian barang.</p>
            </div>
            <a class="btn btn-light btn-sm" href="{{ route($role === 'Petugas' ? 'petugas.pengembalian.show' : 'admin.pengembalian.show', $transaksi->pengembalian->id) }}">Lihat Detail</a>
          </div>
          <div class="row g-3">
            <div class="col-md-4"><p class="text-muted small mb-1">Tanggal Kembali</p><p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($transaksi->pengembalian->tgl_kembali)->format('d M Y') }}</p></div>
            <div class="col-md-4"><p class="text-muted small mb-1">Keterlambatan</p><p class="fw-semibold mb-0">{{ $transaksi->pengembalian->terlambat_hari }} hari</p></div>
            <div class="col-md-4"><p class="text-muted small mb-1">Total Denda</p><p class="fw-semibold mb-0">{{ 'Rp ' . number_format($transaksi->pengembalian->total_denda, 0, ',', '.') }}</p></div>
            <div class="col-md-4"><p class="text-muted small mb-1">Biaya Sewa</p><p class="fw-semibold mb-0">{{ 'Rp ' . number_format($transaksi->totalBiayaSewa(), 0, ',', '.') }}</p></div>
            <div class="col-md-4"><p class="text-muted small mb-1">Total Tagihan</p><p class="fw-semibold mb-0 text-primary">{{ 'Rp ' . number_format($transaksi->totalBiayaSewa() + $transaksi->pengembalian->total_denda, 0, ',', '.') }}</p></div>
            <div class="col-md-4"><p class="text-muted small mb-1">Kondisi Barang</p><p class="fw-semibold mb-0">{{ $transaksi->pengembalian->kondisi_barang }}</p></div>
            <div class="col-md-4"><p class="text-muted small mb-1">Diterima Oleh</p><p class="fw-semibold mb-0">{{ $transaksi->pengembalian->petugas->nama ?? '-' }}</p></div>
          </div>
        </div>
      </div>
    @endif
  </section>
@endsection
