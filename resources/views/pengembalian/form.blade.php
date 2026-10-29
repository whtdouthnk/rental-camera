@extends('layouts.admin')

@section('title', 'Proses Pengembalian')

@section('content')
  <div class="page-heading">
    <div class="page-heading-copy">
      <span class="page-icon"><i class="bi bi-arrow-return-left" aria-hidden="true"></i></span>
      <div>
        <p class="eyebrow mb-1">Operasional</p>
        <h1 class="h3 mb-1">Proses Pengembalian</h1>
        <p class="text-muted mb-0">Catat pengembalian barang dan denda dihitung otomatis.</p>
      </div>
    </div>
    <div class="heading-actions">
      <a class="btn btn-outline-secondary btn-sm" href="{{ route('petugas.pengembalian.index') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali</a>
    </div>
  </div>

  <form class="panel needs-validation" method="POST" action="{{ route('petugas.pengembalian.store') }}" novalidate>
    @csrf
    <input type="hidden" name="transaksi_id" value="{{ $transaksi->id }}">

    <div class="panel-header">
      <div>
        <h2 class="h5 mb-1 section-title"><i class="bi bi-receipt" aria-hidden="true"></i><span>{{ $transaksi->kode_transaksi }}</span></h2>
        <p class="text-muted mb-0">Peminjam: {{ $transaksi->user->nama }} ({{ $transaksi->user->email }})</p>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-md-6">
        <p class="text-muted small mb-1">Tanggal Pinjam</p>
        <p class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($transaksi->tgl_pinjam)->format('d M Y') }}</p>
      </div>
      <div class="col-md-6">
        <p class="text-muted small mb-1">Jatuh Tempo</p>
        <p class="fw-semibold mb-0" id="jatuhTempo" data-tanggal="{{ $transaksi->tgl_jatuh_tempo }}">{{ \Carbon\Carbon::parse($transaksi->tgl_jatuh_tempo)->format('d M Y') }}</p>
      </div>

      <div class="col-12">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead><tr><th>Barang</th><th>Kode</th><th class="text-end">Jumlah</th><th class="text-end">Sewa/Hari</th><th class="text-end">Denda/Hari</th></tr></thead>
            <tbody>
              @foreach ($transaksi->detailTransaksi as $detail)
                <tr>
                  <td class="fw-semibold">{{ $detail->barang->nama_barang }}</td>
                  <td class="text-muted">{{ $detail->barang->kode_barang }}</td>
                  <td class="text-end">{{ $detail->jumlah }}</td>
                  <td class="text-end">{{ 'Rp ' . number_format($detail->barang->harga_sewa_per_hari, 0, ',', '.') }}</td>
                  <td class="text-end">{{ 'Rp ' . number_format($detail->barang->denda_per_hari, 0, ',', '.') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <p class="text-muted small mt-2 mb-0">Total denda per hari: <strong>{{ 'Rp ' . number_format($dendaPerHari, 0, ',', '.') }}</strong></p>
      </div>

      <div class="col-md-6">
        <label class="form-label" for="tgl_kembali">Tanggal Kembali</label>
        <input class="form-control @error('tgl_kembali') is-invalid @enderror" id="tgl_kembali" name="tgl_kembali" type="date" value="{{ old('tgl_kembali', now()->format('Y-m-d')) }}" required>
        @error('tgl_kembali')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-6">
        <label class="form-label" for="kondisi_barang">Kondisi Barang</label>
        <select class="form-select @error('kondisi_barang') is-invalid @enderror" id="kondisi_barang" name="kondisi_barang" required>
          <option value="">Pilih kondisi</option>
          @foreach (['Baik', 'Rusak Ringan', 'Rusak Berat'] as $kondisi)
            <option value="{{ $kondisi }}" @selected(old('kondisi_barang') === $kondisi)>{{ $kondisi }}</option>
          @endforeach
        </select>
        @error('kondisi_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-12">
        <label class="form-label" for="catatan">Catatan</label>
        <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="3" placeholder="Opsional">{{ old('catatan') }}</textarea>
        @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4">
      <div>
        <p class="text-muted small mb-1">Biaya sewa ({{ $transaksi->durasiHari() }} hari)</p>
        <p class="fw-semibold mb-2">{{ 'Rp ' . number_format($biayaSewa, 0, ',', '.') }}</p>
        <p class="text-muted small mb-1">Denda keterlambatan</p>
        <p class="fw-semibold mb-2" id="dendaPreview">{{ 'Rp ' . number_format(0, 0, ',', '.') }}</p>
        <p class="text-muted small mb-1">Total tagihan</p>
        <p class="h4 mb-0" id="totalPreview">{{ 'Rp ' . number_format($biayaSewa, 0, ',', '.') }}</p>
      </div>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('petugas.pengembalian.index') }}">Batal</a>
        <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg" aria-hidden="true"></i> Simpan Pengembalian</button>
      </div>
    </div>
  </form>
@endsection

@section('scripts')
  <script>
    (function () {
      var jatuhTempoEl = document.getElementById('jatuhTempo');
      var tanggalKembali = document.getElementById('tgl_kembali');
      var previewEl = document.getElementById('dendaPreview');
      var totalEl = document.getElementById('totalPreview');
      var dendaPerHari = {{ $dendaPerHari }};
      var biayaSewa = {{ $biayaSewa }};

      function formatRupiah(value) {
        return 'Rp ' + Number(value).toLocaleString('id-ID');
      }

      function updatePreview() {
        if (!jatuhTempoEl || !tanggalKembali.value) {
          previewEl.textContent = formatRupiah(0);
          totalEl.textContent = formatRupiah(biayaSewa);
          return;
        }

        var jatuhTempo = new Date(jatuhTempoEl.dataset.tanggal + 'T00:00:00');
        var kembali = new Date(tanggalKembali.value + 'T00:00:00');

        var terlambat = Math.floor((kembali - jatuhTempo) / 86400000);
        if (terlambat < 0) terlambat = 0;

        var denda = terlambat * dendaPerHari;

        previewEl.textContent = formatRupiah(denda);
        totalEl.textContent = formatRupiah(biayaSewa + denda);
      }

      tanggalKembali.addEventListener('change', updatePreview);
      updatePreview();
    })();
  </script>
@endsection
