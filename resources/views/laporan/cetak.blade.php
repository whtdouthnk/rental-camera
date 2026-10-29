<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Transaksi RentCam</title>
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
      color: #1f2933; background: #f3f4f6; margin: 0; padding: 24px;
    }
    .page { max-width: 800px; margin: 0 auto; background: #fff; border: 1px solid #e5e7eb; padding: 32px; }
    .head { text-align: center; border-bottom: 2px solid #1f2933; padding-bottom: 12px; margin-bottom: 20px; }
    .head .brand { font-size: 24px; font-weight: 800; }
    .head .title { font-size: 14px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; margin-top: 4px; }
    .meta { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 16px; }
    .meta .label { color: #6b7280; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    th { text-align: left; color: #6b7280; font-weight: 600; padding: 8px 6px; border-bottom: 2px solid #1f2933; }
    td { padding: 7px 6px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
    td.num, th.num { text-align: right; }
    .summary { margin-top: 16px; border: 1px solid #e5e7eb; border-radius: 6px; }
    .summary .row { display: flex; justify-content: space-between; padding: 8px 14px; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
    .summary .row.grand { font-weight: 800; font-size: 15px; border-bottom: 0; background: #f9fafb; }
    .sign { display: flex; justify-content: flex-end; margin-top: 48px; font-size: 12px; text-align: center; }
    .sign .box { width: 200px; }
    .sign .name { margin-top: 60px; border-top: 1px solid #1f2933; padding-top: 6px; }
    .no-print { text-align: center; margin-bottom: 16px; }
    .btn-print { background: #2563eb; color: #fff; border: 0; padding: 10px 28px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; }
    @media print {
      body { background: #fff; padding: 0; }
      .page { border: none; max-width: none; padding: 12px; }
      .no-print { display: none; }
    }
  </style>
</head>

<body>
  <div class="no-print">
    <button class="btn-print" type="button" onclick="window.print()">Cetak Laporan</button>
  </div>

  <div class="page">
    <div class="head">
      <div class="brand">RentCam</div>
      <div class="title">Laporan Transaksi Penyewaan</div>
    </div>

    <div class="meta">
      <div>
        <div><span class="label">Periode: </span>
          {{ $dari ? $dari->format('d M Y') : 'Awal' }} s/d {{ $sampai ? $sampai->format('d M Y') : 'Sekarang' }}
        </div>
        <div><span class="label">Status: </span>{{ request('status') ?: 'Semua' }}</div>
      </div>
      <div>
        <div><span class="label">Dicetak: </span>{{ now()->format('d M Y H:i') }}</div>
        <div><span class="label">Oleh: </span>{{ auth()->user()->nama }}</div>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Kode</th>
          <th>Peminjam</th>
          <th>Tgl Pinjam</th>
          <th>Jatuh Tempo</th>
          <th>Status</th>
          <th class="num">Biaya Sewa</th>
          <th class="num">Denda</th>
          <th class="num">Total</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($transaksis as $i => $transaksi)
          <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $transaksi->kode_transaksi }}</td>
            <td>{{ $transaksi->user->nama ?? '-' }}</td>
            <td>{{ \Carbon\Carbon::parse($transaksi->tgl_pinjam)->format('d M Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($transaksi->tgl_jatuh_tempo)->format('d M Y') }}</td>
            <td>{{ $transaksi->status }}</td>
            <td class="num">{{ number_format($transaksi->totalBiayaSewa(), 0, ',', '.') }}</td>
            <td class="num">{{ number_format($transaksi->pengembalian->total_denda ?? 0, 0, ',', '.') }}</td>
            <td class="num">{{ number_format($transaksi->totalBiayaSewa() + ($transaksi->pengembalian->total_denda ?? 0), 0, ',', '.') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="9" style="text-align:center; padding: 24px; color:#9ca3af;">Tidak ada transaksi pada rentang tanggal tersebut.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div class="summary">
      <div class="row"><span>Jumlah Transaksi</span><strong>{{ $totalTransaksi }}</strong></div>
      <div class="row"><span>Total Biaya Sewa</span><strong>Rp {{ number_format($totalSewa, 0, ',', '.') }}</strong></div>
      <div class="row"><span>Total Denda</span><strong>Rp {{ number_format($totalDenda, 0, ',', '.') }}</strong></div>
      <div class="row grand"><span>Total Pendapatan</span><strong>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong></div>
    </div>

    <div class="sign">
      <div class="box">
        <div>Mengetahui,<br>Admin</div>
        <div class="name">{{ auth()->user()->nama }}</div>
      </div>
    </div>
  </div>
</body>

</html>
