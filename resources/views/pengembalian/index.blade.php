@extends('layouts.admin')

@section('title', 'Pengembalian')

@section('content')
  @php $role = auth()->user()->role; @endphp

  <div class="page-heading">
    <div class="page-heading-copy">
      <span class="page-icon"><i class="bi bi-arrow-return-left" aria-hidden="true"></i></span>
      <div>
        <p class="eyebrow mb-1">Operasional</p>
        <h1 class="h3 mb-1">Pengembalian</h1>
        <p class="text-muted mb-0">Riwayat pengembalian barang beserta denda keterlambatan.</p>
      </div>
    </div>
    @if ($role === 'Petugas')
      <div class="heading-actions">
        <a class="btn btn-primary btn-sm" href="{{ route('petugas.transaksi.index', ['status' => 'Dipinjam']) }}"><i class="bi bi-plus-circle" aria-hidden="true"></i> Proses Pengembalian</a>
      </div>
    @endif
  </div>

  <section class="panel">
    <div class="panel-header">
      <div>
        <h2 class="h5 mb-1 section-title"><i class="bi bi-arrow-return-left" aria-hidden="true"></i><span>Data Pengembalian</span></h2>
        <p class="text-muted mb-0">Daftar seluruh proses pengembalian.</p>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th scope="col">Transaksi</th>
            <th scope="col">Peminjam</th>
            <th scope="col">Tgl Kembali</th>
            <th scope="col">Terlambat</th>
            <th scope="col">Denda</th>
            <th scope="col">Tagihan</th>
            <th scope="col">Kondisi</th>
            <th scope="col" class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($pengembalians as $pengembalian)
            <tr>
              <td class="fw-semibold">{{ $pengembalian->transaksi->kode_transaksi ?? '-' }}</td>
              <td>{{ $pengembalian->transaksi->user->nama ?? '-' }}</td>
              <td>{{ \Carbon\Carbon::parse($pengembalian->tgl_kembali)->format('d M Y') }}</td>
              <td>
                @if ($pengembalian->terlambat_hari > 0)
                  <span class="badge text-bg-danger">{{ $pengembalian->terlambat_hari }} hari</span>
                @else
                  <span class="badge text-bg-success">Tepat waktu</span>
                @endif
              </td>
              <td>{{ 'Rp ' . number_format($pengembalian->total_denda, 0, ',', '.') }}</td>
              <td class="fw-semibold">{{ 'Rp ' . number_format($pengembalian->transaksi->totalBiayaSewa() + $pengembalian->total_denda, 0, ',', '.') }}</td>
              <td>
                @if ($pengembalian->kondisi_barang === 'Baik')
                  <span class="badge text-bg-success">{{ $pengembalian->kondisi_barang }}</span>
                @elseif ($pengembalian->kondisi_barang === 'Rusak Ringan')
                  <span class="badge text-bg-warning">{{ $pengembalian->kondisi_barang }}</span>
                @else
                  <span class="badge text-bg-danger">{{ $pengembalian->kondisi_barang }}</span>
                @endif
              </td>
              <td class="text-end">
                <a class="btn btn-light btn-sm" href="{{ route($role === 'Petugas' ? 'petugas.pengembalian.show' : 'admin.pengembalian.show', $pengembalian->id) }}"><i class="bi bi-eye" aria-hidden="true"></i> Detail</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center text-muted py-4">Belum ada data pengembalian.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="p-3">
      {{ $pengembalians->links('pagination::bootstrap-5') }}
    </div>
  </section>
@endsection
