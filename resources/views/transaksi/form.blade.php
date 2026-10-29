@extends('layouts.admin')

@section('title', 'Ajukan Peminjaman')

@section('content')
  <div class="page-heading">
    <div class="page-heading-copy">
      <span class="page-icon"><i class="bi bi-plus-circle" aria-hidden="true"></i></span>
      <div>
        <p class="eyebrow mb-1">Peminjaman</p>
        <h1 class="h3 mb-1">Ajukan Peminjaman</h1>
        <p class="text-muted mb-0">Pilih barang yang ingin dipinjam beserta tanggal peminjaman.</p>
      </div>
    </div>
    <div class="heading-actions">
      <a class="btn btn-outline-secondary btn-sm" href="{{ route('user.transaksi.index') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali</a>
    </div>
  </div>

  <form class="panel needs-validation" method="POST" action="{{ route('user.transaksi.store') }}" novalidate>
    @csrf

    <div class="panel-header">
      <div>
        <h2 class="h5 mb-1 section-title"><i class="bi bi-box-seam" aria-hidden="true"></i><span>Barang yang Dipinjam</span></h2>
        <p class="text-muted mb-0">Tambahkan satu atau lebih barang yang tersedia.</p>
      </div>
    </div>

    <div id="barangRows">
      <div class="row g-3 barang-row">
        <div class="col-md-8">
          <label class="form-label">Barang</label>
          <select class="form-select" name="barang_id[]" required>
            <option value="">Pilih barang</option>
            @foreach ($barangs as $barang)
              <option value="{{ $barang->id }}" data-stok="{{ $barang->stok_tersedia }}" data-harga="{{ $barang->harga_sewa_per_hari }}">{{ $barang->kode_barang }} — {{ $barang->nama_barang }} (sewa {{ 'Rp ' . number_format($barang->harga_sewa_per_hari, 0, ',', '.') }}/hari, stok {{ $barang->stok_tersedia }})</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Jumlah</label>
          <input class="form-control" type="number" name="jumlah[]" min="1" value="1" required>
        </div>
        <div class="col-md-1 d-flex align-items-end">
          <button class="btn btn-outline-danger btn-sm remove-row" type="button" title="Hapus baris"><i class="bi bi-trash" aria-hidden="true"></i></button>
        </div>
      </div>
    </div>

    @error('barang_id')<div class="alert alert-danger mt-3 mb-0 py-2">{{ $message }}</div>@enderror

    <div class="mt-3">
      <button class="btn btn-outline-secondary btn-sm" type="button" id="addRow"><i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah Barang</button>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-md-6">
        <label class="form-label" for="tgl_pinjam">Tanggal Pinjam</label>
        <input class="form-control @error('tgl_pinjam') is-invalid @enderror" id="tgl_pinjam" name="tgl_pinjam" type="date" value="{{ old('tgl_pinjam') }}" required>
        @error('tgl_pinjam')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-6">
        <label class="form-label" for="tgl_jatuh_tempo">Tanggal Jatuh Tempo</label>
        <input class="form-control @error('tgl_jatuh_tempo') is-invalid @enderror" id="tgl_jatuh_tempo" name="tgl_jatuh_tempo" type="date" value="{{ old('tgl_jatuh_tempo') }}" required>
        @error('tgl_jatuh_tempo')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-12">
        <label class="form-label" for="catatan">Catatan</label>
        <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="3" placeholder="Opsional">{{ old('catatan') }}</textarea>
        @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="mt-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 bg-light border rounded p-3">
        <div>
          <p class="text-muted small mb-1">Lama sewa: <span class="fw-semibold" id="durasiPreview">-</span></p>
          <p class="text-muted small mb-1">Estimasi biaya sewa</p>
          <p class="h4 mb-0" id="biayaPreview">{{ 'Rp ' . number_format(0, 0, ',', '.') }}</p>
        </div>
        <div class="d-flex gap-2">
          <a class="btn btn-outline-secondary" href="{{ route('user.transaksi.index') }}">Batal</a>
          <button class="btn btn-primary" type="submit"><i class="bi bi-send" aria-hidden="true"></i> Ajukan</button>
        </div>
      </div>
    </div>
  </form>
@endsection

@section('scripts')
  <script>
    (function () {
      var rows = document.getElementById('barangRows');
      var template = rows.querySelector('.barang-row');
      var addBtn = document.getElementById('addRow');
      var tglPinjam = document.getElementById('tgl_pinjam');
      var tglJatuhTempo = document.getElementById('tgl_jatuh_tempo');
      var biayaPreview = document.getElementById('biayaPreview');
      var durasiPreview = document.getElementById('durasiPreview');

      function formatRupiah(value) {
        return 'Rp ' + Number(value).toLocaleString('id-ID');
      }

      function hitungDurasi() {
        if (!tglPinjam.value || !tglJatuhTempo.value) return 0;
        var mulai = new Date(tglPinjam.value + 'T00:00:00');
        var selesai = new Date(tglJatuhTempo.value + 'T00:00:00');
        if (selesai < mulai) return 0;
        return Math.max(Math.floor((selesai - mulai) / 86400000), 1);
      }

      function updateBiaya() {
        var durasi = hitungDurasi();
        durasiPreview.textContent = durasi > 0 ? durasi + ' hari' : '-';

        var total = 0;
        Array.prototype.forEach.call(rows.querySelectorAll('.barang-row'), function (row) {
          var sel = row.querySelector('select');
          var jml = parseInt(row.querySelector('input[type="number"]').value, 10) || 0;
          var opt = sel.options[sel.selectedIndex];
          var harga = opt ? parseFloat(opt.getAttribute('data-harga')) || 0 : 0;
          total += harga * jml;
        });

        biayaPreview.textContent = formatRupiah(total * durasi);
      }

      function syncRemoveButtons() {
        var currentRows = rows.querySelectorAll('.barang-row');
        Array.prototype.forEach.call(currentRows, function (row, index) {
          var btn = row.querySelector('.remove-row');
          if (btn) {
            btn.style.visibility = currentRows.length > 1 ? 'visible' : 'hidden';
          }
        });
      }

      addBtn.addEventListener('click', function () {
        var clone = template.cloneNode(true);
        clone.querySelectorAll('input, select').forEach(function (field) {
          field.value = field.type === 'number' ? '1' : '';
        });
        rows.appendChild(clone);
        syncRemoveButtons();
      });

      rows.addEventListener('click', function (event) {
        var btn = event.target.closest('.remove-row');
        if (!btn) return;
        var row = btn.closest('.barang-row');
        if (rows.querySelectorAll('.barang-row').length > 1) {
          row.remove();
          syncRemoveButtons();
        }
      });

      rows.addEventListener('change', updateBiaya);
      rows.addEventListener('input', updateBiaya);
      tglPinjam.addEventListener('change', updateBiaya);
      tglJatuhTempo.addEventListener('change', updateBiaya);

      syncRemoveButtons();
      updateBiaya();
    })();
  </script>
@endsection
