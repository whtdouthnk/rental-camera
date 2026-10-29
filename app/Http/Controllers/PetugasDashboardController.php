<?php

namespace App\Http\Controllers;

use App\Models\Pengembalian;
use App\Models\Transaksi;

class PetugasDashboardController extends Controller
{
    public function index()
    {
        $menunggu = Transaksi::where('status', 'Menunggu')->count();
        $disetujui = Transaksi::where('status', 'Disetujui')->count();
        $dipinjam = Transaksi::where('status', 'Dipinjam')->count();
        $selesai = Transaksi::where('status', 'Selesai')->count();

        $totalDenda = Pengembalian::sum('total_denda');

        $transaksiTerbaru = Transaksi::with(['user', 'detailTransaksi.barang'])
            ->latest()
            ->limit(8)
            ->get();

        return view('petugas.dashboard', compact(
            'menunggu',
            'disetujui',
            'dipinjam',
            'selesai',
            'totalDenda',
            'transaksiTerbaru'
        ));
    }
}
