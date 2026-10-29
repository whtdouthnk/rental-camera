@extends('layouts.admin')

@section('title', 'Dashboard Pengguna')

@section('content')
  <div class="page-heading">
    <div class="page-heading-copy">
      <span class="page-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
      <div>
        <p class="eyebrow mb-1">Overview</p>
        <h1 class="h3 mb-1">Dashboard Pengguna</h1>
        <p class="text-muted mb-0">Ajukan peminjaman kamera dan pantau status transaksi Anda.</p>
      </div>
    </div>
    <div class="heading-actions">
      <a class="btn btn-primary btn-sm" href="{{ route('user.transaksi.create') }}"><i class="bi bi-plus-circle" aria-hidden="true"></i> Ajukan Peminjaman</a>
    </div>
  </div>

  <section class="row g-3 mt-1" aria-label="Pengguna metrics">
    <div class="col-12 col-sm-6 col-xl-3">
      <article class="metric-card metric-primary">
        <div class="metric-top">
          <span class="metric-label">Total Peminjaman</span>
          <span class="metric-icon"><i class="bi bi-arrow-left-right" aria-hidden="true"></i></span>
        </div>
        <div class="metric-value">{{ $totalPeminjaman }}</div>
        <div class="metric-meta"><span>transaksi pernah diajukan</span></div>
      </article>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
      <article class="metric-card metric-warning">
        <div class="metric-top">
          <span class="metric-label">Menunggu</span>
          <span class="metric-icon"><i class="bi bi-hourglass-split" aria-hidden="true"></i></span>
        </div>
        <div class="metric-value">{{ $menunggu }}</div>
        <div class="metric-meta"><span>menunggu persetujuan petugas</span></div>
      </article>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
      <article class="metric-card metric-danger">
        <div class="metric-top">
          <span class="metric-label">Dipinjam</span>
          <span class="metric-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
        </div>
        <div class="metric-value">{{ $dipinjam }}</div>
        <div class="metric-meta"><span>barang sedang Anda pinjam</span></div>
      </article>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
      <article class="metric-card metric-success">
        <div class="metric-top">
          <span class="metric-label">Selesai</span>
          <span class="metric-icon"><i class="bi bi-check2-all" aria-hidden="true"></i></span>
        </div>
        <div class="metric-value">{{ $selesai }}</div>
        <div class="metric-meta"><span>{{ $barangTersedia }} barang tersedia</span></div>
      </article>
    </div>
  </section>

  <section class="panel mt-3">
    <div class="panel-header">
      <div>
        <h2 class="h5 mb-1 section-title"><i class="bi bi-clock-history" aria-hidden="true"></i><span>Riwayat Peminjaman</span></h2>
        <p class="text-muted mb-0">Transaksi peminjaman Anda terbaru.</p>
      </div>
      <a class="btn btn-outline-secondary btn-sm" href="{{ route('user.transaksi.index') }}">Lihat Semua</a>
    </div>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>Kode</th><th>Tanggal Pinjam</th><th>Jatuh Tempo</th><th>Status</th></tr></thead>
        <tbody>
          @forelse ($transaksiTerbaru as $transaksi)
            <tr>
              <td class="fw-semibold">{{ $transaksi->kode_transaksi }}</td>
              <td>{{ \Carbon\Carbon::parse($transaksi->tgl_pinjam)->format('d M Y') }}</td>
              <td>{{ \Carbon\Carbon::parse($transaksi->tgl_jatuh_tempo)->format('d M Y') }}</td>
              <td>
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
                <span class="badge {{ $badge }}">{{ $transaksi->status }}</span>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada transaksi. Ajukan peminjaman pertama Anda.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection
