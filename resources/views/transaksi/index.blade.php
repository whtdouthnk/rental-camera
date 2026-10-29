@extends('layouts.admin')

@section('title', 'Transaksi')

@section('content')
  @php $role = auth()->user()->role; @endphp

  <div class="page-heading">
    <div class="page-heading-copy">
      <span class="page-icon"><i class="bi bi-arrow-left-right" aria-hidden="true"></i></span>
      <div>
        <p class="eyebrow mb-1">Operasional</p>
        <h1 class="h3 mb-1">Transaksi</h1>
        <p class="text-muted mb-0">
          @if ($role === 'Pengguna')
            Daftar pengajuan dan riwayat peminjaman Anda.
          @elseif ($role === 'Petugas')
            Kelola pengajuan peminjaman dan konfirmasi barang.
          @else
            Pantau seluruh transaksi penyewaan.
          @endif
        </p>
      </div>
    </div>
    @if ($role === 'Pengguna')
      <div class="heading-actions">
        <a class="btn btn-primary btn-sm" href="{{ route('user.transaksi.create') }}"><i class="bi bi-plus-circle" aria-hidden="true"></i> Ajukan Peminjaman</a>
      </div>
    @endif
  </div>

  <section class="panel">
    <div class="panel-header">
      <div>
        <h2 class="h5 mb-1 section-title"><i class="bi bi-list-ol" aria-hidden="true"></i><span>Data Transaksi</span></h2>
        <p class="text-muted mb-0">Daftar transaksi penyewaan kamera.</p>
      </div>
      <form class="d-flex gap-2" method="GET" action="{{ request()->url() }}">
        <select class="form-select form-select-sm table-search" name="status" onchange="this.form.submit()" aria-label="Filter status">
          <option value="">Semua status</option>
          @foreach ($statuses as $status)
            <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
          @endforeach
        </select>
      </form>
    </div>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Kode</th>
            <th scope="col">Peminjam</th>
            <th scope="col">Tgl Pinjam</th>
            <th scope="col">Jatuh Tempo</th>
            <th scope="col">Barang</th>
            <th scope="col">Biaya Sewa</th>
            <th scope="col">Status</th>
            <th scope="col" class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($transaksis as $transaksi)
            <tr>
              <td class="fw-semibold">{{ $transaksi->kode_transaksi }}</td>
              <td>{{ $transaksi->user->nama ?? '-' }}</td>
              <td>{{ \Carbon\Carbon::parse($transaksi->tgl_pinjam)->format('d M Y') }}</td>
              <td>{{ \Carbon\Carbon::parse($transaksi->tgl_jatuh_tempo)->format('d M Y') }}</td>
              <td>{{ $transaksi->detailTransaksi->sum('jumlah') }} item</td>
              <td>{{ 'Rp ' . number_format($transaksi->totalBiayaSewa(), 0, ',', '.') }}</td>
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
              <td class="text-end">
                <a class="btn btn-light btn-sm" href="{{ route($role === 'Pengguna' ? 'user.transaksi.show' : ($role === 'Petugas' ? 'petugas.transaksi.show' : 'admin.transaksi.show'), $transaksi->id) }}"><i class="bi bi-eye" aria-hidden="true"></i> Detail</a>

                @if ($role === 'Petugas')
                  @if ($transaksi->status === 'Menunggu')
                    <form class="d-inline" method="POST" action="{{ route('petugas.transaksi.approve', $transaksi->id) }}" onsubmit="return confirm('Setujui pengajuan {{ $transaksi->kode_transaksi }}?')">
                      @csrf
                      <button class="btn btn-outline-success btn-sm" type="submit"><i class="bi bi-check-lg" aria-hidden="true"></i> Setujui</button>
                    </form>
                    <form class="d-inline" method="POST" action="{{ route('petugas.transaksi.reject', $transaksi->id) }}" onsubmit="return confirm('Tolak pengajuan {{ $transaksi->kode_transaksi }}?')">
                      @csrf
                      <button class="btn btn-outline-danger btn-sm" type="submit"><i class="bi bi-x-lg" aria-hidden="true"></i> Tolak</button>
                    </form>
                  @elseif ($transaksi->status === 'Disetujui')
                    <form class="d-inline" method="POST" action="{{ route('petugas.transaksi.serah', $transaksi->id) }}" onsubmit="return confirm('Konfirmasi barang sudah diserahkan untuk {{ $transaksi->kode_transaksi }}?')">
                      @csrf
                      <button class="btn btn-outline-primary btn-sm" type="submit"><i class="bi bi-box-seam" aria-hidden="true"></i> Serahkan</button>
                    </form>
                  @elseif ($transaksi->status === 'Dipinjam')
                    <a class="btn btn-outline-info btn-sm" href="{{ route('petugas.pengembalian.create', ['transaksi' => $transaksi->id]) }}"><i class="bi bi-arrow-return-left" aria-hidden="true"></i> Proses Pengembalian</a>
                  @endif
                @endif

                @if ($role === 'Pengguna' && $transaksi->status === 'Menunggu')
                  <form class="d-inline" method="POST" action="{{ route('user.transaksi.cancel', $transaksi->id) }}" onsubmit="return confirm('Batalkan pengajuan {{ $transaksi->kode_transaksi }}?')">
                    @csrf
                    <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-x-circle" aria-hidden="true"></i> Batal</button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center text-muted py-4">Belum ada data transaksi.</td>
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
