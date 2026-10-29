<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bukti Pembayaran {{ $transaksi->kode_transaksi }}</title>
  <style>
    * { box-sizing: border-box; }
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
      color: #1f2933;
      background: #f3f4f6;
      margin: 0;
      padding: 24px;
    }
    .receipt {
      max-width: 480px;
      margin: 0 auto;
      background: #fff;
      border: 1px solid #e5e7eb;
      padding: 28px;
    }
    .receipt-header { text-align: center; border-bottom: 2px solid #1f2933; padding-bottom: 16px; margin-bottom: 20px; }
    .receipt-header .brand { font-size: 22px; font-weight: 800; letter-spacing: .5px; }
    .receipt-header .title { font-size: 13px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; margin-top: 4px; }
    .row { display: flex; justify-content: space-between; padding: 3px 0; font-size: 13px; }
    .row .label { color: #6b7280; }
    .row .value { font-weight: 600; text-align: right; }
    .divider { border-top: 1px dashed #cbd5e1; margin: 12px 0; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; margin: 8px 0; }
    th { text-align: left; color: #6b7280; font-weight: 600; padding: 6px 4px; border-bottom: 1px solid #e5e7eb; }
    td { padding: 6px 4px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
    td.num, th.num { text-align: right; }
    .total { background: #f9fafb; }
    .total td { font-size: 14px; font-weight: 700; }
    .paid {
      text-align: center; margin: 16px 0; padding: 10px; border: 2px solid #059669;
      color: #059669; font-weight: 800; letter-spacing: 2px; font-size: 15px;
    }
    .sign { display: flex; justify-content: space-between; margin-top: 40px; font-size: 12px; text-align: center; }
    .sign .box { width: 45%; }
    .sign .name { margin-top: 52px; border-top: 1px solid #1f2933; padding-top: 6px; }
    .footer-note { text-align: center; color: #9ca3af; font-size: 11px; margin-top: 20px; }
    .no-print { text-align: center; margin-bottom: 16px; }
    .btn-print {
      background: #2563eb; color: #fff; border: 0; padding: 10px 28px; border-radius: 6px;
      font-size: 14px; font-weight: 600; cursor: pointer;
    }
    @media print {
      body { background: #fff; padding: 0; }
      .receipt { border: none; max-width: none; padding: 8px; }
      .no-print { display: none; }
    }
  </style>
</head>

<body>
  <div class="no-print">
    <button class="btn-print" type="button" onclick="window.print()"><i class="bi bi-printer"></i> Cetak Bukti</button>
  </div>

  <div class="receipt">
    <div class="receipt-header">
      <div class="brand">RentCam</div>
      <div class="title">Bukti Pembayaran Sewa</div>
    </div>

    <div class="row"><span class="label">No. Transaksi</span><span class="value">{{ $transaksi->kode_transaksi }}</span></div>
    <div class="row"><span class="label">Tanggal Bayar</span><span class="value">{{ $transaksi->pembayaran->dibayar_pada ? $transaksi->pembayaran->dibayar_pada->format('d M Y H:i') : '-' }}</span></div>
    <div class="row"><span class="label">Metode Pembayaran</span><span class="value">Tunai</span></div>
    <div class="divider"></div>

    <div class="row"><span class="label">Peminjam</span><span class="value">{{ $transaksi->user->nama }}</span></div>
    <div class="row"><span class="label">No. HP</span><span class="value">{{ $transaksi->user->no_hp ?? '-' }}</span></div>
    <div class="row"><span class="label">Diterima Oleh</span><span class="value">{{ $transaksi->pembayaran->petugas->nama ?? '-' }}</span></div>
    <div class="divider"></div>

    <div class="row"><span class="label">Tanggal Pinjam</span><span class="value">{{ \Carbon\Carbon::parse($transaksi->tgl_pinjam)->format('d M Y') }}</span></div>
    <div class="row"><span class="label">Jatuh Tempo</span><span class="value">{{ \Carbon\Carbon::parse($transaksi->tgl_jatuh_tempo)->format('d M Y') }}</span></div>
    <div class="row"><span class="label">Lama Sewa</span><span class="value">{{ $transaksi->durasiHari() }} hari</span></div>

    <table>
      <thead>
        <tr><th>Barang</th><th>Jml</th><th class="num">Sewa/Hari</th><th class="num">Biaya</th></tr>
      </thead>
      <tbody>
        @foreach ($transaksi->detailTransaksi as $detail)
          <tr>
            <td>{{ $detail->barang->nama_barang }}<br><small>{{ $detail->barang->kode_barang }}</small></td>
            <td>{{ $detail->jumlah }}</td>
            <td class="num">{{ 'Rp ' . number_format($detail->barang->harga_sewa_per_hari, 0, ',', '.') }}</td>
            <td class="num">{{ 'Rp ' . number_format($detail->jumlah * $detail->barang->harga_sewa_per_hari * $transaksi->durasiHari(), 0, ',', '.') }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr class="total">
          <td colspan="3">Total Dibayar (Lunas)</td>
          <td class="num">{{ 'Rp ' . number_format($transaksi->pembayaran->jumlah, 0, ',', '.') }}</td>
        </tr>
      </tfoot>
    </table>

    <div class="paid">SUDAH LUNAS / TUNAI</div>

    <div class="sign">
      <div class="box">
        <div>Petugas</div>
        <div class="name">{{ $transaksi->pembayaran->petugas->nama ?? '................' }}</div>
      </div>
      <div class="box">
        <div>Peminjam</div>
        <div class="name">{{ $transaksi->user->nama ?? '................' }}</div>
      </div>
    </div>

    <div class="footer-note">Barang harus dikembalikan sesuai tanggal jatuh tempo. Keterlambatan dikenakan denda.</div>
  </div>
</body>

</html>
