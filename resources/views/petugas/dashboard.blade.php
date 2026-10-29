@extends('layouts.admin')

@section('title', 'Dashboard Petugas')

@section('content')
  <div class="page-heading">
    <div class="page-heading-copy">
      <span class="page-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
      <div>
        <p class="eyebrow mb-1">Overview</p>
        <h1 class="h3 mb-1">Dashboard Petugas</h1>
        <p class="text-muted mb-0">Pantau pengajuan, peminjaman berjalan, dan pengembalian.</p>
      </div>
    </div>
  </div>

  <section class="row g-3 mt-1" aria-label="Petugas metrics">
    <div class="col-12 col-sm-6 col-xl-3">
      <article class="metric-card metric-warning">
        <div class="metric-top">
          <span class="metric-label">Menunggu</span>
          <span class="metric-icon"><i class="bi bi-hourglass-split" aria-hidden="true"></i></span>
        </div>
        <div class="metric-value">{{ $menunggu }}</div>
        <div class="metric-meta"><span>pengajuan perlu disetujui</span></div>
      </article>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
      <article class="metric-card metric-primary">
        <div class="metric-top">
          <span class="metric-label">Disetujui</span>
          <span class="metric-icon"><i class="bi bi-check2-circle" aria-hidden="true"></i></span>
        </div>
        <div class="metric-value">{{ $disetujui }}</div>
        <div class="metric-meta"><span>menunggu barang diserahkan</span></div>
      </article>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
      <article class="metric-card metric-danger">
        <div class="metric-top">
          <span class="metric-label">Dipinjam</span>
          <span class="metric-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
        </div>
        <div class="metric-value">{{ $dipinjam }}</div>
        <div class="metric-meta"><span>barang sedang dipinjam</span></div>
      </article>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
      <article class="metric-card metric-success">
        <div class="metric-top">
          <span class="metric-label">Selesai</span>
          <span class="metric-icon"><i class="bi bi-check2-all" aria-hidden="true"></i></span>
        </div>
        <div class="metric-value">{{ $selesai }}</div>
        <div class="metric-meta"><span>total denda: {{ 'Rp ' . number_format($totalDenda, 0, ',', '.') }}</span></div>
      </article>
    </div>
  </section>

  <section class="panel mt-3">
    <div class="panel-header">
      <div>
        <h2 class="h5 mb-1 section-title"><i class="bi bi-arrow-left-right" aria-hidden="true"></i><span>Transaksi Terbaru</span></h2>
        <p class="text-muted mb-0">Aktivitas peminjaman terakhir.</p>
      </div>
      <a class="btn btn-outline-secondary btn-sm" href="{{ route('petugas.transaksi.index') }}">Lihat Semua</a>
    </div>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th>Kode</th><th>Peminjam</th><th>Jatuh Tempo</th><th>Status</th></tr></thead>
        <tbody>
          @forelse ($transaksiTerbaru as $transaksi)
            <tr>
              <td class="fw-semibold">{{ $transaksi->kode_transaksi }}</td>
              <td>{{ $transaksi->user->nama ?? '-' }}</td>
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
            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada transaksi.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection
