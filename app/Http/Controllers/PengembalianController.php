<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\LogAktivitas;
use App\Models\Pengembalian;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PengembalianController extends Controller
{
    /**
     * Menampilkan daftar pengembalian.
     */
    public function index(Request $request)
    {
        $pengembalians = Pengembalian::with(['transaksi.user', 'transaksi.detailTransaksi.barang'])
            ->latest()
            ->paginate(10);

        return view('pengembalian.index', compact('pengembalians'));
    }

    /**
     * Form proses pengembalian untuk transaksi yang dipinjam.
     */
    public function create(Request $request)
    {
        $transaksi = Transaksi::with(['user', 'detailTransaksi.barang'])
            ->findOrFail($request->transaksi);

        if ($transaksi->status !== 'Dipinjam') {
            return redirect()->route('petugas.pengembalian.index')
                ->with('error', 'Hanya transaksi berstatus Dipinjam yang dapat diproses pengembaliannya');
        }

        if ($transaksi->pengembalian) {
            return redirect()->route('petugas.pengembalian.index')
                ->with('error', 'Transaksi ini sudah memiliki pengembalian');
        }

        $dendaPerHari = $transaksi->detailTransaksi
            ->sum(fn ($d) => $d->barang->denda_per_hari * $d->jumlah);

        $biayaSewa = $transaksi->totalBiayaSewa();

        return view('pengembalian.form', compact('transaksi', 'dendaPerHari', 'biayaSewa'));
    }

    /**
     * Simpan pengembalian dan hitung denda otomatis.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'transaksi_id'   => 'required|exists:transaksi,id',
            'tgl_kembali'    => 'required|date',
            'kondisi_barang' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'catatan'        => 'nullable|string|max:1000'
        ]);

        $transaksi = Transaksi::with(['detailTransaksi.barang'])
            ->findOrFail($data['transaksi_id']);

        if ($transaksi->status !== 'Dipinjam') {
            return back()->with('error', 'Hanya transaksi berstatus Dipinjam yang dapat dikembalikan');
        }

        if ($transaksi->pengembalian) {
            return back()->with('error', 'Transaksi ini sudah memiliki pengembalian');
        }

        if (Carbon::parse($data['tgl_kembali'])->lt(Carbon::parse($transaksi->tgl_pinjam))) {
            return back()
                ->withInput()
                ->withErrors(['tgl_kembali' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam']);
        }

        $jatuhTempo = Carbon::parse($transaksi->tgl_jatuh_tempo);
        $tglKembali = Carbon::parse($data['tgl_kembali']);

        $terlambatHari = $tglKembali->gt($jatuhTempo) ? $jatuhTempo->diffInDays($tglKembali) : 0;

        $dendaPerHari = $transaksi->detailTransaksi
            ->sum(fn ($d) => $d->barang->denda_per_hari * $d->jumlah);

        $totalDenda = $terlambatHari * $dendaPerHari;

        Pengembalian::create([
            'transaksi_id'    => $transaksi->id,
            'tgl_kembali'     => $data['tgl_kembali'],
            'terlambat_hari'  => $terlambatHari,
            'total_denda'     => $totalDenda,
            'kondisi_barang'  => $data['kondisi_barang'],
            'catatan'         => $data['catatan'] ?? null,
            'diterima_oleh'   => auth()->id()
        ]);

        foreach ($transaksi->detailTransaksi as $detail) {
            $barang = Barang::find($detail->barang_id);

            $barang->increment('stok_tersedia', $detail->jumlah);

            if ($data['kondisi_barang'] === 'Rusak Ringan') {
                $barang->update(['kondisi' => 'Rusak Ringan']);
            } elseif ($data['kondisi_barang'] === 'Rusak Berat') {
                $barang->update(['kondisi' => 'Rusak']);
            }
        }

        $transaksi->update(['status' => 'Selesai']);

        LogAktivitas::record('pengembalian', 'Pengembalian', "Pengembalian transaksi {$transaksi->kode_transaksi} selesai diproses, denda Rp " . number_format($totalDenda, 0, ',', '.'));

        return redirect()->route('petugas.pengembalian.index')
            ->with('success', "Pengembalian {$transaksi->kode_transaksi} selesai diproses");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengembalian = Pengembalian::with(['transaksi.user', 'transaksi.detailTransaksi.barang', 'petugas'])
            ->findOrFail($id);

        return view('pengembalian.show', compact('pengembalian'));
    }
}
