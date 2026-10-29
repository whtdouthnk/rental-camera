<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;

class PenggunaDashboardController extends Controller
{
    public function index()
    {
        $totalPeminjaman = Transaksi::where('user_id', auth()->id())->count();
        $menunggu = Transaksi::where('user_id', auth()->id())
            ->where('status', 'Menunggu')
            ->count();
        $dipinjam = Transaksi::where('user_id', auth()->id())
            ->where('status', 'Dipinjam')
            ->count();
        $selesai = Transaksi::where('user_id', auth()->id())
            ->where('status', 'Selesai')
            ->count();

        $barangTersedia = Barang::where('status', 'Aktif')
            ->where('stok_tersedia', '>', 0)
            ->count();

        $transaksiTerbaru = Transaksi::with(['detailTransaksi.barang'])
            ->where('user_id', auth()->id())
            ->latest()
            ->limit(8)
            ->get();

        return view('pengguna.dashboard', compact(
            'totalPeminjaman',
            'menunggu',
            'dipinjam',
            'selesai',
            'barangTersedia',
            'transaksiTerbaru'
        ));
    }
}
