@extends('layouts.admin')

@section('title', 'Laporan Transaksi')

@section('content')
  <div class="page-heading">
    <div class="page-heading-copy">
      <span class="page-icon"><i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i></span>
      <div>
        <p class="eyebrow mb-1">Operasional</p>
        <h1 class="h3 mb-1">Laporan Transaksi</h1>
        <p class="text-muted mb-0">Filter transaksi berdasarkan tanggal, lalu cetak laporannya.</p>
      </div>
    </div>
  </div>

  <section class="panel">
    <form class="panel-header" method="GET" action="{{ route('admin.laporan.index') }}">
      <div class="d-flex flex-wrap align-items-end gap-2">
        <div>
          <label class="form-label small mb-1" for="dari">Tanggal Awal</label>
          <input class="form-control form-control-sm" id="dari" name="dari" type="date" value="{{ request('dari') }}">
        </div>
        <div>
          <label class="form-label small mb-1" for="sampai">Tanggal Akhir</label>
          <input class="form-control form-control-sm" id="sampai" name="sampai" type="date" value="{{ request('sampai') }}">
        </div>
        <div>
          <label class="form-label small mb-1" for="status">Status</label>
          <select class="form-select form-select-sm" id="status" name="status">
            <option value="">Semua status</option>
            @foreach (['Menunggu', 'Disetujui', 'Ditolak', 'Dipinjam', 'Selesai', 'Dibatalkan'] as $status)
              <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
            @endforeach
          </select>
        </div>
        <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-funnel" aria-hidden="true"></i> Tampilkan</button>
        <a class="btn btn-outline-primary btn-sm" target="_blank" href="{{ route('admin.laporan.cetak', request()->query()) }}"><i class="bi bi-printer" aria-hidden="true"></i> Cetak Laporan</a>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">No</th>
            <th scope="col">Kode</th>
            <th scope="col">Peminjam</th>
            <th scope="col">Tgl Pinjam</th>
            <th scope="col">Jatuh Tempo</th>
            <th scope="col">Status</th>
            <th scope="col" class="text-end">Biaya Sewa</th>
            <th scope="col" class="text-end">Denda</th>
            <th scope="col" class="text-end">Total</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($transaksis as $i => $transaksi)
            <tr>
              <td>{{ $transaksis->firstItem() + $i }}</td>
              <td class="fw-semibold">{{ $transaksi->kode_transaksi }}</td>
              <td>{{ $transaksi->user->nama ?? '-' }}</td>
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
              <td class="text-end">{{ 'Rp ' . number_format($transaksi->totalBiayaSewa(), 0, ',', '.') }}</td>
              <td class="text-end">{{ 'Rp ' . number_format($transaksi->pengembalian->total_denda ?? 0, 0, ',', '.') }}</td>
              <td class="text-end fw-semibold">{{ 'Rp ' . number_format($transaksi->totalBiayaSewa() + ($transaksi->pengembalian->total_denda ?? 0), 0, ',', '.') }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center text-muted py-4">Tidak ada transaksi pada rentang tanggal tersebut.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">
      {{ $transaksis->links('pagination::bootstrap-5') }}
    </div>
  </section>
@endsection
