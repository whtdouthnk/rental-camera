<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\DetailTransaksi;
use App\Models\LogAktivitas;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Menampilkan daftar transaksi sesuai role.
     */
    public function index(Request $request)
    {
        $query = Transaksi::with(['user', 'detailTransaksi.barang'])
            ->latest();

        if (auth()->user()->role === 'Pengguna') {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transaksis = $query->paginate(10);

        $statuses = ['Menunggu', 'Disetujui', 'Ditolak', 'Dipinjam', 'Selesai', 'Dibatalkan'];

        return view('transaksi.index', compact('transaksis', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barangs = Barang::with('kategori')
            ->where('status', 'Aktif')
            ->where('stok_tersedia', '>', 0)
            ->orderBy('nama_barang')
            ->get();

        return view('transaksi.form', compact('barangs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tgl_pinjam'      => 'required|date',
            'tgl_jatuh_tempo' => 'required|date|after_or_equal:tgl_pinjam',
            'barang_id'       => 'required|array|min:1',
            'barang_id.*'     => 'required|exists:barang,id',
            'jumlah'          => 'required|array|min:1',
            'jumlah.*'        => 'required|integer|min:1',
            'catatan'         => 'nullable|string|max:1000'
        ]);

        $detail = [];

        foreach ($request->barang_id as $i => $barangId) {
            $barangId = (int) $barangId;
            $jumlah = (int) $request->jumlah[$i];

            $detail[$barangId] = ($detail[$barangId] ?? 0) + $jumlah;
        }

        $barangs = Barang::whereIn('id', array_keys($detail))->get()->keyBy('id');

        foreach ($detail as $barangId => $jumlah) {
            $barang = $barangs[$barangId];

            if ($barang->stok_tersedia < $jumlah) {
                return back()
                    ->withInput()
                    ->withErrors(['barang_id' => "Stok {$barang->nama_barang} tidak mencukupi (tersisa {$barang->stok_tersedia})"]);
            }
        }

        $transaksi = Transaksi::create([
            'kode_transaksi'  => $this->generateKodeTransaksi(),
            'user_id'         => auth()->id(),
            'tgl_pinjam'      => $data['tgl_pinjam'],
            'tgl_jatuh_tempo' => $data['tgl_jatuh_tempo'],
            'status'          => 'Menunggu',
            'catatan'         => $data['catatan'] ?? null
        ]);

        foreach ($detail as $barangId => $jumlah) {
            DetailTransaksi::create([
                'transaksi_id' => $transaksi->id,
                'barang_id'    => $barangId,
                'jumlah'       => $jumlah
            ]);
        }

        LogAktivitas::record('pengajuan', 'Transaksi', "Pengajuan peminjaman {$transaksi->kode_transaksi} diajukan");

        return redirect()->route('user.transaksi.index')
            ->with('success', 'Peminjaman berhasil diajukan, menunggu persetujuan petugas');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaksi = Transaksi::with(['user', 'petugas', 'detailTransaksi.barang', 'pengembalian', 'pembayaran.petugas'])
            ->findOrFail($id);

        if (auth()->user()->role === 'Pengguna' && $transaksi->user_id !== auth()->id()) {
            abort(403);
        }

        return view('transaksi.show', compact('transaksi'));
    }

    /**
     * Petugas menyetujui pengajuan peminjaman.
     */
    public function approve(string $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->status !== 'Menunggu') {
            return back()->with('error', 'Transaksi tidak dalam status Menunggu');
        }

        $transaksi->update([
            'status'        => 'Disetujui',
            'disetujui_oleh' => auth()->id()
        ]);

        LogAktivitas::record('approve', 'Transaksi', "Transaksi {$transaksi->kode_transaksi} disetujui");

        return back()->with('success', "Transaksi {$transaksi->kode_transaksi} disetujui");
    }

    /**
     * Petugas menolak pengajuan peminjaman.
     */
    public function reject(Request $request, string $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->status !== 'Menunggu') {
            return back()->with('error', 'Transaksi tidak dalam status Menunggu');
        }

        $request->validate([
            'catatan' => 'nullable|string|max:1000'
        ]);

        $transaksi->update([
            'status'        => 'Ditolak',
            'disetujui_oleh' => auth()->id(),
            'catatan'       => $request->catatan ?: $transaksi->catatan
        ]);

        LogAktivitas::record('reject', 'Transaksi', "Transaksi {$transaksi->kode_transaksi} ditolak");

        return back()->with('success', "Transaksi {$transaksi->kode_transaksi} ditolak");
    }

    /**
     * Petugas mengonfirmasi barang sudah diserahkan (status Dipinjam).
     */
    public function serah(string $id)
    {
        $transaksi = Transaksi::with(['detailTransaksi.barang', 'user'])
            ->findOrFail($id);

        if ($transaksi->status !== 'Disetujui') {
            return back()->with('error', 'Transaksi harus disetujui terlebih dahulu');
        }

        foreach ($transaksi->detailTransaksi as $detail) {
            $barang = Barang::find($detail->barang_id);

            if ($barang->stok_tersedia < $detail->jumlah) {
                return back()->with('error', "Stok {$barang->nama_barang} tidak mencukupi");
            }
        }

        foreach ($transaksi->detailTransaksi as $detail) {
            $barang = Barang::find($detail->barang_id);
            $barang->decrement('stok_tersedia', $detail->jumlah);
        }

        $transaksi->update(['status' => 'Dipinjam']);

        Pembayaran::create([
            'transaksi_id'  => $transaksi->id,
            'jumlah'        => $transaksi->totalBiayaSewa(),
            'metode'        => 'Tunai',
            'dibayar_pada'  => now(),
            'dibayar_oleh'  => auth()->id()
        ]);

        LogAktivitas::record('serah', 'Transaksi', "Barang transaksi {$transaksi->kode_transaksi} diserahkan dan pembayaran tunai dicatat");

        return back()->with('success', "Barang untuk {$transaksi->kode_transaksi} sudah diserahkan dan pembayaran tunai dicatat");
    }

    /**
     * Petugas mencetak bukti pembayaran tunai untuk pengguna.
     */
    public function buktiPembayaran(string $id)
    {
        $transaksi = Transaksi::with(['user', 'petugas', 'detailTransaksi.barang', 'pembayaran.petugas'])
            ->findOrFail($id);

        if (!in_array($transaksi->status, ['Dipinjam', 'Selesai']) || !$transaksi->pembayaran) {
            abort(404);
        }

        return view('transaksi.bukti', compact('transaksi'));
    }

    /**
     * Pengguna membatalkan pengajuan yang masih menunggu.
     */
    public function cancel(string $id)
    {
        $transaksi = Transaksi::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($transaksi->status !== 'Menunggu') {
            return back()->with('error', 'Hanya pengajuan berstatus Menunggu yang bisa dibatalkan');
        }

        $transaksi->update(['status' => 'Dibatalkan']);

        LogAktivitas::record('cancel', 'Transaksi', "Pengajuan {$transaksi->kode_transaksi} dibatalkan");

        return back()->with('success', 'Pengajuan peminjaman dibatalkan');
    }

    private function generateKodeTransaksi(): string
    {
        $tanggal = now()->format('Ymd');
        $prefix = 'TRX-' . $tanggal . '-';
        $urutan = Transaksi::where('kode_transaksi', 'like', $prefix . '%')->count() + 1;

        do {
            $kode = $prefix . str_pad($urutan, 3, '0', STR_PAD_LEFT);
            $urutan++;
        } while (Transaksi::where('kode_transaksi', $kode)->exists());

        return $kode;
    }
}
